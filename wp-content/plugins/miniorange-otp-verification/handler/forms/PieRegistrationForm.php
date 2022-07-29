<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoMessages;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;


class PieRegistrationForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar 	= FormSessionVars::PIE_REG;
        $this->_typePhoneTag = 'mo_pie_phone_enable';
        $this->_typeEmailTag = 'mo_pie_email_enable';
        $this->_typeBothTag = 'mo_pie_both_enable';
        $this->_formKey = 'PIE_FORM';
        $this->_formName = mo_('PIE Registration Form');
        $this->_isFormEnabled = get_mo_option('pie_default_enable');
        $this->_formDocuments = MoOTPDocs::PIE_FORM_LINK ;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('pie_enable_type');
        $this->_phoneKey = get_mo_option('pie_phone_key');
        $this->_phoneFormId = $this->getPhoneFieldKey();
        add_action( 'pie_register_before_register_validate', array($this,'miniorange_pie_user_registration'),99,1);
    }

    
    function isPhoneVerificationEnabled()
    {
        $otpVerType = $this->getVerificationType();
        return $otpVerType===VerificationType::PHONE || $otpVerType===VerificationType::BOTH;
    }


    
    function miniorange_pie_user_registration()
    {
        global $errors;

        if(!empty($errors->errors)) return;
        if($this->checkIfVerificationIsComplete()) return ;
        if(empty($_POST[$this->_phoneFormId]) && $this->isPhoneVerificationEnabled() ) 	{
            $errors->add('mo_otp_verify',MoMessages::showMessage(MoMessages::ENTER_PHONE_DEFAULT));
            return;
        }
        $this->startTheOTPVerificationProcess(sanitize_email($_POST['e_mail']),sanitize_text_field($_POST[$this->_phoneFormId]));

        if(!$this->checkIfVerificationIsComplete()) {
            $errors->add('mo_otp_verify', MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE));
        }
    }

    
    function checkIfVerificationIsComplete()
    {
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType())) {
            $this->unsetOTPSessionVariables();
            return TRUE;
        }
        return FALSE;
    }

    
    function startTheOTPVerificationProcess($userEmail,$phone)
    {

        MoUtility::initialize_transaction($this->_formSessionVar);
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $this->sendChallenge('',$userEmail,null,$phone,VerificationType::PHONE);
        else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
            $this->sendChallenge('',$userEmail,null,$phone,VerificationType::BOTH);
        else
            $this->sendChallenge('',$userEmail,null,$phone,VerificationType::EMAIL);
    }


    
    function getPhoneFieldKey()
    {
        $pie_fields = get_option('pie_fields');
        if(empty($pie_fields)) return '';
        $fields = maybe_unserialize($pie_fields);
        foreach($fields as $key)
        {
            if(strcasecmp(trim($key['label']),$this->_phoneKey)==0) {
                return str_replace("-","_",sanitize_title($key['type']."_"
                    .(isset($key['id']) ? $key['id'] : "")));
            }
        }
        return '';
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {
        $otpVerType = $this->getVerificationType();
        $fromBoth = $otpVerType===VerificationType::BOTH ? TRUE : FALSE;
        miniorange_site_otp_validation_form(
            $user_login,$user_email,$phone_number, MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth
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
            array_push($selector, 'input#'.$this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('pie_default_enable');
        $this->_otpType = $this->sanitizeFormPOST('pie_enable_type');
        $this->_phoneKey = $this->sanitizeFormPOST('pie_phone_field_key');

        update_mo_option('pie_default_enable',$this->_isFormEnabled);
        update_mo_option('pie_enable_type',$this->_otpType);
        update_mo_option('pie_phone_key',$this->_phoneKey);
    }
}