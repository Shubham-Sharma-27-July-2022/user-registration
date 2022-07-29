<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;
use \WP_Error;


class WpEmemberForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::EMEMBER;
        $this->_typePhoneTag = 'mo_emember_phone_enable';
        $this->_typeEmailTag = 'mo_emember_email_enable';
        $this->_typeBothTag = 'mo_emember_both_enable';
        $this->_formKey = 'WP_EMEMBER';
        $this->_formName = mo_('WP eMember');
        $this->_isFormEnabled = get_mo_option('emember_default_enable');
        $this->_phoneKey = 'wp_emember_phone';
        $this->_phoneFormId = 'input[name='.$this->_phoneKey.']';
        $this->_formDocuments = MoOTPDocs::EMEMBER_FORM_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('emember_enable_type');
        if(array_key_exists('emember_dsc_nonce',$_POST) && !array_key_exists('option',$_POST))
            $this->miniorange_emember_user_registration();
    }


    
    function isPhoneVerificationEnabled()
    {
        $otpType = $this->getVerificationType();
        return $otpType===VerificationType::PHONE || $otpType===VerificationType::BOTH;
    }


    
    function miniorange_emember_user_registration()
    {

        if($this->validatePostFields())
        {
            $phone = array_key_exists($this->_phoneKey,$_POST) ? sanitize_text_field($_POST[$this->_phoneKey]) : NULL;
            $this->startTheOTPVerificationProcess(sanitize_text_field($_POST['wp_emember_user_name']),sanitize_email($_POST['wp_emember_email']),$phone);
        }
    }


    
    function startTheOTPVerificationProcess($username, $userEmail, $phone)
    {
        MoUtility::initialize_transaction($this->_formSessionVar);
        $errors = new WP_Error();
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0)
            $this->sendChallenge( $username,$userEmail,$errors,$phone,VerificationType::PHONE);
        else if(strcasecmp($this->_otpType,$this->_typeBothTag)===0)
            $this->sendChallenge( $username,$userEmail,$errors,$phone,VerificationType::BOTH);
        else
            $this->sendChallenge( $username,$userEmail,$errors,$phone,VerificationType::EMAIL);
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        $otpVerType = $this->getVerificationType();
        $fromBoth = $otpVerType===VerificationType::BOTH ? TRUE : FALSE;
        miniorange_site_otp_validation_form(
            $user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth
        );
    }


    
    function validatePostFields()
    {
        if(is_blocked_ip(get_real_ip_addr())) return FALSE;
        if(emember_wp_username_exists(sanitize_text_field($_POST['wp_emember_user_name']))
            || emember_username_exists(sanitize_text_field($_POST['wp_emember_user_name'])) ) return FALSE;
        if(is_blocked_email($_POST['wp_emember_email']) || emember_registered_email_exists(sanitize_text_field($_POST['wp_emember_email']))
            || emember_wp_email_exists(sanitize_text_field($_POST['wp_emember_email']))) return FALSE;
        if(isset($_POST['eMember_Register']) && array_key_exists('wp_emember_pwd_re',$_POST)
            && sanitize_text_field($_POST['wp_emember_pwd']) != sanitize_text_field($_POST['wp_emember_pwd_re'])) return FALSE;
        return TRUE;
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        $this->unsetOTPSessionVariables();
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId, $this->_formSessionVar]);
    }

    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && $this->isPhoneVerificationEnabled()) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('emember_default_enable');
        $this->_otpType = $this->sanitizeFormPOST('emember_enable_type');

        update_mo_option('emember_default_enable',$this->_isFormEnabled);
        update_mo_option('emember_enable_type',$this->_otpType);
    }
}