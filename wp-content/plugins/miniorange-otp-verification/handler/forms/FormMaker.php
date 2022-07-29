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


class FormMaker extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::FORM_MAKER;
        $this->_typePhoneTag = 'mo_form_maker_phone_enable';
        $this->_typeEmailTag = 'mo_form_maker_email_enable';
        $this->_formName = mo_('Form Maker Form');
        $this->_formKey = 'FORM_MAKER';
        $this->_isFormEnabled = get_mo_option('formmaker_enable');
        $this->_otpType = get_mo_option('formmaker_enable_type');
        $this->_formDetails = maybe_unserialize(get_mo_option('formmaker_otp_enabled'));
        $this->_buttonText = get_mo_option("formmaker_button_text");
        $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_("Click Here to send OTP");
        $this->_formDocuments = MoOTPDocs::FORMMAKER;
        parent::__construct();

        if($this->_isFormEnabled){
            add_action( 'wp_enqueue_scripts', array($this , 'register_fm_button_script'));
        }
    }

    
    function handleForm()
    {
        $this->routeData();
    }

    
    function routeData()
    {
        if(!array_key_exists('option', $_GET)) return;
        switch (trim($_GET['option']))
        {
            case "miniorange-fm-ajax-verify":
                $this->_send_otp_fm_ajax_verify($_POST);		break;
            case "miniorange-fm-verify-code":
                $this->_validate_otp($_POST);                   break;
        }
    }

    
    private function _validate_otp($post)
    {
        $this->validateChallenge($this->getVerificationType(),NULL,sanitize_text_field($post['otp_token']));
    }

    
    function _send_otp_fm_ajax_verify($data)
    {

        if ( $this->_otpType == $this->_typePhoneTag)
            $this->_send_fm_ajax_otp_to_phone($data);
        else
            $this->_send_fm_ajax_otp_to_email($data);
    }

    
    function _send_fm_ajax_otp_to_phone($data)
    {
        if(!MoUtility::sanitizeCheck('user_phone',$data)) {
            wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $this->sendOTP(trim($data['user_phone']), NULL, trim($data['user_phone']), VerificationType::PHONE);
        }
    }

    
    function _send_fm_ajax_otp_to_email($data)
    {
        if(!MoUtility::sanitizeCheck('user_email',$data)) {
            wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $this->sendOTP($data['user_email'], $data['user_email'], NULL, VerificationType::EMAIL);
        }
    }


    
    private function checkPhoneOrEmailIntegrity($fieldValue)
    {
        if($this->getVerificationType()===VerificationType::PHONE) {
            return SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar, $fieldValue);
        } else {
            return SessionUtils::isEmailVerifiedMatch($this->_formSessionVar, $fieldValue);
        }
    }


    
    private function sendOTP($sessionValue, $userEmail, $phoneNumber, $otpType)
    {
        MoUtility::initialize_transaction($this->_formSessionVar);
        if($otpType===VerificationType::PHONE) {
            SessionUtils::addPhoneVerified($this->_formSessionVar, $sessionValue);
        } else {
            SessionUtils::addEmailVerified($this->_formSessionVar, $sessionValue);
        }
        $this->sendChallenge('',$userEmail,NULL,$phoneNumber,$otpType);
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        wp_send_json(MoUtility::createJson(MoUtility::_get_invalid_otp_method(), MoConstants::ERROR_JSON_TYPE));
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        if ($this->checkPhoneOrEmailIntegrity(sanitize_text_field($_POST['sub_field']))) {
            $this->unsetOTPSessionVariables();
            wp_send_json(MoUtility::createJson(self::VALIDATED, MoConstants::SUCCESS_JSON_TYPE));
        } else if( $this->_otpType == $this->_typePhoneTag ) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::PHONE_MISMATCH), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::EMAIL_MISMATCH), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }

    
    function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }


    
    public function getPhoneNumberSelector($selector)
    {
        if($this->isFormEnabled() &&  $this->getVerificationType()===VerificationType::PHONE) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function register_fm_button_script()
    {
        wp_register_script( 'fmotpbuttonscript', MOV_URL . 'includes/js/formmaker.min.js',array('jquery') );
        wp_localize_script( 'fmotpbuttonscript', 'mofmvar', array(
            'siteURL' 		=> 	site_url(),
            'otpType'       =>  $this->_otpType,
            'formDetails'   =>  $this->_formDetails,
            'buttontext'    =>  mo_($this->_buttonText),
            'imgURL'        =>  MOV_URL. "includes/images/loader.gif",
        ) );
        wp_enqueue_script( 'fmotpbuttonscript' );
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $form = $this->parseFormDetails();

        $this->_formDetails = !empty($form) ? $form : "";
        $this->_otpType = $this->sanitizeFormPOST('fm_enable_type');
        $this->_isFormEnabled = $this->sanitizeFormPOST('fm_enable');
        $this->_buttonText = $this->sanitizeFormPOST('fm_button_text');

        if($this->basicValidationCheck(BaseMessages::FORMMAKER_CHOOSE)) {
            update_mo_option('formmaker_enable', $this->_isFormEnabled);
            update_mo_option('formmaker_enable_type', $this->_otpType);
            update_mo_option('formmaker_otp_enabled', maybe_serialize($this->_formDetails));
            update_mo_option('formmaker_button_text', $this->_buttonText);
        }
    }


    
    private function parseFormDetails()
    {
        $form = array();
        if(!array_key_exists('formmaker_form',$_POST)) return array();

        foreach (array_filter($_POST['formmaker_form']['form']) as $key => $value)
        {
            $value = sanitize_text_field($value);
            $form[$value]= array(
                'emailkey'      => $this->_get_efield_id(sanitize_text_field($_POST['formmaker_form']['emailkey'][$key]),$value),
                'phonekey'      => $this->_get_efield_id(sanitize_text_field($_POST['formmaker_form']['phonekey'][$key]),$value),
                'verifyKey'     => $this->_get_efield_id(sanitize_text_field($_POST['formmaker_form']['verifyKey'][$key]),$value),
                'phone_show'    => sanitize_text_field($_POST['formmaker_form']['phonekey'][$key]),
                'email_show'    => sanitize_text_field($_POST['formmaker_form']['emailkey'][$key]),
                'verify_show'   => sanitize_text_field($_POST['formmaker_form']['verifyKey'][$key])
            );
        }

        return $form;
    }


    
    private function _get_efield_id($label,$form)
    {
        global $wpdb;
        $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}formmaker where `id` =".$form);

        if(MoUtility::isBlank($row)) return '';

        $fields = explode('*:*new_field*:*', $row->form_fields);
        $ids = $types = $labels = array();

        foreach ( $fields as $field ) {
            $temp = explode('*:*id*:*', $field);
            if(!MoUtility::isBlank($temp)) {
                array_push($ids, $temp[0]);
                if(array_key_exists(1,$temp)) {
                    $temp = explode('*:*type*:*', $temp[1]);
                    array_push($types, $temp[0]);
                    $temp = explode('*:*w_field_label*:*', $temp[1]);
                }
                array_push($labels, $temp[0]);
            }
        }
        $key = array_search($label,$labels);
        return "#wdform_".$ids[$key]."_element".$form;
    }
}