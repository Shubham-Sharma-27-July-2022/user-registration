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
use WP_Error;


class WcProfileForm extends FormHandler implements IFormHandler
{
    use Instance;

    
    private $_verifyFieldKey;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::WC_PROFILE_UPDATE;
        $this->_typePhoneTag = 'mo_wc_profile_phone_enable';
        $this->_typeEmailTag = 'mo_wc_profile_email_enable';
        $this->_formKey = 'WC_AC_FORM';
        $this->_verifyFieldKey = 'verify_field';
        $this->_formName = mo_("WooCommerce Account Details Form");
        $this->_isFormEnabled = get_mo_option('wc_profile_enable');
        $this->_restrictDuplicates = get_mo_option('wc_profile_restrict_duplicates');
        $this->_buttonText = get_mo_option("wc_profile_button_text");
        $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_("Click Here to send OTP");
        $this->_phoneKey =  get_mo_option('wc_profile_phone_key');
        $this->_phoneKey = $this->_phoneKey ? $this->_phoneKey : "billing_phone";
        $this->_phoneFormId= "#billing_phone";
        $this->_generateOTPAction = 'miniorange_wc_ac_otp';
        parent::__construct();
    }


    
    public function handleForm()
    {   
        $this->_otpType = get_mo_option('wc_profile_enable_type');
         add_action( 'woocommerce_edit_account_form',array($this, 'mo_add_phone_field_account_form'));
         add_action("wp_ajax_{$this->_generateOTPAction}", [$this,'startOtpVerificationProcess']);
         add_action("wp_ajax_nopriv_{$this->_generateOTPAction}", [$this,'startOtpVerificationProcess']);
         add_action( "woocommerce_save_account_details_errors",  [$this,'verifyOtpEntered'],10,1 ); 
         add_action('wp_enqueue_scripts',array($this, 'miniorange_wc_ac_script'));
    }

    function verifyOtpEntered($errors){
        
        $verificationkey =  strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? "billing_phone" : "account_email";

        if($this->getUserData($this->_phoneKey) !== sanitize_text_field($_POST[$verificationkey])){
            $this->checkIfOTPSent($errors);
            if(!empty($errors->errors)) return $errors;
            $this->checkIntegrityAndValidateOTP($errors);
        }else{
            return;
        }
    }

      function checkIfOTPSent($errors)
    {   
         if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) {
        $errors->add( 'billing_user_need_to_verify_error', MoMessages::showMessage(MoMessages::PLEASE_VALIDATE));
        return $errors;
        }
    }

     function checkIntegrityAndValidateOTP($errors)
    {  
        $this->checkIntegrity($errors);
        $this->validateChallenge($this->getVerificationType(),NULL,sanitize_text_field($_POST['enter_otp']));
        if(!empty($errors->errors)) return $errors;
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType())) {
            if($this->getVerificationType()===VerificationType::PHONE ) {                
                SessionUtils::addPhoneSubmitted($this->_formSessionVar,sanitize_text_field($_POST['billing_phone']));
                $user_id = get_current_user_id();
               update_user_meta( $user_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
               $this->unsetOTPSessionVariables();
            }
        if($this->getVerificationType()===VerificationType::EMAIL ){
                SessionUtils::addEmailSubmitted($this->_formSessionVar,sanitize_email($_POST['account_email']));
                $user_id = get_current_user_id();
                $updateEmail = array(
                    'ID'         => $user_id,
                    'user_email' => sanitize_email( $_POST['account_email'] )
                   );
                   
                   wp_update_user( $updateEmail );
                   $this->unsetOTPSessionVariables();
            }
        }else{  
               $errors->add( 'billing_invalid_otp_error', MoMessages::showMessage(MoMessages::INVALID_OTP));
                 return $errors;
        }
    }

    function checkIntegrity($errors)
    {      
        if($this->getVerificationType()===VerificationType::PHONE ){
            if(!SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,sanitize_text_field($_POST['billing_phone']))) {
                 $errors->add( 'billing_phone_mismatch_error', MoMessages::showMessage(MoMessages::PHONE_MISMATCH));
                 return $errors;
            }
        }
        if($this->getVerificationType()===VerificationType::EMAIL ){
            if(!SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,sanitize_email($_POST['account_email']))) {
                $errors->add( 'billing_email_mismatch_error', MoMessages::showMessage(MoMessages::EMAIL_MISMATCH));
                return $errors;
           }
        }
    }


    function miniorange_wc_ac_script()
    { 
        wp_register_script( 'mowcac', MOV_URL . 'includes/js/mowcac.min.js',array('jquery') );
        wp_localize_script( 'mowcac', 'mowcac', array(
            'siteURL'       => wp_ajax_url(),
            'otpType'       => $this->_otpType == $this->_typePhoneTag? "phone":"email",
            'nonce'         => wp_create_nonce($this->_nonce),
            'buttontext'    => mo_($this->_buttonText),
            'imgURL'        => MOV_LOADER_URL,
            'generateURL'   => $this->_generateOTPAction,
            'fieldValue'    => $this->getUserData($this->_phoneKey),
            'phoneKey'      => $this->_phoneKey,
        ));
        wp_enqueue_script( 'mowcac' );
    }

    private function getUserData($key)
    {  
        $current_user = wp_get_current_user();
    
        if($this->_otpType == $this->_typePhoneTag){
            global $wpdb;
            $fetch_number_query = "SELECT meta_value FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$key' AND `user_id` = $current_user->ID";
            $results = $wpdb->get_row($fetch_number_query);
            return (isset($results)) ? $results->meta_value : '';
      }else{
        return $current_user->user_email;
      }
    }

     function startOtpVerificationProcess()
    {
        MoUtility::initialize_transaction($this->_formSessionVar);
        if($this->_otpType == $this->_typePhoneTag)
            $this->_processPhoneAndSendOTP($_POST);
        else
            $this->_processEmailAndSendOTP($_POST);
    }

    function _processPhoneAndSendOTP($data)
    {  
        if(!MoUtility::sanitizeCheck('user_input',$data)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $this->checkDuplicates(sanitize_text_field($data['user_input']), $this->_phoneKey);
            SessionUtils::addPhoneVerified($this->_formSessionVar,sanitize_text_field($data['user_input']));
            $this->sendChallenge('',NULL,NULL,sanitize_text_field($data['user_input']),VerificationType::PHONE);
        }
    }

    function _processEmailAndSendOTP($data){
        if(!MoUtility::sanitizeCheck('user_input',$data)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            SessionUtils::addEmailVerified($this->_formSessionVar,sanitize_email($data['user_input']));
            $this->sendChallenge('',sanitize_text_field($data['user_input']),NULL,NULL,VerificationType::EMAIL);
        }
    }

     private function checkDuplicates($value,$key)
    {
        if($this->_restrictDuplicates && $this->isPhoneNumberAlreadyInUse($value,$key)) {
            $message = MoMessages::showMessage(MoMessages::PHONE_EXISTS);
            wp_send_json(MoUtility::createJson($message,MoConstants::ERROR_JSON_TYPE));
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


  function mo_add_phone_field_account_form(){

    woocommerce_form_field(
        'billing_phone',
        array(
            'type'        => 'text',
            'required'    => true, 
            'label'       => 'Phone Number',
        ),
        get_user_meta( get_current_user_id(), 'billing_phone', true ) 
    );

     woocommerce_form_field(
        'enter_otp',
        array(
            'type'        => 'text',
            'required'    => false, 
            'label'       => 'Enter OTP',
        ),
        get_user_meta( get_current_user_id(), 'enter_otp', true ) 
    );
  }
    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }


    
    public function handle_post_verification($redirect_to, $user_login, $user_email, $password, $phone_number,
                                             $extra_data, $otpType)
    {
        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }


    
    public function handle_failed_verification($user_login, $user_email, $phone_number,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VERIFICATION_FAILED,$otpType);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && ($this->_otpType===$this->_typePhoneTag)) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    public function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('wc_profile_enable');
        $this->_otpType =  $this->sanitizeFormPOST('wc_profile_enable_type');
        $this->_buttonText = $this->sanitizeFormPOST('wc_profile_button_text');
        $this->_restrictDuplicates = $this->sanitizeFormPOST('wc_profile_restrict_duplicates');
        $this->_phoneKey =  $this->sanitizeFormPOST('wc_profile_phone_key');

            update_mo_option('wc_profile_enable', $this->_isFormEnabled);
            update_mo_option('wc_profile_enable_type', $this->_otpType);
            update_mo_option('wc_profile_button_text', $this->_buttonText);
            update_mo_option('wc_profile_restrict_duplicates', $this->_restrictDuplicates);
            update_mo_option('wc_profile_phone_key', $this->_phoneKey);
    }
}