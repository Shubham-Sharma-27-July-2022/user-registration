<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
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
use um\core\Form;
use WP_Error;


class UltimateMemberRegistrationForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = get_mo_option('um_is_ajax_form');
        $this->_formSessionVar = FormSessionVars::UM_DEFAULT_REG;
        $this->_typePhoneTag = 'mo_um_phone_enable';
        $this->_typeEmailTag = 'mo_um_email_enable';
        $this->_typeBothTag = 'mo_um_both_enable';
        $this->_phoneKey =  get_mo_option('um_phone_key');
        $this->_phoneKey = $this->_phoneKey ? $this->_phoneKey : "mobile_number";
        $this->_phoneFormId= "input[name^='".$this->_phoneKey."']";
        $this->_formKey = 'ULTIMATE_FORM';
        $this->_formName = mo_("Ultimate Member Registration Form");
        $this->_isFormEnabled = get_mo_option('um_default_enable');
        $this->_restrictDuplicates = get_mo_option('um_restrict_duplicates');
        $this->_buttonText = get_mo_option("um_button_text");
        $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_("Click Here to send OTP");
        $this->_formKey = get_mo_option('um_verify_meta_key');
        $this->_formDocuments = MoOTPDocs::UM_ENABLED;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('um_enable_type');
        if($this->isUltimateMemberV2Installed()) {
            add_action('um_submit_form_errors_hook__registration', array($this, 'miniorange_um2_phone_validation'), 99, 1);
            add_filter('um_registration_user_role', array($this, 'miniorange_um2_user_registration'), 99, 2);
        }else{
            add_action( 'um_submit_form_errors_hook_', array($this,'miniorange_um_phone_validation'), 99,1);
            add_action( 'um_before_new_user_register', array($this,'miniorange_um_user_registration'), 99,1);
        }
        if($this->_isAjaxForm && $this->_otpType!=$this->_typeBothTag) {
            add_action('wp_enqueue_scripts',array($this, 'miniorange_register_um_script'));
            $this->routeData();
        }
    }


    
    function isUltimateMemberV2Installed()
    {
        if( !function_exists('is_plugin_active') ) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        return is_plugin_active( 'ultimate-member/ultimate-member.php' );
    }


    
    private function routeData()
    {
        if(!array_key_exists('option', $_GET)) return;
        switch (trim($_GET['option']))
        {
            case "miniorange-um-ajax-verify":
                $this->sendAjaxOTPRequest();	break;
        }
    }


    
    private function sendAjaxOTPRequest()
    {
        MoUtility::initialize_transaction($this->_formSessionVar);
        $this->validateAjaxRequest();
        $mobile_number = MoUtility::sanitizeCheck('user_phone',$_POST);
        $user_email = MoUtility::sanitizeCheck('user_email',$_POST);
        if($this->_otpType===$this->_typePhoneTag){
            $this->checkDuplicates($mobile_number,$this->_phoneKey,null);
            SessionUtils::addPhoneVerified($this->_formSessionVar,$mobile_number);
        } else {
            SessionUtils::addEmailVerified($this->_formSessionVar,$user_email);
        }
        $this->startOtpTransaction(null,$user_email,null,$mobile_number,null,null);
    }


    
    function miniorange_register_um_script()
    {
        wp_register_script( 'movum', MOV_URL . 'includes/js/umreg.min.js',array('jquery') );
        wp_localize_script( 'movum', 'moumvar', array(
            'siteURL' 		=> site_url(),
            'otpType'  		=> $this->_otpType,
            'nonce'         => wp_create_nonce($this->_nonce),
            'buttontext'    => mo_($this->_buttonText),
            'field'         => $this->_otpType === $this->_typePhoneTag ? $this->_phoneKey : "user_email",
            'imgURL'        => MOV_LOADER_URL,
        ));
        wp_enqueue_script( 'movum' );
    }


    
    function isPhoneVerificationEnabled()
    {
        $otpberType = $this->getVerificationType();
        return $otpberType===VerificationType::PHONE || $otpberType===VerificationType::BOTH;
    }


    
    function miniorange_um2_user_registration($user_role,$args)
    {

        $otpVerType = $this->getVerificationType();
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpVerType)) {
            $this->unsetOTPSessionVariables();
            return $user_role;
        } elseif(!SessionUtils::isOTPInitialized($this->_formSessionVar) && $this->_isAjaxForm) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::PLEASE_VALIDATE),MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            MoUtility::initialize_transaction($this->_formSessionVar);
            $args = $this->extractArgs($args);
            $this->startOtpTransaction(
                $args["user_login"],
                $args["user_email"],
                new WP_Error(),
                $args[$this->_phoneKey],
                $args["user_password"],
                null
            );
        }
        return $user_role;
    }

    
    private function extractArgs($args)
    {
        return [
            "user_login"    => $args['user_login'],
            "user_email"    => $args['user_email'],
            $this->_phoneKey => $args[$this->_phoneKey],
            "user_password" => $args['user_password']
        ];
    }


    
    function miniorange_um_user_registration($args)
    {

        $errors = new WP_Error();
        MoUtility::initialize_transaction($this->_formSessionVar);
        foreach ($args as $key => $value)
        {
            if($key=="user_login")
                $username = $value;
            elseif ($key=="user_email")
                $email = $value;
            elseif ($key=="user_password")
                $password = $value;
            elseif ($key == $this->_phoneKey)
                $phone_number = $value;
            else
                $extra_data[$key]=$value;
        }
        $this->startOtpTransaction($username,$email,$errors,$phone_number,$password,$extra_data);
    }


    
    function startOtpTransaction($username,$email,$errors,$phone_number,$password,$extra_data)
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::PHONE,$password,$extra_data);
        elseif(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::BOTH,$password,$extra_data);
        else
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::EMAIL,$password,$extra_data);
    }


    
    function miniorange_um2_phone_validation($args)
    {

        $form = UM()->form();
        foreach ($args as $key => $value)
        {
            if($this->_isAjaxForm && $key === $this->_formKey){
                $this->checkIntegrityAndValidateOTP($form,$value,$args);
            } elseif ($key===$this->_phoneKey) {
                $this->processPhoneNumbers($value,$key,$form);
            }
        }
    }


    
    private function processPhoneNumbers($value,$key,$form=null)
    {
        
        global $phoneLogic;
        if (!MoUtility::validatePhoneNumber($value)) {
            $message = str_replace("##phone##", $value, $phoneLogic->_get_otp_invalid_format_message());
            $form->add_error($key, $message);
        }
        $this->checkDuplicates($value,$key,$form);
    }


    
    private function checkDuplicates($value,$key,$form=null)
    {
        if($this->_restrictDuplicates && $this->isPhoneNumberAlreadyInUse($value,$key)) {
            $message = MoMessages::showMessage(MoMessages::PHONE_EXISTS);
            if($this->_isAjaxForm && SessionUtils::isOTPInitialized($this->_formSessionVar)){
                wp_send_json(MoUtility::createJson($message,MoConstants::ERROR_JSON_TYPE));
            } else {
                $form->add_error($key, $message);
            }
        }
    }


    
    private function checkIntegrityAndValidateOTP($form,$value,array $args)
    {

        $otpVerType = $this->getVerificationType();
        if (!SessionUtils::isOTPInitialized($this->_formSessionVar)) {
            $form->add_error($this->_formKey, MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE));
            return;
        }
        $this->checkIntegrity($form,$args,$otpVerType);
        $this->validateChallenge($otpVerType,NULL,$value);
        if(!SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpVerType)) {
            $form->add_error($this->_formKey,MoUtility::_get_invalid_otp_method());
        }
    }


    
    private function checkIntegrity($umForm,array $args,$otpVerType)
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0) {
            if(!SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,$args[$this->_phoneKey])) {
                $umForm->add_error($this->_formKey, MoMessages::showMessage(MoMessages::PHONE_MISMATCH));
            }
        } else if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0) {
            if(!SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,$args['user_email'])) {
                $umForm->add_error($this->_formKey, MoMessages::showMessage(MoMessages::EMAIL_MISMATCH));
            }
        }
    }


    
    function miniorange_um_phone_validation($args)
    {
        global $ultimatemember;
        foreach ($args as $key => $value)
        {
            if($this->_isAjaxForm && $key === $this->_formKey){
                $this->checkIntegrityAndValidateOTP($ultimatemember->form,$value,$args);
            } elseif ($key===$this->_phoneKey) {
                $this->processPhoneNumbers($value,$key,$ultimatemember->form);
            }
        }
    }

    
    function isPhoneNumberAlreadyInUse($phone,$key)
    {
        global $wpdb;
        MoUtility::processPhoneNumber($phone);
        $q = "SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$key' AND `meta_value` =  '$phone'";
        $results = $wpdb->get_row($q);
        return !MoUtility::isBlank($results);
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        $otpVerType = $this->getVerificationType();
        $fromBoth = $otpVerType===VerificationType::BOTH ? TRUE : FALSE;
        if(!$this->_isAjaxForm) {
            miniorange_site_otp_validation_form(
                $user_login,$user_email,$phone_number, MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth
            );
        }
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        if(!function_exists('is_plugin_active')) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if($this->isUltimateMemberV2Installed()) {
            SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
        }else{
            $this->register_ultimateMember_user($user_login,$user_email,$password,$phone_number,$extra_data);
        }
    }


    
    function register_ultimateMember_user($user_login,$user_email,$password,$phone_number,$extra_data)
    {
        $args = array();
        $args['user_login'] = $user_login;
        $args['user_email'] = $user_email;
        $args['user_password'] = $password;
        $args = array_merge($args,$extra_data);
        $user_id = wp_create_user( $user_login,$password, $user_email );
        $this->unsetOTPSessionVariables();
        do_action('um_after_new_user_register', $user_id, $args);
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

        $this->_isFormEnabled = $this->sanitizeFormPOST('um_default_enable');
        $this->_otpType = $this->sanitizeFormPOST('um_enable_type');
        $this->_restrictDuplicates = $this->sanitizeFormPOST('um_restrict_duplicates');
        
        $this->_isAjaxForm = $this->sanitizeFormPOST('um_is_ajax_form');
        $this->_buttonText = $this->sanitizeFormPOST('um_button_text');
        $this->_formKey = $this->sanitizeFormPOST('um_verify_meta_key');
        $this->_phoneKey = $this->sanitizeFormPOST('um_phone_key');

        if($this->basicValidationCheck(BaseMessages::UM_CHOOSE)) {
            update_mo_option('um_phone_key', $this->_phoneKey);
            update_mo_option('um_default_enable',$this->_isFormEnabled);
            update_mo_option('um_enable_type',$this->_otpType);
            update_mo_option('um_restrict_duplicates',$this->_restrictDuplicates);
            update_mo_option('um_is_ajax_form',$this->_isAjaxForm);
            update_mo_option('um_button_text',$this->_buttonText);
            update_mo_option('um_verify_meta_key',$this->_formKey);
        }
    }
}


