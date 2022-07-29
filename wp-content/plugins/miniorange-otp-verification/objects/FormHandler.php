<?php

namespace OTP\Objects;

use OTP\Helper\FormList;
use OTP\Helper\FormSessionVars;
use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;


class FormHandler
{
    
    protected $_typePhoneTag;

    
    protected $_typeEmailTag;

    
    protected $_typeBothTag;

    
    protected $_formKey;

    
    protected $_formName;

    
    protected $_otpType;

    
    protected $_phoneFormId;

    
    protected $_isFormEnabled;

    
    protected $_restrictDuplicates;

    
    protected $_byPassLogin;

    
    protected $_isLoginOrSocialForm;

    
    protected $_isAjaxForm;

    
    protected $_phoneKey;

    
    protected $_emailKey;

    
    protected $_buttonText;

    
    protected $_formDetails;

    
    protected $_disableAutoActivate;

    
    protected $_formSessionVar;

    
    protected $_formSessionVar2;

    
    protected $_nonce = 'form_nonce';

    
    protected $_txSessionId  = FormSessionVars::TX_SESSION_ID;

    
    protected $_formOption   = "mo_customer_validation_settings";

    
    protected $_generateOTPAction;

    
    protected $_validateOTPAction;

    
    protected $_nonceKey = 'security';

    
    protected $_isAddOnForm = FALSE;

    
    protected $_formDocuments = [];

    const VALIDATED = "VALIDATED";
    const VERIFICATION_FAILED = "verification_failed";
    const VALIDATION_CHECKED = "validationChecked";

    protected function __construct()
    {
        
        add_action( 'admin_init', array($this,'handleFormOptions') , 2 );

        
        if(!$this->isFormEnabled()) return;

        
        add_action(	'init', array($this,'handleForm') ,1 );

        
        add_filter( 'mo_phone_dropdown_selector', array($this,'getPhoneNumberSelector'),1,1);


        
        if(SessionUtils::isOTPInitialized($this->_formSessionVar)
            || SessionUtils::isOTPInitialized($this->_formSessionVar2)) {
            
            add_action('otp_verification_successful', array($this, 'handle_post_verification'), 1, 7);

            
            add_action('otp_verification_failed', array($this, 'handle_failed_verification'), 1, 4);

            
            add_action('unset_session_variable', array($this, 'unsetOTPSessionVariables'), 1, 0);
        }

        
        add_filter( 'is_ajax_form', array($this,'is_ajax_form_in_play'), 1,1);

        
        add_filter( 'is_login_or_social_form', array($this,'isLoginOrSocialForm'),1,1);

        
        $handlerList = FormList::instance();
        $handlerList->add($this->getFormKey(),$this);
    }

    
    public function isLoginOrSocialForm($isLoginOrSocialForm)
    {
        return SessionUtils::isOTPInitialized($this->_formSessionVar) ? $this->getisLoginOrSocialForm() : $isLoginOrSocialForm;
    }


    
    public function is_ajax_form_in_play($isAjax)
    {
        return SessionUtils::isOTPInitialized($this->_formSessionVar) ? $this->_isAjaxForm : $isAjax;
    }


    
    public function sanitizeFormPOST($param,$prefix=null)
    {
        $param = ($prefix===null ? "mo_customer_validation_" : "") . $param;
        return MoUtility::sanitizeCheck($param,$_POST);
    }


    
    public function sendChallenge($user_login, $user_email, $errors, $phone_number = null,
                                  $otp_type="email", $password = "", $extra_data = null, $from_both = false)
    {
        do_action('mo_generate_otp',$user_login, $user_email, $errors, $phone_number,
            $otp_type, $password, $extra_data, $from_both);
    }


    
    public function validateChallenge($otpType, $reqVar = 'mo_otp_token', $otpToken = NULL)
    {
        do_action('mo_validate_otp',$otpType, $reqVar,$otpToken);
    }


    
    public function basicValidationCheck($message)
    {
                if($this->isFormEnabled() && MoUtility::isBlank($this->_otpType)) {
            do_action('mo_registration_show_message', MoMessages::showMessage($message), MoConstants::ERROR);
            return false;
        }
        return true;
    }

    
    public function getVerificationType()
    {
        $map = [
            $this->_typePhoneTag => VerificationType::PHONE,
            $this->_typeEmailTag => VerificationType::EMAIL,
            $this->_typeBothTag => VerificationType::BOTH,
        ];
        return MoUtility::isBlank($this->_otpType) ? false : $map[$this->_otpType];
    }

    
    protected function validateAjaxRequest()
    {
        if(!check_ajax_referer($this->_nonce,$this->_nonceKey)) {
            wp_send_json(
                MoUtility::createJson(
                    MoMessages::showMessage(BaseMessages::INVALID_OP),
                    MoConstants::ERROR_JSON_TYPE
                )
            );
            exit;
        }
    }

    
    protected function ajaxProcessingFields()
    {
        $map = [
            $this->_typePhoneTag => [VerificationType::PHONE],
            $this->_typeEmailTag => [VerificationType::EMAIL],
            $this->_typeBothTag => [VerificationType::PHONE,VerificationType::EMAIL],
        ];
        return $map[$this->_otpType];
    }

    

    public function getPhoneHTMLTag(){ return $this->_typePhoneTag; }

    public function getEmailHTMLTag(){ return $this->_typeEmailTag; }

    public function getBothHTMLTag(){ return $this->_typeBothTag; }

    public function getFormKey(){ return $this->_formKey; }

    public function getFormName(){ return $this->_formName; }

    public function getOtpTypeEnabled(){ return $this->_otpType; }

    
    public function disableAutoActivation(){ return $this->_disableAutoActivate; }

    public function getPhoneKeyDetails(){ return $this->_phoneKey; }

    public function getEmailKeyDetails(){ return $this->_emailKey; }

    
    public function isFormEnabled(){ return $this->_isFormEnabled; }

    public function getButtonText(){ return mo_($this->_buttonText); }

    public function getFormDetails(){ return $this->_formDetails; }

    
    public function restrictDuplicates(){ return $this->_restrictDuplicates; }

    
    public function bypassForLoggedInUsers(){ return $this->_byPassLogin; }

    
    public function getisLoginOrSocialForm(){ return (bool) $this->_isLoginOrSocialForm; }

    public function getFormOption() { return $this->_formOption; }

    
    public function isAjaxForm() { return $this->_isAjaxForm; }

    
    public function isAddOnForm(){ return $this->_isAddOnForm; }

    
    public function getFormDocuments(){ return $this->_formDocuments; }
}