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


class WPFormsPlugin extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::WPFORM;
        $this->_phoneFormId = array();
        $this->_formKey = 'WPFORMS';
        $this->_typePhoneTag = "mo_wpform_phone_enable";
        $this->_typeEmailTag = "mo_wpform_email_enable";
        $this->_typeBothTag  = "mo_wpform_both_enable";
        $this->_formName = mo_("WPForms");
        $this->_isFormEnabled = get_mo_option('wpform_enable');
        $this->_buttonText = get_mo_option('wpforms_button_text');
        $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_("Send OTP");
        $this->_generateOTPAction = "miniorange-wpform-send-otp";
        $this->_validateOTPAction = "miniorange-wpform-verify-code";
        $this->_formDocuments = MoOTPDocs::WP_FORMS_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('wpform_enable_type');
        $this->_formDetails = maybe_unserialize(get_mo_option('wpform_forms'));
        if(empty($this->_formDetails)) return;
        
        if($this->_otpType===$this->_typePhoneTag || $this->_otpType === $this->_typeBothTag) {
            foreach ($this->_formDetails as $key => $value) {
                array_push($this->_phoneFormId,'#wpforms-'.$key.'-field_'.$value["phonekey"]);
            }
        }
        
        add_filter('wpforms_process_initial_errors',array($this,'validateForm'),1,2);
        add_action('wp_enqueue_scripts', array($this, 'mo_enqueue_wpforms'));

        
        add_action("wp_ajax_{$this->_generateOTPAction}", [$this,'_send_otp']);
        add_action("wp_ajax_nopriv_{$this->_generateOTPAction}", [$this,'_send_otp']);
        add_action("wp_ajax_{$this->_validateOTPAction}", [$this,'processFormAndValidateOTP']);
        add_action("wp_ajax_nopriv_{$this->_validateOTPAction}", [$this,'processFormAndValidateOTP']);
    }

    
    function mo_enqueue_wpforms()
    {
        wp_register_script( 'mowpforms', MOV_URL . 'includes/js/mowpforms.min.js',array('jquery') );
        wp_localize_script( 'mowpforms', 'mowpforms', array(
            'siteURL' 		=> 	wp_ajax_url(),
            'otpType'       =>  $this->ajaxProcessingFields(),
            'formDetails'   =>  $this->_formDetails,
            'buttontext'    =>  $this->_buttonText,
            'validated'     =>  $this->getSessionDetails(),
            'imgURL'        =>  MOV_LOADER_URL,
            'fieldText'     =>  mo_('Enter OTP here'),
            'gnonce'        =>  wp_create_nonce($this->_nonce),
            'nonceKey'      =>  wp_create_nonce($this->_nonceKey),
            'vnonce'        =>  wp_create_nonce($this->_nonce),
            'gaction'       =>  $this->_generateOTPAction,
            'vaction'       =>  $this->_validateOTPAction
        ) );
        wp_enqueue_script( 'mowpforms' );
    }


    function getSessionDetails()
    {
        return [
            VerificationType::EMAIL => SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,VerificationType::EMAIL),
            VerificationType::PHONE => SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,VerificationType::PHONE)
        ];
    }

    
    function _send_otp()
    {

        MoUtility::initialize_transaction($this->_formSessionVar);
        if('mo_wpform_'.sanitize_text_field($_POST['otpType']).'_enable'===$this->_typePhoneTag)
            $this->_processPhoneAndSendOTP($_POST);
        else
            $this->_processEmailAndSendOTP($_POST);
    }


    
    private function _processEmailAndSendOTP($data)
    {
        if(!MoUtility::sanitizeCheck('user_email',$data)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $user_email = sanitize_email($data['user_email']);
            SessionUtils::addEmailVerified($this->_formSessionVar,$user_email);
            $this->sendChallenge('',$user_email,NULL,NULL,VerificationType::EMAIL);
        }
    }


    
    private function _processPhoneAndSendOTP($data)
    {
        if(!MoUtility::sanitizeCheck('user_phone',$data)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $user_phone = sanitize_text_field($data['user_phone']);
            SessionUtils::addPhoneVerified($this->_formSessionVar,$user_phone);
            $this->sendChallenge('',NULL,NULL,$user_phone,VerificationType::PHONE);
        }
    }

    function processFormAndValidateOTP()
    {
        $this->validateAjaxRequest();
        $this->checkIfOTPSent();
        $this->checkIntegrityAndValidateOTP($_POST);
    }

    function checkIfOTPSent()
    {
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }

    private function checkIntegrityAndValidateOTP($data)
    {

        $this->checkIntegrity($data);
        $this->validateChallenge(sanitize_text_field($data['otpType']),NULL,sanitize_text_field($data['otp_token']));

        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,sanitize_text_field($data['otpType']))) {
            wp_send_json(MoUtility::createJson(
                    MoConstants::SUCCESS_JSON_TYPE, MoConstants::SUCCESS_JSON_TYPE
            ));
        }else{
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::INVALID_OTP), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }

    private function checkIntegrity($data)
    {
        if($data['otpType']==='phone' ) {
            if(!SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,sanitize_text_field($data['user_phone']))) {
                wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::PHONE_MISMATCH), MoConstants::ERROR_JSON_TYPE
                ));
            }
        } else if(!SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,sanitize_email($data['user_email']))) {
            wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::EMAIL_MISMATCH), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }

    
    public function validateForm($errors,$formData)
    {

        $id = $formData['id'];
        if(!array_key_exists($id,$this->_formDetails)) return $errors;
        $formData = $this->_formDetails[$id];
        if(!empty($errors)) return $errors;
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) {
            $field_id =$this->_otpType===$this->_typeEmailTag? $formData['emailkey']:$formData['phonekey'];
            $errors[$id][$field_id]=MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE);
            return $errors;
        }
        if($this->_otpType===$this->_typeEmailTag|| $this->_otpType===$this->_typeBothTag)
            $errors = $this->processEmail($formData,$errors,$id);
        if($this->_otpType===$this->_typePhoneTag || $this->_otpType===$this->_typeBothTag)
            $errors = $this->processPhone($formData,$errors,$id);
        if(empty($errors))
            $this->unsetOTPSessionVariables();
        return $errors;
    }

    
    function processEmail($formData, $errors, $id)
    {
        $field_id = $formData['emailkey'];
        if(!SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,VerificationType::EMAIL))
            $errors[$id][$field_id]=MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE);
        if(!SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,sanitize_text_field($_POST['wpforms']['fields'][$field_id])))
            $errors[$id][$field_id]=MoMessages::showMessage(MoMessages::EMAIL_MISMATCH);
        return $errors;
    }


    
    function processPhone($formData, $errors, $id)
    {
        $field_id = $formData['phonekey'];
        if(!SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,VerificationType::PHONE))
            $errors[$id][$field_id]=MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE);
        if(!SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,sanitize_text_field($_POST['wpforms']['fields'][$field_id])))
            $errors[$id][$field_id]=MoMessages::showMessage(MoMessages::PHONE_MISMATCH);
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
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->_isFormEnabled
            && ($this->_otpType===$this->_typePhoneTag || $this->_otpType === $this->_typeBothTag)) {
            $selector = array_merge($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;
        

        $form = $this->parseFormDetails();

        $this->_isFormEnabled = $this->sanitizeFormPOST('wpform_enable');
        $this->_otpType = $this->sanitizeFormPOST('wpform_enable_type');
        $this->_buttonText = $this->sanitizeFormPOST('wpforms_button_text');
        $this->_formDetails = !empty($form) ? $form : "";

        update_mo_option('wpform_enable', $this->_isFormEnabled);
        update_mo_option('wpform_enable_type',$this->_otpType);
        update_mo_option('wpforms_button_text',$this->_buttonText);
        update_mo_option('wpform_forms', maybe_serialize($this->_formDetails));
    }

    function parseFormDetails()
    {
        $form = [];
        if(!array_key_exists('wpform_form',$_POST)) return $form;
        foreach (array_filter($_POST['wpform_form']['form']) as $key => $value)
        {
            $formData = $this->getFormDataFromID($value);
            if(MoUtility::isBlank($formData)) continue;
            $fieldIds = $this->getFieldIDs($_POST,$key,$formData);
            $form[sanitize_text_field($value)]= array(
                'emailkey'=> $fieldIds['emailKey'],
                'phonekey'=> $fieldIds['phoneKey'],
                'phone_show'=> sanitize_text_field($_POST['wpform_form']['phonekey'][$key]),
                'email_show'=> sanitize_text_field($_POST['wpform_form']['emailkey'][$key])
            );
        }
        return $form;
    }

    
    private function getFormDataFromID($id)
    {
        if(Moutility::isBlank($id)) return '';
        $form = get_post( absint( $id ) );
        if(MoUtility::isBlank($id)) return '';
        return wp_unslash( json_decode($form->post_content));
    }


    
    private function getFieldIDs($data,$key,$formData)
    {
        $fieldIds = array('emailKey'=>'','phoneKey'=>'');
        if(empty($data)) return $fieldIds;
        foreach($formData->fields as $field) {
            if(!property_exists($field,'label')) continue;
            if(strcasecmp($field->label,$data['wpform_form']['emailkey'][$key])===0) $fieldIds['emailKey']=$field->id;
            if(strcasecmp($field->label,$data['wpform_form']['phonekey'][$key])===0) $fieldIds['phoneKey']=$field->id;
        }
        return $fieldIds;
    }
}