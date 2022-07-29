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


class CalderaForms extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars:: CALDERA;
        $this->_typePhoneTag = 'mo_caldera_phone_enable';
        $this->_typeEmailTag = 'mo_caldera_email_enable';
        $this->_formKey = 'CALDERA';
        $this->_formName = mo_('Caldera Forms');
        $this->_isFormEnabled = get_mo_option('caldera_enable');
        $this->_buttonText = get_mo_option('caldera_button_text');
        $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_("Click Here to send OTP");
        $this->_phoneFormId = array();
        $this->_formDocuments = MoOTPDocs::CALDERA_FORMS_LINK;
        $this->_generateOTPAction = 'miniorange_caldera_generate_otp';
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('caldera_enable_type');
        $this->_formDetails = maybe_unserialize(get_mo_option('caldera_forms'));
        if(empty($this->_formDetails)) return;
        
        foreach ($this->_formDetails as $key => $value) {
            array_push($this->_phoneFormId,'input[name='.$value["phonekey"]);
            add_filter( 'caldera_forms_validate_field_'.$value["phonekey"], [$this,'validateForm'],99,3);
            add_filter( 'caldera_forms_validate_field_'.$value["emailkey"], [$this,'validateForm'],99,3);
            add_filter( 'caldera_forms_validate_field_'.$value["verifyKey"], [$this,'validateForm'],99,3);
            add_action("caldera_forms_submit_complete",[$this,'unsetOTPSessionVariables'],99);

        }
        add_action("wp_ajax_{$this->_generateOTPAction}", [$this,'_send_otp']);
        add_action("wp_ajax_nopriv_{$this->_generateOTPAction}", [$this,'_send_otp']);
        add_action('wp_enqueue_scripts',array($this, 'miniorange_register_caldera_script'));
    }

    
    function unsetSessionVariable()
    {
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType())) {
            $this->unsetOTPSessionVariables();
        }
    }

    
    function miniorange_register_caldera_script()
    {
        wp_register_script( 'mocaldera', MOV_URL . 'includes/js/caldera.min.js',array('jquery') );
        wp_localize_script( 'mocaldera', 'mocaldera', array(
            'siteURL' 		=> wp_ajax_url(),
            'otpType'  		=> $this->_otpType,
            'formkey'       => strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? 'phonekey' : 'emailkey',
            'nonce'         => wp_create_nonce($this->_nonce),
            'buttontext'    => mo_($this->_buttonText),
            'imgURL'        => MOV_LOADER_URL,
            'forms'         => $this->_formDetails,
            'generateURL'   => $this->_generateOTPAction,
        ));
        wp_enqueue_script( 'mocaldera' );
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
            $this->setSessionAndStartOTPVerification($data['user_email'],$data['user_email'],NULL,VerificationType::EMAIL);
    }


    
    private function _processPhoneAndStartOTPVerificationProcess($data)
    {
        if(!MoUtility::sanitizeCheck('user_phone',$data))
            wp_send_json( MoUtility::createJson(MoMessages::showMessage(MoMessages::ENTER_PHONE),MoConstants::ERROR_JSON_TYPE) );
        else
            $this->setSessionAndStartOTPVerification(trim($data['user_phone']),NULL,trim($data['user_phone']),VerificationType::PHONE);
    }


    
    private function setSessionAndStartOTPVerification($sessionValue, $userEmail, $phoneNumber, $_otpType)
    {
        SessionUtils::addEmailOrPhoneVerified($this->_formSessionVar,$sessionValue,$_otpType);
        $this->sendChallenge('',$userEmail,NULL,$phoneNumber,$_otpType);
    }


    
    public function validateForm($entry, $field, $form)
    {
        if(is_wp_error( $entry ) ) return $entry;
        $id = $form['ID'];
        if(!array_key_exists($id,$this->_formDetails)) return $entry;
        $formData = $this->_formDetails[$id];

        $entry = $this->checkIfOtpVerificationStarted($entry);
         
        if(is_wp_error($entry)) return $entry;
        if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0 && strcasecmp($field['ID'],$formData['emailkey'])==0) {
            $entry = $this->processEmail($entry);
        }elseif(strcasecmp($this->_otpType,$this->_typePhoneTag)==0 && strcasecmp($field['ID'],$formData['phonekey'])==0) {
            $entry = $this->processPhone($entry);
            
        }elseif(strcasecmp($field['ID'],$formData['verifyKey'])==0) {
            $entry = $this->processOTPEntered($entry);
        }
        return $entry;
    }


    
    function processOTPEntered($entry)
    {
        $otpVerType = $this->getVerificationType();
        $this->validateChallenge($otpVerType,NULL,$entry);
        if(!SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpVerType))
            $entry = new WP_Error('INVALID_OTP',MoUtility::_get_invalid_otp_method());
        return $entry;
    }


    
    function checkIfOtpVerificationStarted($entry)
    {
        return SessionUtils::isOTPInitialized($this->_formSessionVar) ? $entry
            : new WP_Error('ENTER_VERIFY_CODE', MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE));
    }


    
    function processEmail($entry)
    {
        return SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,$entry) ? $entry :
            new WP_Error('EMAIL_MISMATCH',MoMessages::showMessage(MoMessages::EMAIL_MISMATCH));
    }


    
    function processPhone($entry)
    {
        return SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,$entry) ? $entry :
            new WP_Error('PHONE_MISMATCH',MoMessages::showMessage(MoMessages::PHONE_MISMATCH));
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

        $this->_isFormEnabled = $this->sanitizeFormPOST('caldera_enable');
        $this->_otpType = $this->sanitizeFormPOST('caldera_enable_type');
        $this->_buttonText = $this->sanitizeFormPOST('caldera_button_text');

        $form = $this->parseFormDetails();

        $this->_formDetails = !empty($form) ? $form : "";

        update_mo_option('caldera_enable',$this->_isFormEnabled);
        update_mo_option('caldera_enable_type',$this->_otpType);
        update_mo_option('caldera_button_text',$this->_buttonText);
        update_mo_option('caldera_forms',maybe_serialize($this->_formDetails));
    }


    
    function parseFormDetails()
    {
        $form = [];
        
        if(!array_key_exists('caldera_form',$_POST) || !$this->_isFormEnabled) return $form;
        foreach (array_filter($_POST['caldera_form']['form']) as $key => $value)
        {
            $key = sanitize_text_field($key);
            $form[sanitize_text_field($value)]= array(
                'emailkey'=> sanitize_text_field($_POST['caldera_form']['emailkey'][$key]),
                'phonekey'=> sanitize_text_field($_POST['caldera_form']['phonekey'][$key]),
                'verifyKey'=> sanitize_text_field($_POST['caldera_form']['verifyKey'][$key]),
                'phone_show'=> sanitize_text_field($_POST['caldera_form']['phonekey'][$key]),
                'email_show'=> sanitize_text_field($_POST['caldera_form']['emailkey'][$key]),
                'verify_show'=> sanitize_text_field($_POST['caldera_form']['verifyKey'][$key])
            );
        }
        return $form;
    }

}