<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;
use WP_Error;


class RegistrationMagicForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::CRF_DEFAULT_REG;
        $this->_typePhoneTag = 'mo_crf_phone_enable';
        $this->_typeEmailTag = 'mo_crf_email_enable';
        $this->_typeBothTag = 'mo_crf_both_enable';
        $this->_formKey = 'CRF_FORM';
        $this->_formName = mo_('Custom User Registration Form Builder (Registration Magic)');
        $this->_isFormEnabled = get_mo_option('crf_default_enable');
        $this->_phoneFormId = array();
        $this->_formDocuments = MoOTPDocs::CRF_FORM_ENABLE;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('crf_enable_type');
        $this->_restrictDuplicates = get_mo_option('crf_restrict_duplicates');
        $this->_formDetails =  maybe_unserialize(get_mo_option('crf_otp_enabled'));
        if(empty($this->_formDetails)) return;
        foreach ($this->_formDetails as $key => $value) {
            array_push($this->_phoneFormId,'input[name='.$this->getFieldID($value['phonekey'],$key).']');
        }

        if(!$this->checkIfPromptForOTP()) return;
        $this->_handle_crf_form_submit($_REQUEST);
    }

    
    private function checkIfPromptForOTP()
    {
        if(array_key_exists('option',$_POST) || !array_key_exists('rm_form_sub_id',$_POST)) return FALSE;
        foreach($this->_formDetails as $key => $value) {
            if (strpos(sanitize_text_field($_POST['rm_form_sub_id']), 'form_' . $key . '_') !== FALSE){
                MoUtility::initialize_transaction($this->_formSessionVar);
                SessionUtils::setFormOrFieldId($this->_formSessionVar,$key);
                return TRUE;
            }
        }
        return FALSE;
    }


    
    private function isPhoneVerificationEnabled()
    {
        $otpVerType = $this->getVerificationType();
        return $otpVerType===VerificationType::PHONE || $otpVerType===VerificationType::BOTH;
    }


    
    private function isEmailVerificationEnabled()
    {
        $otpVerType = $this->getVerificationType();
        return $otpVerType===VerificationType::EMAIL || $otpVerType===VerificationType::BOTH;
    }


    
    private function _handle_crf_form_submit($requestData)
    {
        $email = $this->isEmailVerificationEnabled() ? $this->getCRFEmailFromRequest($requestData) : "";
        $phone = $this->isPhoneVerificationEnabled() ? $this->getCRFPhoneFromRequest($requestData) : "";
        $this->miniorange_crf_user($email, isset($requestData['user_name']) ? sanitize_text_field($requestData['user_name']) : NULL ,$phone);
        $this->checkIfValidated();
    }


    
    private function checkIfValidated()
    {
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType())) {
            $this->unsetOTPSessionVariables();
        }
    }


    
    private function getCRFEmailFromRequest($requestData)
    {
        $formId = SessionUtils::getFormOrFieldId($this->_formSessionVar);
        $emailKey = $this->_formDetails[$formId]['emailkey'];
        return $this->getFormPostSubmittedValue($this->getFieldID($emailKey,$formId),$requestData);
    }


    
    private function getCRFPhoneFromRequest($requestData)
    {
        $formId = SessionUtils::getFormOrFieldId($this->_formSessionVar);
        $phonekey = $this->_formDetails[$formId]['phonekey'];
        return $this->getFormPostSubmittedValue($this->getFieldID($phonekey,$formId),$requestData);
    }


    
    private function getFormPostSubmittedValue($reg1, $requestData)
    {
        return isset($requestData[$reg1]) ? $requestData[$reg1] : "";
    }


    
    private function getFieldID($key,$formID)
    {
        global $wpdb;
        $crf_fields =$wpdb->prefix."rm_fields";
        $row1 = $wpdb->get_row("SELECT * FROM $crf_fields where form_id = '".$formID."' and field_label ='".$key."'");
        return isset($row1) ? ($row1->field_type=='Mobile'? 'Textbox' : $row1->field_type) .'_'.$row1->field_id : "null";
    }


    
    private function miniorange_crf_user($user_email,$user_name,$phone_number)
    {

        MoUtility::initialize_transaction($this->_formSessionVar);
        $errors = new WP_Error();
        if($this->isPhoneNumberAlreadyInUse($phone_number))
            miniorange_site_otp_validation_form(
            '','','','Phone number already in use. Please Enter a different Phone number.','',''
        );
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $this->sendChallenge($user_name,$user_email,$errors,$phone_number,VerificationType::PHONE);
        else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
            $this->sendChallenge($user_name,$user_email,$errors,$phone_number,VerificationType::BOTH);
        else
            $this->sendChallenge($user_name,$user_email,$errors,$phone_number,VerificationType::EMAIL);
    }

      function isPhoneNumberAlreadyInUse($phone)
    {
        if($this->_restrictDuplicates) {
            global $wpdb;
            $phone = MoUtility::processPhoneNumber($phone);
            $results = $wpdb->get_row("SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_value` = '$phone'");
            return !MoUtility::isBlank($results);
        }
        return false;
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        $otpVerType = $this->getVerificationType();
        $fromBoth = $otpVerType===VerificationType::BOTH ? TRUE : FALSE;
        miniorange_site_otp_validation_form(
            $user_login,$user_email,$phone_number, MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth
        );
    }


     
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && $this->isPhoneVerificationEnabled()) {
            $selector = array_merge($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $form = $this->parseFormDetails();

        $this->_formDetails = !empty($form) ? $form : "";
        $this->_isFormEnabled = $this->sanitizeFormPOST('crf_default_enable');
        $this->_otpType = $this->sanitizeFormPOST('crf_enable_type');
        $this->_restrictDuplicates = $this->sanitizeFormPOST('crf_restrict_duplicates');

        update_mo_option('crf_default_enable', $this->_isFormEnabled);
        update_mo_option('crf_enable_type', $this->_otpType);
        update_mo_option('crf_otp_enabled',maybe_serialize($this->_formDetails));
        update_mo_option('crf_restrict_duplicates', $this->_restrictDuplicates);
    }

    function parseFormDetails()
    {
        $form = array();
        if(!array_key_exists('crf_form',$_POST) && empty($_POST['crf_form']['form'])) {
            return $form;
        }
        foreach (array_filter($_POST['crf_form']['form']) as $key => $value) {
            $form[sanitize_text_field($value)]=array('emailkey'=>sanitize_text_field($_POST['crf_form']['emailkey'][$key]),
                                'phonekey'=>sanitize_text_field($_POST['crf_form']['phonekey'][$key]),
                                'email_show'=>sanitize_text_field($_POST['crf_form']['emailkey'][$key]),
                                'phone_show'=>sanitize_text_field($_POST['crf_form']['phonekey'][$key]));
        }
        return $form;
    }
}