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


class RealesWPTheme extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::REALESWP_REGISTER;
        $this->_typePhoneTag = 'mo_reales_phone_enable';
        $this->_typeEmailTag = 'mo_reales_email_enable';
        $this->_phoneFormId = '#phoneSignup';
        $this->_formKey = 'REALES_REGISTER';
        $this->_formName = mo_("Reales WP Theme Registration Form");
        $this->_isFormEnabled = get_mo_option('reales_enable');
        $this->_formDocuments = MoOTPDocs::REALES_THEME;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('reales_enable_type');
        add_action('wp_enqueue_scripts', array($this,'enqueue_script_on_page'));
        $this->routeData();
    }


    
    function routeData()
    {
        if(!array_key_exists('option', $_GET)) return;
        switch (trim($_GET['option']))
        {
            case "miniorange-realeswp-verify":
                $this->_send_otp_realeswp_verify($_POST);		break;
            case "miniorange-validate-realeswp-otp":
                $this->_reales_validate_otp($_POST);			break;
        }
    }


    
    function enqueue_script_on_page()
    {
        wp_register_script( 'realeswpScript', MOV_URL . 'includes/js/realeswp.min.js?version='.MOV_VERSION , array('jquery') );
        wp_localize_script('realeswpScript', 'movars', array(
            'imgURL'		=> MOV_URL. "includes/images/loader.gif",
            'fieldname' 	=> $this->_otpType==$this->_typePhoneTag ? 'phone number' : 'email',
            'field'     	=> $this->_otpType==$this->_typePhoneTag ? 'phoneSignup' : 'emailSignup',
            'siteURL' 		=> site_url(),
            'insertAfter'	=> $this->_otpType==$this->_typePhoneTag ? '#phoneSignup' : '#emailSignup',
            'placeHolder' 	=> mo_('OTP Code'),
            'buttonText'	=> mo_('Validate and Sign Up'),
            'ajaxurl'       => wp_ajax_url(),
        ));
        wp_enqueue_script('realeswpScript');
    }


    
    function _send_otp_realeswp_verify($data)
    {

        MoUtility::initialize_transaction($this->_formSessionVar);
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $this->_send_otp_to_phone($data);
        else
            $this->_send_otp_to_email($data);
    }


    
    function _send_otp_to_phone($data)
    {
        if(array_key_exists('user_phone', $data) && !MoUtility::isBlank(sanitize_text_field($data['user_phone']))) {
            SessionUtils::addPhoneVerified($this->_formSessionVar,trim($data['user_phone']));
            $this->sendChallenge('test','',null, trim($data['user_phone']),VerificationType::PHONE);
        } else {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }


    
    function _send_otp_to_email($data)
    {
        if(array_key_exists('user_email', $data) && !MoUtility::isBlank(sanitize_email($data['user_email']))) {
            SessionUtils::addEmailVerified($this->_formSessionVar,$data['user_email']);
            $this->sendChallenge('test',$data['user_email'],null,$data['user_email'],VerificationType::EMAIL);
        } else {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }


    
    function _reales_validate_otp($data)
    {

        $moOTP = !isset($data['otp']) ? sanitize_text_field( $data['otp'] ) : '';

        $this->checkIfOTPVerificationHasStarted();
        $this->validateSubmittedFields($data);
        $this->validateChallenge(NULL,$moOTP);
    }


    
    function validateSubmittedFields($data)
    {
        $otpVerType = $this->getVerificationType();
        if($otpVerType===VerificationType::EMAIL
            && !SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,sanitize_email($data['user_email']))) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::EMAIL_MISMATCH), MoConstants::ERROR_JSON_TYPE
            ));
            die();
        }elseif($otpVerType===VerificationType::PHONE
            && !SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,sanitize_text_field($data['user_phone']))) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::PHONE_MISMATCH), MoConstants::ERROR_JSON_TYPE
            ));
            die();
        }
    }


    
    function checkIfOTPVerificationHasStarted()
    {
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::PLEASE_VALIDATE), MoConstants::ERROR_JSON_TYPE
            ));
            die();
        }
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        wp_send_json( MoUtility::createJson(
            MoUtility::_get_invalid_otp_method(),MoConstants::ERROR_JSON_TYPE
        ));
        die();
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        $this->unsetOTPSessionVariables();
        wp_send_json( MoUtility::createJson( MoMessages::REG_SUCCESS,MoConstants::SUCCESS_JSON_TYPE ));
        die();
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && $this->_otpType==$this->_typePhoneTag) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('reales_enable');
        $this->_otpType = $this->sanitizeFormPOST('reales_enable_type');

        update_mo_option('reales_enable',$this->_isFormEnabled);
        update_mo_option('reales_enable_type',$this->_otpType);
    }
}