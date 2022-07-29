<?php

namespace OTP\Handler\Forms;

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


class PaidMembershipForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::PMPRO_REGISTRATION;
        $this->_formKey = 'PM_PRO_FORM';
        $this->_formName = mo_('Paid MemberShip Pro Registration Form');
        $this->_phoneFormId = 'input[name=phone_paidmembership]';
        $this->_typePhoneTag = "pmpro_phone_enable";
        $this->_typeEmailTag = "pmpro_email_enable";
        $this->_isFormEnabled = get_mo_option('pmpro_enable');
        $this->_formDocuments = MoOTPDocs::PAID_MEMBERSHIP_PRO;
        parent::__construct();
    }


    
    function handleForm()
    {
        $this->_otpType = get_mo_option('pmpro_otp_type');
        add_action( 'wp_enqueue_scripts', array($this,'_show_phone_field_on_page'));
        add_filter( 'pmpro_checkout_before_processing', array($this,'_paidMembershipProRegistrationCheck'), 1, 1 );
        add_filter( 'pmpro_checkout_confirmed', array( $this, 'isValidated' ), 99, 2 );
        add_action('user_register', array($this,'miniorange_registration_save'), 10, 1 );

    }

    function miniorange_registration_save($user_id)
    {
        update_user_meta($user_id,'mo_phone_number',sanitize_text_field($_POST['phone_paidmembership']));
    }

    
    public function isValidated($pmpro_confirmed, $morder)
    {
     	global $pmpro_msgt;
     	return $pmpro_msgt=="pmpro_error" ? false : $pmpro_confirmed;
    }


    
    public function _paidMembershipProRegistrationCheck()
    {
        global $pmpro_msgt;

        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType())){
            $this->unsetOTPSessionVariables();
            return;
        }
        $this->validatePhone($_POST);
        if($pmpro_msgt != "pmpro_error") {
            MoUtility::initialize_transaction($this->_formSessionVar);
            $this->startOTPVerificationProcess($_POST);
        }
    }


    
    private function startOTPVerificationProcess($data)
    {
        if(strcasecmp($this->_otpType, $this->_typePhoneTag)==0){
            $this->sendChallenge('','',null, trim(sanitize_text_field($data['phone_paidmembership'])),"phone");
        } elseif(strcasecmp($this->_otpType, $this->_typeEmailTag)==0){
            $this->sendChallenge('',sanitize_email($data['bemail']),null,sanitize_email($data['bemail']),"email");
        }
    }


    
    public function validatePhone($data)
    {
        if($this->getVerificationType()!=VerificationType::PHONE) return;
        
        global $pmpro_msg, $pmpro_msgt,$phoneLogic,$pmpro_requirebilling;
        if($pmpro_msgt=='pmpro_error') return;
        $phoneValue= sanitize_text_field($data['phone_paidmembership']);
        if(!MoUtility::validatePhoneNumber($phoneValue))
        {
            $message = str_replace("##phone##",$phoneValue,$phoneLogic->_get_otp_invalid_format_message());
            $pmpro_msgt = "pmpro_error";
            $pmpro_requirebilling = false;
            $pmpro_msg = apply_filters('pmpro_set_message', $message, $pmpro_msgt);
        }
    }



    
    function _show_phone_field_on_page()
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0){
            wp_enqueue_script('paidmembershipscript', MOV_URL . 'includes/js/paidmembershippro.min.js?version='.MOV_VERSION , array('jquery'));
        }
    }


    
    function handle_failed_verification($user_login, $user_email, $phone_number,$otpType)
    {

        $otpVerType = $this->getVerificationType();
        $fromBoth = $otpVerType===VerificationType::BOTH ? TRUE : FALSE;
        miniorange_site_otp_validation_form(
            $user_login, $user_email, $phone_number, MoUtility::_get_invalid_otp_method(), $otpVerType, $fromBoth
        );
    }


    
    function handle_post_verification($redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if(self::isFormEnabled() && $this->_otpType==$this->_typePhoneTag) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if (!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('pmpro_enable');
        $this->_otpType = $this->sanitizeFormPOST('pmpro_contact_type');

        update_mo_option('pmpro_enable',$this->_isFormEnabled);
        update_mo_option('pmpro_otp_type',$this->_otpType);
    }
}