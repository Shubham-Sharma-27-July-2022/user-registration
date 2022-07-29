<?php

namespace OTP\Handler;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoPHPSessions;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;
use WP_Error;


class CustomForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm          = TRUE;
        $this->_isAddOnForm         = TRUE;
        $this->isEnabled            = get_mo_option("cf_submit_id","mo_otp_") ? true : false;
        $this->_formSessionVar      =  FormSessionVars:: CUSTOMFORM;
        $this->_typePhoneTag        = 'mo_customForm_phone_enable';
        $this->_typeEmailTag        = 'mo_customForm_email_enable';
        $this->_isFormEnabled       = $this->isEnabled;
        $this->_phoneFormId         = stripslashes(get_mo_option("cf_field_id","mo_otp_"));
        $this->_generateOTPAction   = "miniorange-customForm-send-otp";
        $this->_validateOTPAction   = "miniorange-customForm-verify-code";
        $this->_checkValidatedOnSubmit = "miniorange-customForm-verify-submit";
        $this->_otpType             =   get_mo_option("cf_enable_type","mo_otp_");
        $this->_buttonText          =   get_mo_option("cf_button_text","mo_otp_");
        $this->_buttonText          =   !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_("Click Here to send OTP");

        $this->validated            = FALSE;
        parent::__construct();
        $this->handleForm();
    }

    
    function handleForm()
    {
        MoPHPSessions::checkSession();
        if(!$this->isEnabled) return;
        
        add_action('wp_enqueue_scripts', array($this, 'mo_enqueue_form_script'));
        add_action('login_enqueue_scripts', array($this, 'mo_enqueue_form_script'));
        add_action("wp_ajax_{$this->_generateOTPAction}", [$this,'_send_otp']);
        add_action("wp_ajax_nopriv_{$this->_generateOTPAction}", [$this,'_send_otp']);
        add_action("wp_ajax_{$this->_validateOTPAction}", [$this,'processFormAndValidateOTP']);
        add_action("wp_ajax_nopriv_{$this->_validateOTPAction}", [$this,'processFormAndValidateOTP']);
        add_action("wp_ajax_{$this->_checkValidatedOnSubmit}", [$this,'_checkValidatedOnSubmit']);
        add_action("wp_ajax_nopriv_{$this->_checkValidatedOnSubmit}", [$this,'_checkValidatedOnSubmit']);

        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType())){
            $this->validated = TRUE;
            $this->unsetOTPSessionVariables();
            return;


        }
    }


 function mo_enqueue_form_script()
    {
        wp_register_script( $this->_formSessionVar, MOV_URL . 'includes/js/'.$this->_formSessionVar.'.js',array('jquery') );
        wp_localize_script( $this->_formSessionVar, $this->_formSessionVar, array(
            'siteURL'       =>  wp_ajax_url(),
            'otpType'       =>  $this->getVerificationType(),
            'formDetails'   =>  $this->_formDetails,
            'buttontext'    =>  $this->_buttonText,
            'imgURL'        =>  MOV_LOADER_URL,
            'fieldText'     =>  mo_('Enter OTP here'),
            'gnonce'        =>  wp_create_nonce($this->_nonce),
            'nonceKey'      =>  wp_create_nonce($this->_nonceKey),
            'vnonce'        =>  wp_create_nonce($this->_nonce),
            'gaction'       =>  $this->_generateOTPAction,
            'vaction'       =>  $this->_validateOTPAction,
            'fieldSelector' =>stripcslashes(get_mo_option("cf_field_id","mo_otp_")),
            'submitSelector'=>stripcslashes(get_mo_option("cf_submit_id","mo_otp_")),
            'siteURL'       =>wp_ajax_url(), 
            'saction'       => $this->_checkValidatedOnSubmit,
             ) );
        wp_enqueue_script( $this->_formSessionVar );
        wp_enqueue_style( 'mo_forms_css', MOV_FORM_CSS);

    }


    function _send_otp()
    {
        MoPHPSessions::checkSession();
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar))
            MoUtility::initialize_transaction($this->_formSessionVar);
        if(MoUtility::sanitizeCheck('otpType',$_POST)===VerificationType::PHONE)
            $this->_processPhoneAndSendOTP($_POST);
        if(MoUtility::sanitizeCheck('otpType',$_POST)===VerificationType::EMAIL)
            $this->_processEmailAndSendOTP($_POST);
    }


    public function _checkValidatedOnSubmit()
    {

        if(SessionUtils::isOTPInitialized($this->_formSessionVar)|| $this->validated)
        {
             wp_send_json(MoUtility::createJson(
               self::VALIDATED,
                MoConstants::SUCCESS_JSON_TYPE
            ));
        }
        else if(!SessionUtils::isOTPInitialized($this->_formSessionVar) && !$this->validated){
            wp_send_json(MoUtility::createJson(
               MoMessages::showMessage(MoMessages::PLEASE_VALIDATE),
                MoConstants::ERROR_JSON_TYPE
            ));
        }
     } 
    private function _processEmailAndSendOTP($data)
    {
        MoPHPSessions::checkSession();
        if(!MoUtility::sanitizeCheck('user_email',$data)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            SessionUtils::addEmailVerified($this->_formSessionVar,sanitize_email($data['user_email']));
            $this->sendChallenge('',sanitize_email($data['user_email']),NULL,NULL,VerificationType::EMAIL);
        }
    }


    
    private function _processPhoneAndSendOTP($data)
    {
        if(!MoUtility::sanitizeCheck('user_phone',$data)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
                        SessionUtils::addPhoneVerified($this->_formSessionVar,sanitize_text_field($data['user_phone']));
            $this->sendChallenge('',NULL,NULL,sanitize_text_field($data['user_phone']),VerificationType::PHONE);
        }
    }

    function processFormAndValidateOTP()
    {
        MoPHPSessions::checkSession();
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
        MoPHPSessions::checkSession();
        $this->checkIntegrity($data);
        $this->validateChallenge(sanitize_text_field($data['otpType']),NULL,sanitize_text_field($data['otp_token']));
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,sanitize_text_field($data['otpType']))) {
            if($data['otpType']===VerificationType::PHONE ) {                
                SessionUtils::addPhoneSubmitted($this->_formSessionVar,sanitize_text_field($data['user_phone']));
            }
            if($data['otpType']===VerificationType::EMAIL ) {
                SessionUtils::addEmailSubmitted($this->_formSessionVar,sanitize_email($data['user_email']));
            }
                                                wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::CUSTOM_FORM_MESSAGE), MoConstants::ERROR_JSON_TYPE
            ));
        }else{
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::CUSTOM_FORM_MESSAGE), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }

    private function checkIntegrity($data)
    {
        if($data['otpType']===VerificationType::PHONE ) {
            if(!SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,sanitize_text_field($data['user_phone'])) ){
                wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::PHONE_MISMATCH), MoConstants::ERROR_JSON_TYPE
                ));
            }
        }
        if($data['otpType']===VerificationType::EMAIL ) {
            if(!SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,sanitize_email($data['user_email']))) {
                wp_send_json(MoUtility::createJson(
                        MoMessages::showMessage(MoMessages::EMAIL_MISMATCH), MoConstants::ERROR_JSON_TYPE
                ));
            }
        }
    }

    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {
        MoPHPSessions::checkSession();
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        SessionUtils::addStatus($this->_formSessionVar,self::VERIFICATION_FAILED,$otpType);
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {
        MoPHPSessions::checkSession();
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }

    
    
    public function unsetOTPSessionVariables()
    {
        MoPHPSessions::checkSession();
        SessionUtils::unsetSession([$this->_formSessionVar,$this->_txSessionId]);
    }


    
    public function getPhoneNumberSelector($selector)
    {
        if($this->isFormEnabled() && $this->isPhoneEnabled()) 
            array_push($selector, $this->_phoneFormId);
            return $selector;
    }

    function isPhoneEnabled (){
        return $this->getVerificationType() == VerificationType::PHONE ? TRUE : FALSE;
    }

    
    function handleFormOptions()
    {
        
    }

    function getSubmitKeyDetails(){ return stripcslashes(get_mo_option("cf_submit_id","mo_otp_")); }

    function getFieldKeyDetails(){ return stripcslashes(get_mo_option("cf_field_id","mo_otp_")); } 
}