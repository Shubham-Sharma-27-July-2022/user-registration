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


class DocDirectThemeRegistration extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::DOCDIRECT_REG;
        $this->_typePhoneTag = 'mo_docdirect_phone_enable';
        $this->_typeEmailTag = 'mo_docdirect_email_enable';
        $this->_formKey = 'DOCDIRECT_THEME';
        $this->_formName = mo_('Doc Direct Theme by ThemoGraphics');
        $this->_isFormEnabled = get_mo_option('docdirect_enable');
        $this->_phoneFormId = 'input[name=phone_number]';
        $this->_formDocuments = MoOTPDocs::DOCDIRECT_THEME;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('docdirect_enable_type');
        add_action( 'wp_enqueue_scripts', array($this,'addScriptToRegistrationPage'));
        add_action('wp_ajax_docdirect_user_registration', array($this,'mo_validate_docdirect_user_registration'),1);
        add_action('wp_ajax_nopriv_docdirect_user_registration', array($this,'mo_validate_docdirect_user_registration'),1);
        $this->routeData();
    }


    
    function routeData()
    {
        if(!array_key_exists('option', $_GET)) return;
        switch (trim($_GET['option']))
        {
            case "miniorange-docdirect-verify":
                $this->startOTPVerificationProcess($_POST);			break;
        }
    }


    
    function addScriptToRegistrationPage()
    {
        wp_register_script( 'docdirect', MOV_URL . 'includes/js/docdirect.min.js?version='.MOV_VERSION , array('jquery') ,MOV_VERSION,true);
        wp_localize_script( 'docdirect', 'modocdirect', array(
            'imgURL'		=> MOV_URL. "includes/images/loader.gif",
            'buttonText' 	=> mo_("Click Here to Verify Yourself"),
            'insertAfter'	=> strcasecmp($this->_otpType,$this->_typePhoneTag)===0 ? 'input[name=phone_number]' : 'input[name=email]',
            'placeHolder' 	=> mo_('OTP Code'),
            'siteURL' 		=> 	site_url(),
        ));
        wp_enqueue_script('docdirect');
    }


    
    function startOtpVerificationProcess($data)
    {

        MoUtility::initialize_transaction($this->_formSessionVar);
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0)
            $this->_send_otp_to_phone($data);
        else
            $this->_send_otp_to_email($data);
    }


    
    function _send_otp_to_phone($data)
    {
        if(array_key_exists('user_phone', $data) && !MoUtility::isBlank($data['user_phone'])) {
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
        if(array_key_exists('user_email', $data) && !MoUtility::isBlank($data['user_email'])) {
            SessionUtils::addEmailVerified($this->_formSessionVar,$data['user_email']);
            $this->sendChallenge('test',$data['user_email'],null,$data['user_email'],VerificationType::EMAIL);
        } else {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }


    
    function mo_validate_docdirect_user_registration()
    {

        $this->checkIfVerificationNotStarted();
        $this->checkIfVerificationCodeNotEntered();
        $this->handle_otp_token_submitted();
    }


    
    function checkIfVerificationNotStarted()
    {
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) {
            echo json_encode( array('type' => 'error', 'message' =>  MoMessages::showMessage(MoMessages::DOC_DIRECT_VERIFY)) );
            die();
        }
    }


    
    function checkIfVerificationCodeNotEntered()
    {
        if(!array_key_exists('mo_verify', $_POST) || MoUtility::isBlank(sanitize_text_field($_POST['mo_verify']))){
            echo json_encode( array('type' => 'error', 'message' =>  MoMessages::showMessage(MoMessages::DCD_ENTER_VERIFY_CODE)) );
            die();
        }
    }


    
    function handle_otp_token_submitted()
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0)
            $this->processPhoneNumber();
        else
            $this->processEmail();
        $this->validateChallenge($this->getVerificationType(),'mo_verify',NULL);
    }


    
    function processPhoneNumber()
    {

        if(!SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,$_POST['phone_number'])) {
            echo json_encode( array('type' => 'error', 'message' =>  MoMessages::showMessage(MoMessages::PHONE_MISMATCH)));
            die();
        }
    }


    
    function processEmail()
    {

        if(!SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,$_POST["email"])) {
            echo json_encode( array('type' => 'error', 'message' =>  MoMessages::showMessage(MoMessages::EMAIL_MISMATCH)));
            die();
        }
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        echo json_encode( array('type' => 'error', 'message' =>  MoUtility::_get_invalid_otp_method()) );
        die();
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        $this->unsetOTPSessionVariables();
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && ($this->_otpType === $this->_typePhoneTag)) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_otpType = $this->sanitizeFormPOST('docdirect_enable_type');
        $this->_isFormEnabled = $this->sanitizeFormPOST('docdirect_enable');

        update_mo_option('docdirect_enable',$this->_isFormEnabled);
        update_mo_option('docdirect_enable_type', $this->_otpType);
    }
}