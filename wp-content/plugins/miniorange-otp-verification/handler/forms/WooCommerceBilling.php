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
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;


class WooCommerceBilling extends FormHandler implements IFormHandler
{
    use Instance;

    function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::WC_BILLING;
        $this->_typePhoneTag = 'mo_wcb_phone_enable';
        $this->_typeEmailTag = 'mo_wcb_email_enable';
                $this->_phoneFormId = '#billing_phone';
        $this->_formKey = 'WC_BILLING_FORM';
        $this->_formName = mo_("Woocommerce Billing Address Form");
        $this->_isFormEnabled = get_mo_option('wc_billing_enable');
        $this->_buttonText = get_mo_option("wc_billing_button_text");
        $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_("Click Here to send OTP");
        $this->_formDocuments = MoOTPDocs::WC_BILLING_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_restrictDuplicates = get_mo_option('wc_billing_restrict_duplicates');
        $this->_otpType = get_mo_option('wc_billing_type_enabled');
        if($this->_otpType===$this->_typeEmailTag) {
            add_filter('woocommerce_process_myaccount_field_billing_email', [$this, '_wc_user_account_update'], 99, 1);
        } else {
            add_filter('woocommerce_process_myaccount_field_billing_phone', [$this, '_wc_user_account_update'], 99, 1);
        }
    }


    
    function _wc_user_account_update($value)
    {

        $value = $this->_otpType===$this->_typePhoneTag ? MoUtility::processPhoneNumber($value) : $value ;
        $type = $this->getVerificationType();

                if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$type)) {
            $this->unsetOTPSessionVariables();
            return $value;
        }

        if($this->userHasNotChangeData($value)) return $value;

        if($this->_restrictDuplicates && $this->isDuplicate($value,$type)) {
            return $value;
        }

        MoUtility::initialize_transaction($this->_formSessionVar);
        $this->sendChallenge(null,sanitize_email($_POST['billing_email']),null,sanitize_text_field($_POST['billing_phone']),$type);
        return $value;
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
        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }



    
    private function userHasNotChangeData($value)
    {
        $data = $this->getUserData();
        return strcasecmp($data,$value)==0;
    }

    
    private function getUserData()
    {
        global $wpdb;
        $current_user = wp_get_current_user();
        $key = ($this->_otpType===$this->_typePhoneTag) ? 'billing_phone' : 'billing_email';
        $q = "SELECT meta_value FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$key' AND `user_id` = $current_user->ID";
        $results = $wpdb->get_row($q);
        return isset($results) ? $results->meta_value : '';

    }

    
    private function isDuplicate($value,$type)
    {
        global $wpdb;
        $key='billing_'.$type;
        $results = $wpdb->get_row("SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$key' AND `meta_value` =  '$value'");

        if(isset($results)) {
            if($type===VerificationType::PHONE ) {
                wc_add_notice(MoMessages::showMessage(MoMessages::PHONE_EXISTS), MoConstants::ERROR_JSON_TYPE);
            } else if($type===VerificationType::EMAIL) {
                wc_add_notice(MoMessages::showMessage(MoMessages::EMAIL_EXISTS), MoConstants::ERROR_JSON_TYPE);
            }
            return TRUE;
        }
        return FALSE;
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId, $this->_formSessionVar]);
    }

    
    public function getPhoneNumberSelector($selector)
    {

        if($this->_isFormEnabled && ($this->_otpType == $this->_typePhoneTag)) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if (!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;
        $this->_isFormEnabled = $this->sanitizeFormPOST('wc_billing_enable');
        $this->_otpType = $this->sanitizeFormPOST('wc_billing_type_enabled');
        $this->_restrictDuplicates = $this->sanitizeFormPOST('wc_billing_restrict_duplicates');

        if ($this->basicValidationCheck(BaseMessages::WC_BILLING_CHOOSE)) {
            update_mo_option('wc_billing_enable', $this->_isFormEnabled);
            update_mo_option('wc_billing_type_enabled', $this->_otpType);
            update_mo_option('wc_billing_restrict_duplicates', $this->_restrictDuplicates);
        }
    }
}