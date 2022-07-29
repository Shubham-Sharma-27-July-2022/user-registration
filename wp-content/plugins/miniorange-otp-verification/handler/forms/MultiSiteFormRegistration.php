<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoPHPSessions;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;
use \WP_Error;


class MultiSiteFormRegistration extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar =  FormSessionVars::MULTISITE;
        $this->_phoneFormId = 'input[name=multisite_user_phone_miniorange]';
        $this->_typePhoneTag = 'mo_multisite_contact_phone_enable';
        $this->_typeEmailTag = 'mo_multisite_contact_email_enable';
        $this->_formKey = 'WP_SIGNUP_FORM';
        $this->_formName = mo_('WordPress Multisite SignUp Form');
        $this->_isFormEnabled = get_mo_option('multisite_enable');
        $this->_phoneKey = 'telephone';
        $this->_formDocuments = MoOTPDocs::MULTISITE_REG_FORM;
        parent::__construct();
    }

    
    public function handleForm()
    {
        add_action( 'wp_enqueue_scripts', array($this,'addPhoneFieldScript'));
        add_action( 'user_register', array($this,'_savePhoneNumber'), 10, 1 );
        $this->_otpType= get_mo_option('multisite_otp_type');

        if(!array_key_exists('option',$_POST))  return;

        switch(trim($_POST['option']))
        {
            case 'multisite_register':
                $this->_sanitizeAndRouteData($_POST);   break;
            case 'miniorange-validate-otp-form':
                $this->_startValidation();              break;
        }
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }


    
    public function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
        $this->unsetOTPSessionVariables();
    }


    
    public function _savePhoneNumber($user_id)
    {
        $phoneNumber = MoPHPSessions::getSessionVar('phone_number_mo');
        if($phoneNumber) {
            update_user_meta($user_id, $this->_phoneKey, $phoneNumber);
        }
    }

    
    public function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        $otpVerType = $this->getVerificationType();
        $fromBoth = $otpVerType===VerificationType::BOTH ? TRUE : FALSE;
        miniorange_site_otp_validation_form(
            $user_login,$user_email,$phone_number, MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth
        );
    }


    
    function _sanitizeAndRouteData($getData)
    {
        $result= wpmu_validate_user_signup(sanitize_text_field($_POST['user_name']), sanitize_email($_POST['user_email']));
        
        $errors = $result['errors'];
        if ($errors->get_error_code()) return false;

        Moutility::initialize_transaction($this->_formSessionVar);

        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $this->_processPhone($getData);
        else if (strcasecmp($this->_otpType, $this->_typeEmailTag)==0)
            $this->_processEmail($getData);
        return false;
}

    private function _startValidation()
    {

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        $otpVerType = $this->getVerificationType();
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpVerType)) return;
        $this->validateChallenge($otpVerType);
    }

    
    public function addPhoneFieldScript()
    {
        wp_enqueue_script('multisitescript', MOV_URL . 'includes/js/multisite.min.js?version='.MOV_VERSION , array('jquery'));
    }

    
    private function _processPhone($getData)
    {
        if(!isset($getData['multisite_user_phone_miniorange'])) return;
        $this->sendChallenge('','',null, trim($getData['multisite_user_phone_miniorange']),VerificationType::PHONE);
    }

    
    private function _processEmail($getData)
    {
        if(!isset($getData['user_email'])) return;
        $this->sendChallenge('', $getData['user_email'], null, null,VerificationType::EMAIL,"");
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if(self::isFormEnabled()) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }

    
    public function handleFormOptions()
    {
        if (!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('multisite_enable');
        $this->_otpType = $this->sanitizeFormPOST('multisite_contact_type');

        update_mo_option('multisite_enable',$this->_isFormEnabled);
        update_mo_option('multisite_otp_type',$this->_otpType);
    }
}