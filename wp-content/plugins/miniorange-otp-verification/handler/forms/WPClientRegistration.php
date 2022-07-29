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
use WP_Error;


class WPClientRegistration extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::WP_CLIENT_REG;
        $this->_phoneKey = 'wp_contact_phone';
        $this->_phoneFormId = "#wpc_contact_phone";
        $this->_formKey = 'WP_CLIENT_REG';
        $this->_typePhoneTag = "mo_wp_client_phone_enable";
        $this->_typeEmailTag = "mo_wp_client_email_enable";
        $this->_typeBothTag = 'mo_wp_client_both_enable';
        $this->_formName = mo_("WP Client Registration Form");
        $this->_isFormEnabled = get_mo_option('wp_client_enable');
        $this->_formDocuments = MoOTPDocs::WP_CLIENT_FORM;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('wp_client_enable_type');
        $this->_restrictDuplicates = get_mo_option('wp_client_restrict_duplicates');
        add_filter('wpc_client_registration_form_validation', [$this,'miniorange_client_registration_verify'],99,1);
    }


    
    function isPhoneVerificationEnabled()
    {
        $otpType = $this->getVerificationType();
        return $otpType===VerificationType::PHONE || $otpType===VerificationType::BOTH;
    }

    
    function miniorange_client_registration_verify($errors)
    {
        $otpType = $this->getVerificationType();
        $phone_number = MoUtility::sanitizeCheck('contact_phone',$_POST);
        $user_email = MoUtility::sanitizeCheck('contact_email',$_POST);
        $sanitized_user_login = MoUtility::sanitizeCheck('contact_username',$_POST);

        if($this->_restrictDuplicates && $this->isPhoneNumberAlreadyInUse($phone_number,$this->_phoneKey)){
            $errors.= mo_("Phone number already in use. Please Enter a different Phone number.");
        }

        if(!MoUtility::isBlank($errors)) return $errors;


        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) {
            MoUtility::initialize_transaction($this->_formSessionVar);
        }elseif(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpType)) {
            $this->unsetOTPSessionVariables();
            return $errors;
        }
        return $this->startOTPTransaction($sanitized_user_login,$user_email,$errors,$phone_number);
    }

    
    function startOTPTransaction($sanitized_user_login,$user_email,$errors,$phone_number)
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0)
            $this->sendChallenge($sanitized_user_login,$user_email,$errors,$phone_number,VerificationType::PHONE);
        else if(strcasecmp($this->_otpType,$this->_typeBothTag)===0)
            $this->sendChallenge($sanitized_user_login,$user_email,$errors,$phone_number,VerificationType::BOTH);
        else
            $this->sendChallenge($sanitized_user_login,$user_email,$errors,$phone_number,VerificationType::EMAIL);

        return $errors;
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        $otpVerType = $this->getVerificationType();
        $fromBoth = $otpVerType===VerificationType::BOTH ? TRUE : FALSE;
        miniorange_site_otp_validation_form(
            $user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth
        );
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }

    
    function isPhoneNumberAlreadyInUse($phone, $key)
    {
        global $wpdb;
        $phone = MoUtility::processPhoneNumber($phone);
        $results = $wpdb->get_row("SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$key' AND `meta_value` =  '$phone'");
        return !MoUtility::isBlank($results);
    }

    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
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

        $this->_isFormEnabled = $this->sanitizeFormPOST('wp_client_enable');
        $this->_otpType = $this->sanitizeFormPOST('wp_client_enable_type');
        $this->_restrictDuplicates = $this->getVerificationType() === VerificationType::PHONE
            ? $this->sanitizeFormPOST('wp_client_restrict_duplicates') : false;

        update_mo_option('wp_client_enable', $this->_isFormEnabled);
        update_mo_option('wp_client_enable_type', $this->_otpType);
        update_mo_option('wp_client_restrict_duplicates', $this->_restrictDuplicates);
    }
}