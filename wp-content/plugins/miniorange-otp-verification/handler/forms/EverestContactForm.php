<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;
use WP_Error;


class EverestContactForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars:: EVEREST_CONTACT;
        $this->_typePhoneTag = 'mo_everest_contact_phone_enable';
        $this->_typeEmailTag = 'mo_everest_contact_email_enable';
        $this->_formKey = 'EVEREST_CONTACT';
        $this->_formName = mo_('Everest Contact Form');
        $this->_isFormEnabled = get_mo_option('everest_contact_enable');
        $this->_phoneFormId = array();
        $this->_formDocuments = MoOTPDocs::EVEREST_CONTACT_FORM_LINK;
        $this->_generateOTPAction = 'miniorange_everest_contact_generate_otp';
        $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_("Click Here to send OTP");
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('everest_contact_enable_type');
        $this->_formDetails = maybe_unserialize(get_mo_option('everest_contact_forms'));
        $this->_buttonText = get_mo_option('everest_contact_button_text');
        if(empty($this->_formDetails)) return;
        foreach ($this->_formDetails as $key => $value) {
            array_push($this->_phoneFormId,'#evf-'.$key.'-field_'.$value["phonekey"]);
        }
        add_filter( 'everest_forms_process_initial_errors',[$this,'validateForm'],99,2 );
        add_filter( 'everest_forms_process_after_filter', [$this,'unsetSessionVariable'],99,3);
        
        add_action("wp_ajax_{$this->_generateOTPAction}", [$this,'_send_otp']);
        add_action("wp_ajax_nopriv_{$this->_generateOTPAction}", [$this,'_send_otp']);
        add_action('wp_enqueue_scripts',array($this, 'miniorange_register_everest_contact_script'));
    }

    
    function unsetSessionVariable($form_fields,$entry,$form_data)
    {
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType())) {
            $this->unsetOTPSessionVariables();
        }
        return $form_fields;
    }

    
    function miniorange_register_everest_contact_script()
    {
        wp_register_script( 'moeverestcontact', MOV_URL . 'includes/js/moeverestcontact.min.js',array('jquery') );
        wp_localize_script( 'moeverestcontact', 'moeverestcontact', array(
            'siteURL' 		=> wp_ajax_url(),
            'otpType'  		=> $this->_otpType,
            'formkey'       => strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? 'phonekey' : 'emailkey',
            'nonce'         => wp_create_nonce($this->_nonce),
            'buttontext'    => mo_($this->_buttonText),
            'imgURL'        => MOV_LOADER_URL,
            'forms'         => $this->_formDetails,
            'generateURL'   => $this->_generateOTPAction,
        ));
        wp_enqueue_script( 'moeverestcontact' );
    }

    
    function _send_otp()
    {
        $data = $_POST;

        $this->validateAjaxRequest();
        MoUtility::initialize_transaction($this->_formSessionVar);
        if($this->_otpType==$this->_typePhoneTag)
            $this->_processPhoneAndStartOTPVerificationProcess($data);
        else
            $this->_processEmailAndStartOTPVerificationProcess($data);
    }


    
    private function _processEmailAndStartOTPVerificationProcess($data)
    {
        if(!MoUtility::sanitizeCheck('user_email',$data))
            wp_send_json( MoUtility::createJson(MoMessages::showMessage(MoMessages::ENTER_EMAIL),MoConstants::ERROR_JSON_TYPE) );
        else
            $this->setSessionAndStartOTPVerification(sanitize_email($data['user_email']),sanitize_email($data['user_email']),NULL,VerificationType::EMAIL);
    }


    
    private function _processPhoneAndStartOTPVerificationProcess($data)
    {
        if(!MoUtility::sanitizeCheck('user_phone',$data))
            wp_send_json( MoUtility::createJson(MoMessages::showMessage(MoMessages::ENTER_PHONE),MoConstants::ERROR_JSON_TYPE) );
        else
            $this->setSessionAndStartOTPVerification(trim(sanitize_text_field($data['user_phone'])),NULL,trim(sanitize_text_field($data['user_phone'])),VerificationType::PHONE);
    }


    
    private function setSessionAndStartOTPVerification($sessionValue, $userEmail, $phoneNumber, $_otpType)
    {
        SessionUtils::addEmailOrPhoneVerified($this->_formSessionVar,$sessionValue,$_otpType);
        $this->sendChallenge('',$userEmail,NULL,$phoneNumber,$_otpType);
    }


    
    public function validateForm($errors, $form_data)
    {
        $id = $form_data['id'];
        if(!empty($errors[ $id ]['header'])) return $errors;
        if(!array_key_exists($id,$this->_formDetails)) return $errors;

        $formData = $this->_formDetails[$id];
        $data = $_POST;

        $errors = $this->checkIfOtpVerificationStarted($errors,$data);

         
        if(!empty($errors[ $id ]['header'])) return $errors;

        if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0 && strcasecmp($form_data['form_fields'][$formData['emailkey']]['id'],$formData['emailkey'])==0) {
            $errors = $this->processEmail($data,$errors,$formData);
        }elseif(strcasecmp($this->_otpType,$this->_typePhoneTag)==0 && strcasecmp($form_data['form_fields'][$formData['phonekey']]['id'],$formData['phonekey'])==0) {
            $errors = $this->processPhone($data,$errors,$formData);
        }
        
        if(is_wp_error($errors)) return $errors;
        if(empty($errors) && strcasecmp($form_data['form_fields'][$formData['verifyKey']]['id'],$formData['verifyKey'])==0) {
            $errors = $this->processOTPEntered($data,$errors,$formData);
        }
        return $errors;
    }


    
    function processOTPEntered($data,$errors,$formData)
    {
        $id=$data['everest_forms']['id'];
        $otpVerType = $this->getVerificationType();
        $this->validateChallenge($otpVerType,NULL,$data['everest_forms']['form_fields'][$formData['verifyKey']]);
        if(!SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpVerType))
            $errors[ $id ]['header'] = MoUtility::_get_invalid_otp_method();
        return $errors;
    }


    
    function checkIfOtpVerificationStarted($errors,$data)
    {
        $id=$data['everest_forms']['id'];
        if(!(SessionUtils::isOTPInitialized($this->_formSessionVar)))
            $errors[ $id ]['header'] = MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE);
        return $errors;
    }


    
    function processEmail($data,$errors,$formData)
    {
        $id=sanitize_text_field($data['everest_forms']['id']);
        if(!SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,$data['everest_forms']['form_fields'][$formData['emailkey']])){
            $errors[ $form_id ]['header'] = MoMessages::showMessage(MoMessages::EMAIL_MISMATCH);
        }
        return $errors;
    }


    
    function processPhone($data,$errors,$formData)
    {
        $id=sanitize_text_field($data['everest_forms']['id']);
        if(!SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,$data['everest_forms']['form_fields'][$formData['phonekey']])){
            $errors[ $id ]['header'] = MoMessages::showMessage(MoMessages::PHONE_MISMATCH);
        }
        return $errors;
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VERIFICATION_FAILED,$otpType);
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_formSessionVar,$this->_txSessionId]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && $this->_otpType==$this->_typePhoneTag)
            $selector = array_merge($selector, $this->_phoneFormId);
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('everest_contact_enable');
        $this->_otpType = $this->sanitizeFormPOST('everest_contact_enable_type');
        $this->_buttonText = $this->sanitizeFormPOST('everest_contact_button_text');

        $form = $this->parseFormDetails();

        $this->_formDetails = !empty($form) ? $form : "";

        update_mo_option('everest_contact_enable',$this->_isFormEnabled);
        update_mo_option('everest_contact_enable_type',$this->_otpType);
        update_mo_option('everest_contact_button_text',$this->_buttonText);
        update_mo_option('everest_contact_forms',maybe_serialize($this->_formDetails));
    }


    
    function parseFormDetails()
    {
        $form = [];
        
        if(!array_key_exists('everest_contact_form',$_POST) || !$this->_isFormEnabled) return $form;
        foreach (array_filter($_POST['everest_contact_form']['form']) as $key => $value)
        {
            $form[sanitize_text_field($value)]= array(
                'emailkey'=> sanitize_text_field($_POST['everest_contact_form']['emailkey'][$key]),
                'phonekey'=> sanitize_text_field($_POST['everest_contact_form']['phonekey'][$key]),
                'verifyKey'=> sanitize_text_field($_POST['everest_contact_form']['verifyKey'][$key]),
                'phone_show'=>sanitize_text_field($_POST['everest_contact_form']['phonekey'][$key]),
                'email_show'=>sanitize_text_field($_POST['everest_contact_form']['emailkey'][$key]),
                'verify_show'=>sanitize_text_field($_POST['everest_contact_form']['verifyKey'][$key])
            );
        }
        return $form;
    }

}