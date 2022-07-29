<?php

namespace OTP\Handler\Forms;

use mysql_xdevapi\Session;
use OTP\Helper\FormSessionVars;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationLogic;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;
use stdClass;


class SimplrRegistrationForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::SIMPLR_REG;
        $this->_typePhoneTag = 'mo_phone_enable';
        $this->_typeEmailTag = 'mo_email_enable';
        $this->_typeBothTag = 'mo_both_enable';
        $this->_formKey = 'SIMPLR_FORM';
        $this->_formName = mo_("Simplr User Registration Form Plus");
        $this->_isFormEnabled = get_mo_option('simplr_default_enable');
        $this->_formDocuments = MoOTPDocs::SIMPLR_FORM_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_formKey = get_mo_option('simplr_field_key');
        $this->_otpType = get_mo_option('simplr_enable_type');
        $this->_phoneFormId = 'input[name='.$this->_formKey.']';
        add_filter( 'simplr_validate_form', array($this,'simplr_site_registration_errors'),10,1);
    }


    
    function isPhoneVerificationEnabled()
    {
        $otpVerType = $this->getVerificationType();
        return $otpVerType===VerificationType::PHONE || $otpVerType===VerificationType::BOTH;
    }


    
    function simplr_site_registration_errors($errors)
    {
        $password = $phone_number = "";

        if(!empty($errors) || isset($_POST['fbuser_id'])) return $errors;

        foreach ($_POST as $key => $value)
        {
            if($key=="username")
                $username = $value;
            elseif ($key=="email")
                $email = $value;
            elseif ($key=="password")
                $password = $value;
            elseif ($key==$this->_formKey)
                $phone_number = $value;
            else
                $extra_data[$key]=$value;
        }
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0
            && !$this->processPhone($phone_number,$errors)) return $errors;
        $this->processAndStartOTPVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data);
        return $errors;
    }


    
    function processPhone($phone_number,&$errors)
    {
        if(!MoUtility::validatePhoneNumber($phone_number))
        {
            
            global $phoneLogic;
            $errors[].= str_replace("##phone##",$phone_number,$phoneLogic->_get_otp_invalid_format_message());
            add_filter($this->_formKey.'_error_class','_sreg_return_error');
            return FALSE;
        }
        return TRUE;
    }


    
    function processAndStartOTPVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data)
    {
        MoUtility::initialize_transaction($this->_formSessionVar);
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::PHONE,$password,$extra_data);
        else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::BOTH,$password,$extra_data);
        else
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::EMAIL,$password,$extra_data);
    }


    
    function register_simplr_user($user_login,$user_email,$password,$phone_number,$extra_data)
    {
        $data = array();
        global $sreg;
        if( !$sreg ) $sreg = new stdClass;
        $data['username'] 	= $user_login;
        $data['email'] 		= $user_email;
        $data['password'] 	= $password;
        if($this->_formKey) $data[$this->_formKey] = $phone_number;
        $data = array_merge($data,$extra_data);
        $atts = $extra_data['atts'];
        $sreg->output = simplr_setup_user($atts,$data);
        if(MoUtility::isBlank($sreg->errors))
            $this->checkMessageAndRedirect($atts);
    }


    
    function checkMessageAndRedirect($atts)
    {
        global $sreg,$simplr_options;

        $page = isset($atts['thanks']) ? get_permalink($atts['thanks'])
                : (!MoUtility::isBlank($simplr_options->thank_you) ? get_permalink($simplr_options->thank_you) : '' );
        if(MoUtility::isBlank($page))
            $sreg->success = $sreg->output;
        else
        {
            wp_redirect($page);
            exit;
        }
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


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        $this->unsetOTPSessionVariables();
        $this->register_simplr_user($user_login,$user_email,$password,$phone_number,$extra_data);
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

        $this->_isFormEnabled = $this->sanitizeFormPOST('simplr_default_enable');
        $this->_otpType = $this->sanitizeFormPOST('simplr_enable_type');
        $this->_phoneKey =$this->sanitizeFormPOST('simplr_phone_field_key');

        update_mo_option('simplr_default_enable',$this->_isFormEnabled);
        update_mo_option('simplr_enable_type',$this->_otpType);
        update_mo_option('simplr_field_key',$this->_phoneKey);
    }
}