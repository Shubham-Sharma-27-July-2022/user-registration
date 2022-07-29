<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoMessages;
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


class DefaultWordPressRegistrationForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::WP_DEFAULT_REG;
        $this->_phoneKey = 'telephone';
        $this->_phoneFormId = "#phone_number_mo";
        $this->_formKey = 'WP_DEFAULT';
        $this->_typePhoneTag = "mo_wp_default_phone_enable";
        $this->_typeEmailTag = "mo_wp_default_email_enable";
        $this->_typeBothTag = 'mo_wp_default_both_enable';
        $this->_formName = mo_("WordPress Default / TML Registration Form");
        $this->_isFormEnabled = get_mo_option('wp_default_enable');
        $this->_formDocuments = MoOTPDocs::WP_DEFAULT_FORM_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('wp_default_enable_type');
        $this->_disableAutoActivate = get_mo_option('wp_reg_auto_activate') ? FALSE : TRUE;
        $this->_restrictDuplicates = get_mo_option('wp_reg_restrict_duplicates');

        add_action('register_form', array($this,'miniorange_site_register_form'));
        add_filter('registration_errors', array($this,'miniorange_site_registration_errors'), 99, 3 );
        add_action('admin_post_nopriv_validation_goBack', array($this,'_handle_validation_goBack_action'));
        add_action('user_register', array($this,'miniorange_registration_save'), 10, 1 );
        add_filter('wp_login_errors', array($this, 'miniorange_custom_reg_message'), 10, 2 );
        if(!$this->_disableAutoActivate){
            remove_action('register_new_user','wp_send_new_user_notifications');
        }
    }


    
    function isPhoneVerificationEnabled()
    {
        $otpType = $this->getVerificationType();
        return $otpType===VerificationType::PHONE || $otpType===VerificationType::BOTH;
    }


    
    function miniorange_custom_reg_message(WP_Error $errors,$redirect_to)
    {
        if(!$this->_disableAutoActivate) {
            if(in_array('registered',$errors->get_error_codes())) {
                $errors->remove('registered');
                $errors->add('registered',mo_("Registration Complete."),'message');
            }
        }
        return $errors;
    }


    
    function miniorange_site_register_form()
    {
        echo '<input type="hidden" name="register_nonce" value="register_nonce"/>';
        if($this->isPhoneVerificationEnabled()) {
            echo '<label for="phone_number_mo">' . mo_("Phone Number") . '<br />
                <input type="text" name="phone_number_mo" id="phone_number_mo" class="input" value="" style=""/></label>';
        }
        if(!$this->_disableAutoActivate) {
            echo '<label for="password_mo">' . mo_("Password") . '<br />
                <input type="password" name="password_mo" id="password_mo" class="input" value="" style=""/></label>';
            echo '<label for="confirm_password_mo">' . mo_("Confirm Password") . '<br />
                <input type="password" name="confirm_password_mo" id="confirm_password_mo" class="input" value="" style=""/></label>';
            echo '<script>window.onload=function(){ document.getElementById("reg_passmail").remove(); }</script>';
        }
    }


    
    function miniorange_registration_save($user_id)
    {
        $phoneNumber = MoPHPSessions::getSessionVar('phone_number_mo');
        if ($phoneNumber) {
            add_user_meta($user_id, $this->_phoneKey,$phoneNumber);
        }
        if(!$this->_disableAutoActivate) {
            wp_set_password(sanitize_text_field($_POST['password_mo']),$user_id);
            update_user_option( $user_id, 'default_password_nag', false, true );
        }
    }


    
    function miniorange_site_registration_errors(WP_Error $errors, $sanitized_user_login, $user_email )
    {
        $phone_number = isset($_POST['phone_number_mo'])? sanitize_text_field($_POST['phone_number_mo']) : null;
        $password = isset($_POST['password_mo'])? sanitize_text_field($_POST['password_mo']) : null;
        $confirmPass = isset($_POST['confirm_password_mo'])? sanitize_text_field($_POST['confirm_password_mo']) : null;
        $this->checkIfPhoneNumberUnique($errors,$phone_number);
        $this->validatePasswords($errors,$password,$confirmPass);

        if(!empty($errors->errors)) return $errors;
        if(!$this->_otpType) return $errors;

        return $this->startOTPTransaction($sanitized_user_login,$user_email,$errors,$phone_number);
    }


    
    private function validatePasswords(WP_Error &$error,$password,$confirmPass)
    {
        if($this->_disableAutoActivate) return;
        if(strcasecmp($password,$confirmPass)!==0) {
            $error->add('password_mismatch',MoMessages::showMessage(MoMessages::PASS_MISMATCH));
        }
    }


    
    private function checkIfPhoneNumberUnique(WP_Error &$errors,$phone_number)
    {
        if(strcasecmp($this->_otpType,$this->_typeEmailTag)===0) return;
        
        if(MoUtility::isBlank($phone_number) || !MoUtility::validatePhoneNumber($phone_number))
            $errors->add( 'invalid_phone', MoMessages::showMessage(MoMessages::ENTER_PHONE_DEFAULT) );
        elseif($this->_restrictDuplicates && $this->isPhoneNumberAlreadyInUse(trim($phone_number),$this->_phoneKey))
            $errors->add( 'invalid_phone', MoMessages::showMessage(MoMessages::PHONE_EXISTS) );
    }


    
    function startOTPTransaction($sanitized_user_login,$user_email,$errors,$phone_number)
    {
        if(!MoUtility::isBlank(array_filter($errors->errors)) || !isset($_POST['register_nonce'])) return $errors;

        MoUtility::initialize_transaction($this->_formSessionVar);
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0)
            $this->sendChallenge($sanitized_user_login,$user_email,$errors,$phone_number,VerificationType::PHONE);
        else if(strcasecmp($this->_otpType,$this->_typeBothTag)===0)
            $this->sendChallenge($sanitized_user_login,$user_email,$errors,$phone_number,VerificationType::BOTH);
        else
            $this->sendChallenge($sanitized_user_login,$user_email,$errors,$phone_number,VerificationType::EMAIL);
        return $errors;
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        $otpVerType = $this->getVerificationType();
        $fromBoth = $otpVerType===VerificationType::BOTH ? TRUE : FALSE;
        miniorange_site_otp_validation_form(
            $user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth
        );
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {
        $this->unsetOTPSessionVariables();
    }

    
    function isPhoneNumberAlreadyInUse($phone, $key)
    {
        global $wpdb;
        $phone = MoUtility::processPhoneNumber($phone);
        $results = $wpdb->get_row("SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$key' AND `meta_value` =  '$phone'");
        return !MoUtility::isBlank($results);
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

        $this->_isFormEnabled = $this->sanitizeFormPOST('wp_default_enable');
        $this->_otpType = $this->sanitizeFormPOST('wp_default_enable_type');
        $this->_restrictDuplicates = $this->sanitizeFormPOST('wp_reg_restrict_duplicates');
        $this->_disableAutoActivate = $this->sanitizeFormPOST('wp_reg_auto_activate') ? FALSE : TRUE;

        update_mo_option('wp_default_enable', $this->_isFormEnabled);
        update_mo_option('wp_default_enable_type', $this->_otpType);
        update_mo_option('wp_reg_restrict_duplicates', $this->_restrictDuplicates);
        update_mo_option('wp_reg_auto_activate', $this->_disableAutoActivate ? FALSE : TRUE);
    }
}