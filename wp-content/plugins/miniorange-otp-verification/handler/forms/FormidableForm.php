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


class FormidableForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::FORMIDABLE_FORM;
        $this->_typePhoneTag = 'mo_frm_form_phone_enable';
        $this->_typeEmailTag = 'mo_frm_form_email_enable';
        $this->_formKey = 'FORMIDABLE_FORM';
        $this->_formName = mo_('Formidable Forms');
        $this->_isFormEnabled = get_mo_option('frm_form_enable');
        $this->_buttonText = get_mo_option("frm_button_text");
        $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_("Click Here to send OTP");
        $this->_generateOTPAction = 'miniorange_frm_generate_otp';
        $this->_formDocuments = MoOTPDocs::FORMIDABLE_FORM_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('frm_form_enable_type');
        $this->_formDetails = maybe_unserialize(get_mo_option('frm_form_otp_enabled'));
        $this->_phoneFormId = array();
        if(empty($this->_formDetails) || !$this->_isFormEnabled) return;
        foreach($this->_formDetails as $key => $value) {
            array_push($this->_phoneFormId, '#' . $value['phonekey'] . ' input');
        }

        add_filter('frm_validate_field_entry', [$this,'miniorange_otp_validation'], 11, 4 );
        add_action("wp_ajax_{$this->_generateOTPAction}", [$this,'_send_otp_frm_ajax']);
        add_action("wp_ajax_nopriv_{$this->_generateOTPAction}", [$this,'_send_otp_frm_ajax']);

                add_action('wp_enqueue_scripts',array($this, 'miniorange_register_formidable_script'));
    }

    
    function miniorange_register_formidable_script()
    {
        wp_register_script( 'moformidable', MOV_URL . 'includes/js/formidable.min.js',array('jquery') );
        wp_localize_script( 'moformidable', 'moformidable', array(
            'siteURL' 		=> wp_ajax_url(),
            'otpType'  		=> $this->_otpType,
            'formkey'       => strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? 'phonekey' : 'emailkey',
            'nonce'         => wp_create_nonce($this->_nonce),
            'buttontext'    => mo_($this->_buttonText),
            'imgURL'        => MOV_LOADER_URL,
            'forms'         => $this->_formDetails,
            'generateURL'   => $this->_generateOTPAction,
        ));
        wp_enqueue_script( 'moformidable' );
    }

    
    function _send_otp_frm_ajax()
    {

        $this->validateAjaxRequest();
        if ( $this->_otpType == $this->_typePhoneTag)
            $this->_send_frm_otp_to_phone($_POST);
        else
            $this->_send_frm_otp_to_email($_POST);
    }

    
    function _send_frm_otp_to_phone($data)
    {
        if(!MoUtility::sanitizeCheck('user_phone',$data)) {
            wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $this->sendOTP(trim($data['user_phone']), NULL, trim($data['user_phone']), VerificationType::PHONE);
        }
    }

    
    function _send_frm_otp_to_email($data)
    {
        if(!MoUtility::sanitizeCheck('user_email',$data)) {
            wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $this->sendOTP(sanitize_email($data['user_email']), sanitize_email($data['user_email']), NULL, VerificationType::EMAIL);
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


    
    function miniorange_otp_validation( $errors, $field, $value, $args )
    {


                if( $this->getFieldId('verify_show',$field) !== $field->id) return $errors;
                if(!MoUtility::isBlank($errors)) return $errors;
        if(!$this->hasOTPBeenSent($errors,$field)) return $errors;
        if($this->isMisMatchEmailOrPhone($errors,$field)) return $errors;
        if(!$this->isValidOTP($value,$field,$errors)) return $errors;
        return $errors;
    }


    
    private function hasOTPBeenSent(&$errors,$field)
    {
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) {
            $message = MoMessages::showMessage(BaseMessages::ENTER_VERIFY_CODE);
            if( $this->isPhoneVerificationEnabled() )
                $errors['field'.$this->getFieldId('phone_show',$field)] = $message;
            else
                $errors['field'.$this->getFieldId('email_show',$field)] = $message;
            return false;
        }
        return true;
    }


    
    private function isMisMatchEmailOrPhone(&$errors,$field)
    {
        $fieldId = $this->getFieldId(($this->isPhoneVerificationEnabled() ? 'phone_show' : 'email_show'),$field);
        $fieldValue = sanitize_text_field($_POST['item_meta'][$fieldId]);
        if ( !$this->checkPhoneOrEmailIntegrity($fieldValue)) {
            if( $this->isPhoneVerificationEnabled() )
                $errors['field'.$this->getFieldId('phone_show',$field)]
                    = MoMessages::showMessage(BaseMessages::PHONE_MISMATCH);
            else
                $errors['field'.$this->getFieldId('email_show',$field)]
                    = MoMessages::showMessage(BaseMessages::EMAIL_MISMATCH);
            return true;
        }
        return false;
    }

    
    private function isValidOTP($value,$field,&$errors)
    {
        $otpType = $this->getVerificationType();
        $this->validateChallenge($otpType,NULL,$value);
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpType)) {
            $this->unsetOTPSessionVariables();
            return true;
        }else {
            $errors['field'.$this->getFieldId('verify_show',$field)] = MoUtility::_get_invalid_otp_method();
            return false;
        }
    }


    
    private function checkPhoneOrEmailIntegrity($fieldValue)
    {
        if($this->isPhoneVerificationEnabled()) {
            return SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar, $fieldValue);
        } else {
            return SessionUtils::isEmailVerifiedMatch($this->_formSessionVar, $fieldValue);
        }
    }

    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VERIFICATION_FAILED,$otpType);
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }

    
    function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }


    
    public function getPhoneNumberSelector($selector)
    {
        if($this->_isFormEnabled && $this->isPhoneVerificationEnabled()) {
            $selector = array_merge($selector, $this->_phoneFormId);
        }
        return $selector;
    }

    
    function isPhoneVerificationEnabled()
    {
        return $this->getVerificationType() === VerificationType::PHONE;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $form = $this->parseFormDetails();

        $this->_isFormEnabled = $this->sanitizeFormPOST('frm_form_enable');
        $this->_otpType = $this->sanitizeFormPOST('frm_form_enable_type');
        $this->_formDetails = !empty($form) ? $form : "";
        $this->_buttonText = $this->sanitizeFormPOST('frm_button_text');

        if($this->basicValidationCheck(BaseMessages::FORMIDABLE_CHOOSE)) {
            update_mo_option('frm_button_text', $this->_buttonText);
            update_mo_option('frm_form_enable', $this->_isFormEnabled);
            update_mo_option('frm_form_enable_type', $this->_otpType);
            update_mo_option('frm_form_otp_enabled', maybe_serialize($this->_formDetails));
        }
    }

    
    function parseFormDetails()
    {
        $form = array();
        if(!array_key_exists('frm_form',$_POST)) return array();

        foreach (array_filter($_POST['frm_form']['form']) as $key => $value)
        {
            $key = sanitize_text_field($key);
            $form[sanitize_text_field($value)]= array(
                'emailkey'=> 'frm_field_'.sanitize_text_field($_POST['frm_form']['emailkey'][$key]).'_container',
                'phonekey'=> 'frm_field_'. sanitize_text_field($_POST['frm_form']['phonekey'][$key]).'_container',
                'verifyKey'=> 'frm_field_'. sanitize_text_field($_POST['frm_form']['verifyKey'][$key]).'_container',
                'phone_show'=> sanitize_text_field($_POST['frm_form']['phonekey'][$key]),
                'email_show'=> sanitize_text_field($_POST['frm_form']['emailkey'][$key]),
                'verify_show'=> sanitize_text_field($_POST['frm_form']['verifyKey'][$key]),
            );
        }
        return $form;
    }

    
    function getFieldId($key,$field) { return $this->_formDetails[$field->form_id][$key]; }
}