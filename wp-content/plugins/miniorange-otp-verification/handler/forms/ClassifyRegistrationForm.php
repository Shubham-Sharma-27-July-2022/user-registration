<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoPHPSessions;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Traits\Instance;
use ReflectionException;


class ClassifyRegistrationForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar 	= FormSessionVars::CLASSIFY_REGISTER;
        $this->_typePhoneTag = 'classify_phone_enable';
        $this->_typeEmailTag = 'classify_email_enable';
        $this->_formKey = 'CLASSIFY_REGISTER';
        $this->_formName = mo_('Classify Theme Registration Form');
        $this->_isFormEnabled = get_mo_option('classify_enable');
        $this->_phoneFormId = 'input[name=phone]';
        $this->_formDocuments = MoOTPDocs::CLASSIFY_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('classify_type');

        add_action( 'wp_enqueue_scripts', array($this,'_show_phone_field_on_page'));
        add_action( 'user_register', array($this,'save_phone_number'), 10, 1);

        $this->routeData();
    }

    
    function routeData()
    {


        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType()))
            $this->unsetOTPSessionVariables();
        else if(MoUtility::sanitizeCheck("option",$_POST)==="verify_user_classify")
            $this->_handle_classify_theme_form_post($_POST);
    }


    
    function _show_phone_field_on_page()
    {
        wp_enqueue_script('classifyscript', MOV_URL . 'includes/js/classify.min.js?version='.MOV_VERSION , array('jquery'));
    }


    
    function _handle_classify_theme_form_post($data)
    {
        $username = sanitize_text_field($data['username']);
        $email_id = sanitize_email($data['email']);
        $phone 	  = sanitize_text_field($data['phone']);

        if ( username_exists( $username )!=FALSE ) return;
        if ( email_exists( $email_id )!=FALSE ) return;

        MoUtility::initialize_transaction($this->_formSessionVar);

        if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0)
            $this->sendChallenge(sanitize_text_field($_POST['username']),$email_id,null,$phone,"phone",null,null);
        else if(strcasecmp($this->_otpType,$this->_typeEmailTag)===0)
            $this->sendChallenge(sanitize_text_field($_POST['username']),$email_id,null, null,"email",null,null);
        else
            $this->sendChallenge(sanitize_text_field($_POST['username']),$email_id,null, $phone,"both",null,null);
    }


    
    function save_phone_number($user_id)
    {

        $phoneNumber = MoPHPSessions::getSessionVar('phone_number_mo');
        if($phoneNumber) {
            update_user_meta($user_id, 'phone', $phoneNumber);
        }
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        $otpVerType = strcasecmp($this->_otpType,$this->_typePhoneTag)===0 ? "phone"
                        : (strcasecmp($this->_otpType,$this->_typeEmailTag)===0 ? "email" : "both" );
        $fromBoth = strcasecmp($otpVerType,"both")===0 ? TRUE : FALSE;
        miniorange_site_otp_validation_form(
            $user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth
        );
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }

    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_formSessionVar,$this->_txSessionId]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && $this->_otpType===$this->_typePhoneTag) array_push($selector, $this->_phoneFormId);
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_otpType = $this->sanitizeFormPOST('classify_type');
        $this->_isFormEnabled = $this->sanitizeFormPOST('classify_enable');

        update_mo_option('classify_enable',$this->_isFormEnabled);
        update_mo_option('classify_type',$this->_otpType);
    }
}