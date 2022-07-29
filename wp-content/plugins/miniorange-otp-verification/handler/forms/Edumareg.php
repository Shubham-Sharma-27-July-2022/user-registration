<?php

namespace OTP\Handler\Forms;

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
use \WP_Error;


class Edumareg extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::EDUMAREG;
        $this->_typePhoneTag = 'mo_edumareg_phone_enable';
        $this->_typeEmailTag = 'mo_edumareg_email_enable';
        $this->_phoneKey = 'telephone';
        $this->_formKey = 'EDUMAREG_THEME';
        $this->_formName = mo_('Eduma Theme Registration Form');
        $this->_isFormEnabled = get_mo_option('edumareg_enable');
        $this->_phoneFormId = "#phone_number_mo";
        $this->_formDocuments = MoOTPDocs::EDUMA_REG;
        parent::__construct();
    }

    
    function handleForm()
    {   
        $this->_otpType = get_mo_option('edumareg_enable_type');
        add_action('register_form', array($this,'miniorange_add_phonefield'));
        add_action('user_register', array($this,'miniorange_registration_save'), 10, 1 );
        add_filter('registration_errors', array($this,'miniorange_site_registration_errors'), 99, 3 );
    }

    
    function miniorange_add_phonefield()
    {
        echo '<input type="hidden" name="register_nonce" value="register_nonce"/>';
        if($this->_otpType === $this->_typePhoneTag) {
            echo '<p><input type="text" name="phone_number_mo" id="phone_number_mo" class="input required" value="" placeholder="Phone Number" style=""/></p>';
        }
    }

    function miniorange_registration_save($user_id)
    {
        $phoneNumber = MoPHPSessions::getSessionVar('phone_number_mo');
        if ($phoneNumber) {
            add_user_meta($user_id, $this->_phoneKey,$phoneNumber);
        }
    }

    function miniorange_site_registration_errors(WP_Error $errors, $sanitized_user_login, $user_email )
    {
        $phone_number = isset($_POST['phone_number_mo'])? sanitize_text_field($_POST['phone_number_mo']) : null;
        $this->checkIfPhoneNumberUnique($errors,$phone_number);

        if(!empty($errors->errors)) return $errors;
        if(!$this->_otpType) return $errors;

        return $this->startOTPTransaction($sanitized_user_login,$user_email,$errors,$phone_number);
    }

    private function checkIfPhoneNumberUnique(WP_Error &$errors,$phone_number)
    {
        if(strcasecmp($this->_otpType,$this->_typeEmailTag)===0) return;
        
        if(MoUtility::isBlank($phone_number) || !MoUtility::validatePhoneNumber($phone_number)){

            $errors->add( 'invalid_phone', MoMessages::showMessage(MoMessages::ENTER_PHONE_DEFAULT) );
        }
    }

    function isPhoneNumberAlreadyInUse($phone, $key)
    {
        global $wpdb;
        $phone = MoUtility::processPhoneNumber($phone);
        $results = $wpdb->get_row("SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$key' AND `meta_value` =  '$phone'");
        return !MoUtility::isBlank($results);
    }

    function startOTPTransaction($sanitized_user_login,$user_email,$errors,$phone_number)
    {
        if(!MoUtility::isBlank(array_filter($errors->errors)) || !isset($_POST['register_nonce'])) return $errors;

        MoUtility::initialize_transaction($this->_formSessionVar);
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0)
            $this->sendChallenge($sanitized_user_login,$user_email,$errors,$phone_number,VerificationType::PHONE);
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

        $this->_otpType = $this->sanitizeFormPOST('edumareg_enable_type');
        $this->_isFormEnabled = $this->sanitizeFormPOST('edumareg_enable');

        update_mo_option('edumareg_enable',$this->_isFormEnabled);
        update_mo_option('edumareg_enable_type', $this->_otpType);
    }
}