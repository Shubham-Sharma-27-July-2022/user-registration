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


class ProfileBuilderRegistrationForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar 	= FormSessionVars::PB_DEFAULT_REG;
        $this->_typePhoneTag = 'mo_pb_phone_enable';
        $this->_typeEmailTag = 'mo_pb_email_enable';
        $this->_typeBothTag = 'mo_pb_both_enable';
        $this->_formKey = 'PB_DEFAULT_FORM';
        $this->_formName = mo_("Profile Builder Registration Form");
        $this->_isFormEnabled = get_mo_option('pb_default_enable');
        $this->_formDocuments = MoOTPDocs::PB_FORM_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('pb_enable_type');
        $this->_phoneKey = get_mo_option('pb_phone_meta_key');
        $this->_phoneFormId = "input[name=" . $this->_phoneKey . "]";
        add_filter( 'wppb_output_field_errors_filter', array($this,'formbuilder_site_registration_errors'),99,4);
    }


    
    function isPhoneVerificationEnabled()
    {
        $otpVerType = $this->getVerificationType();
        return $otpVerType===VerificationType::PHONE || $otpVerType===VerificationType::BOTH;
    }


    
    function formbuilder_site_registration_errors($fieldErrors, $fieldArgs, $globalRequest, $typeArgs)
    {

        
        if(!empty($fieldErrors)) return $fieldErrors;
        if($globalRequest['action']=='register')
        {
            if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType()))
            {
                $this->unsetOTPSessionVariables();
                return $fieldErrors;
            }
            $this->startOTPVerificationProcess($fieldErrors,$globalRequest);
        }
        return $fieldErrors;
    }


    
    function startOTPVerificationProcess($fieldErrors,$data)
    {
        MoUtility::initialize_transaction($this->_formSessionVar);
        $args = $this->extractArgs($data,$this->_phoneKey);
        $this->sendChallenge(
            $args["username"],
            $args["email"],
            new WP_Error(),
            $args["phone"],
            $this->getVerificationType(),
            $args["passw1"], array()
        );
    }


    
    private function extractArgs($args,$phoneKey)
    {
        return [
            "username"    => $args['username'],
            "email"    => $args['email'],
            "passw1" => $args['passw1'],
            "phone" => MoUtility::sanitizeCheck($phoneKey,$args)
        ];
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        miniorange_site_otp_validation_form(
            $user_login,$user_email,$phone_number, MoUtility::_get_invalid_otp_method(),$this->getVerificationType(),FALSE
        );
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
        if($this->isFormEnabled() && $this->isPhoneVerificationEnabled()) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('pb_default_enable');
        $this->_otpType = $this->sanitizeFormPOST('pb_enable_type');
        $this->_phoneKey = $this->sanitizeFormPOST('pb_phone_field_key');

        update_mo_option('pb_default_enable',$this->_isFormEnabled);
        update_mo_option('pb_enable_type',$this->_otpType);
        update_mo_option('pb_phone_meta_key',$this->_phoneKey);
    }
}