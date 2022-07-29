<?php

namespace OTP\Handler\Forms;

use OTP\Handler\PhoneVerificationLogic;
use OTP\Helper\FormSessionVars;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\BaseMessages;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;
use \WP_Error;
use \BP_Signup;
use \WP_User;


class BuddyPressRegistrationForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::BUDDYPRESS_REG;
        $this->_typePhoneTag = 'mo_bbp_phone_enable';
        $this->_typeEmailTag = 'mo_bbp_email_enable';
        $this->_typeBothTag = 'mo_bbp_both_enabled';
        $this->_formKey = 'BP_DEFAULT_FORM';
        $this->_formName = mo_('BuddyPress / BuddyBoss Registration Form');
        $this->_isFormEnabled = get_mo_option('bbp_default_enable');
        $this->_formDocuments = MoOTPDocs::BBP_FORM_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_phoneKey = get_mo_option('bbp_phone_key');
        $this->_otpType = get_mo_option('bbp_enable_type');
        $this->_disableAutoActivate = get_mo_option('bbp_disable_activation');
        $this->_phoneFormId = 'input[name=field_'.$this->moBBPgetphoneFieldId().']';
        $this->_restrictDuplicates = get_mo_option('bbp_restrict_duplicates');

        add_filter( 'bp_registration_needs_activation'	, array($this,'fix_signup_form_validation_text'));
        add_filter( 'bp_core_signup_send_activation_key', array($this,'disable_activation_email'));
        add_filter( 'bp_signup_usermeta', array($this,'miniorange_bp_user_registration'),1,1);
        add_action( 'bp_signup_validate', array($this,'validateOTPRequest'), 99,0);

        if($this->_disableAutoActivate) {
            add_action('bp_core_signup_user', array($this, 'mo_activate_bbp_user'), 1, 5);
        }
    }

    
    function fix_signup_form_validation_text()
    {
        return $this->_disableAutoActivate ? FALSE : TRUE;
    }


    
    function disable_activation_email()
    {
        return $this->_disableAutoActivate ? FALSE : TRUE;
    }


    
    function isPhoneVerificationEnabled()
    {
        $otpType = $this->getVerificationType();
        return $otpType===VerificationType::PHONE || $otpType===VerificationType::BOTH;
    }


    
    function validateOTPRequest()
    {
        
        global $bp,$phoneLogic;
        $field_key = "field_".$this->moBBPgetphoneFieldId();
        if(isset($_POST[$field_key]) && !MoUtility::validatePhoneNumber($_POST[$field_key])) {
            $bp->signup->errors[$field_key] = str_replace(
                "##phone##",  sanitize_text_field($_POST[$field_key]), $phoneLogic->_get_otp_invalid_format_message()
            );
        }else if($this->isPhoneNumberAlreadyInUse(sanitize_text_field($_POST[$field_key]))){
            $bp->signup->errors[$field_key] = mo_("Phone number already in use. Please Enter a different Phone number.");
        }
    }

    
    function isPhoneNumberAlreadyInUse($phone)
    {
        if($this->_restrictDuplicates) {
            global $wpdb;
            $phone = MoUtility::processPhoneNumber($phone);
            $field_key = $this->moBBPgetphoneFieldId();
            $results = $wpdb->get_row("SELECT `user_id` FROM `{$wpdb->prefix}bp_xprofile_data` WHERE `field_id` = '$field_key' AND `value` =  '$phone'");
            return !MoUtility::isBlank($results);
        }
        return false;
    }

    
    function checkIfVerificationIsComplete()
    {
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType()))
        {
            $this->unsetOTPSessionVariables();
            return TRUE;
        }
        return FALSE;
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {
        $otpVerType = $this->getVerificationType();
        $fromBoth = VerificationType::BOTH === $otpVerType ? TRUE : FALSE;
        miniorange_site_otp_validation_form(
            $user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth
        );
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }


    
    function miniorange_bp_user_registration($usermeta)
    {

        if($this->checkIfVerificationIsComplete()) return $usermeta;
        MoUtility::initialize_transaction($this->_formSessionVar);
        $errors = new WP_Error();
        $phone_number = NULL;

        foreach ($_POST as $key => $value)
        {
            if($key==="signup_username")
                $username = $value;
            elseif ($key==="signup_email")
                $email = $value;
            elseif ($key==="signup_password")
                $password = $value;
            else
                $extra_data[$key]=$value;
        }

        $reg1 = $this->moBBPgetphoneFieldId();

        if(isset($_POST["field_".$reg1])) $phone_number = sanitize_text_field($_POST["field_".$reg1]);

        $extra_data['usermeta'] = $usermeta;
        $this->startVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data);
        return $usermeta;
    }


    
    function startVerificationProcess($username,$email,$errors,$phone_number,$password,$extra_data)
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0)
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::PHONE,$password,$extra_data);
        else if(strcasecmp($this->_otpType,$this->_typeBothTag)===0)
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::BOTH,$password,$extra_data);
        else
            $this->sendChallenge($username,$email,$errors,$phone_number,VerificationType::EMAIL,$password,$extra_data);
    }


    
    function mo_activate_bbp_user($userID,$user_login)
    {
        $activation_key = $this->moBBPgetActivationKey($user_login);
        bp_core_activate_signup($activation_key);
        BP_Signup::validate($activation_key);
        $u = new WP_User( $userID );
        $u->add_role( 'subscriber' );
        return;
    }


    
    function moBBPgetActivationKey($user_login)
    {
        global $wpdb;
        return $wpdb->get_var( "SELECT activation_key FROM {$wpdb->prefix}signups WHERE active = '0' AND user_login = '".$user_login."'");
    }


    
    function moBBPgetphoneFieldId()
    {
        global $wpdb;
        return $wpdb->get_var("SELECT id FROM {$wpdb->prefix}bp_xprofile_fields where name ='".$this->_phoneKey."'");
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_formSessionVar,$this->_txSessionId]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && $this->isPhoneVerificationEnabled()) array_push($selector, $this->_phoneFormId);
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('bbp_default_enable');
        $this->_disableAutoActivate = $this->sanitizeFormPOST('bbp_disable_activation');
        $this->_otpType = $this->sanitizeFormPOST('bbp_enable_type');
        $this->_phoneKey = $this->sanitizeFormPOST('bbp_phone_key');
        $this->_restrictDuplicates = $this->sanitizeFormPOST('bbp_restrict_duplicates');
        if($this->basicValidationCheck(BaseMessages::BP_CHOOSE)) {
            update_mo_option('bbp_default_enable', $this->_isFormEnabled);
            update_mo_option('bbp_disable_activation', $this->_disableAutoActivate);
            update_mo_option('bbp_enable_type', $this->_otpType);
            update_mo_option('bbp_restrict_duplicates', $this->_restrictDuplicates);
            update_mo_option('bbp_phone_key', $this->_phoneKey);
        }
    }
}