<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\BaseMessages;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationLogic;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;
use WP_Error;


class MemberPressRegistrationForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::MEMBERPRESS_REG;
        $this->_typePhoneTag = 'mo_mrp_phone_enable';
        $this->_typeEmailTag = 'mo_mrp_email_enable';
        $this->_typeBothTag = 'mo_mrp_both_enable';
        $this->_formName = mo_("MemberPress Registration Form");
        $this->_formKey = 'MEMBERPRESS';
        $this->_isFormEnabled = get_mo_option('mrp_default_enable');
        $this->_formDocuments = MoOTPDocs::MRP_FORM_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_byPassLogin = get_mo_option('mrp_anon_only');
        $this->_phoneKey = get_mo_option('mrp_phone_key');
        $this->_otpType = get_mo_option('mrp_enable_type');
        $this->_phoneFormId = 'input[name='.$this->_phoneKey.']';
        add_filter('mepr-validate-signup', array($this,'miniorange_site_register_form'),99,1);
    }


    
    function miniorange_site_register_form($errors)
    {
                if($this->_byPassLogin && is_user_logged_in()) return $errors;

        $usermeta = $_POST;

        $phone_number = '';
        if($this->isPhoneVerificationEnabled()) {
            $phone_number = sanitize_text_field($_POST[$this->_phoneKey]);
            $errors = $this->validatePhoneNumberField($errors);
        }

        if(is_array($errors) && !empty($errors)) return $errors;

        if($this->checkIfVerificationIsComplete()) return $errors;
        MoUtility::initialize_transaction($this->_formSessionVar);
        $errors = new WP_Error();

        foreach ($_POST as $key => $value)
        {
            if($key=="user_first_name")
                $username = $value;
            elseif ($key=="user_email")
                $email = $value;
            elseif ($key=="mepr_user_password")
                $password = $value;
            else
                $extra_data[$key]=$value;
        }

        $extra_data['usermeta'] = $usermeta;
        $this->startVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data);
        return $errors;
    }


    
    function validatePhoneNumberField($errors)
    {
        
	    global $phoneLogic;
    	if(!MoUtility::sanitizeCheck($this->_phoneKey,$_POST)) {
		    $errors[] = mo_( 'Phone number field can not be blank');
	    }
	    else if(!MoUtility::validatePhoneNumber(sanitize_text_field($_POST[$this->_phoneKey]))) {
		    $errors[] = $phoneLogic->_get_otp_invalid_format_message();
	    }
	    return $errors;
    }


    
    function startVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data)
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::PHONE,$password,$extra_data);
        elseif(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::BOTH,$password,$extra_data);
        else
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::EMAIL,$password,$extra_data);
    }

    
    function checkIfVerificationIsComplete()
    {
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType()))
        {
            $this->unsetOTPSessionVariables();
            return TRUE;
        }
        return FALSE;
    }


    
    function moMRPgetphoneFieldId()
    {
        global $wpdb;
        return $wpdb->get_var("SELECT id FROM {$wpdb->prefix}bp_xprofile_fields where name ='".$this->_phoneKey."'");
    }

    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }

    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        $otpVerType = $this->getVerificationType();
        $fromBoth = $otpVerType===VerificationType::BOTH ? TRUE : FALSE;
        miniorange_site_otp_validation_form(
            $user_login,$user_email,$phone_number, MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth
        );
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if(self::isFormEnabled() && $this->isPhoneVerificationEnabled()) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }

    
    function isPhoneVerificationEnabled()
    {
        $otpType = $this->getVerificationType();
        return $otpType===VerificationType::PHONE || $otpType===VerificationType::BOTH;
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('mrp_default_enable');
        $this->_otpType = $this->sanitizeFormPOST('mrp_enable_type');
        $this->_phoneKey = $this->sanitizeFormPOST('mrp_phone_field_key');
        $this->_byPassLogin = $this->sanitizeFormPOST('mpr_anon_only');

        if($this->basicValidationCheck(BaseMessages::MEMBERPRESS_CHOOSE)) {
                        update_mo_option('mrp_default_enable', $this->_isFormEnabled);
            update_mo_option('mrp_enable_type', $this->_otpType);
            update_mo_option('mrp_phone_key',$this->_phoneKey);
            update_mo_option('mrp_anon_only',$this->_byPassLogin);
        }
    }
}