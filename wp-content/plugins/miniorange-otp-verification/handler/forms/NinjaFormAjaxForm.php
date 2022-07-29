<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoConstants;
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


class NinjaFormAjaxForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::NINJA_FORM_AJAX;
        $this->_typePhoneTag = 'mo_ninja_form_phone_enable';
        $this->_typeEmailTag = 'mo_ninja_form_email_enable';
        $this->_typeBothTag = 'mo_ninja_form_both_enable';
        $this->_formKey = 'NINJA_FORM_AJAX';
        $this->_formName = mo_('Ninja Forms ( Above version 3.0 )');
        $this->_isFormEnabled = get_mo_option('nja_enable');
        $this->_buttonText = get_mo_option('nja_button_text');
        $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_('Click Here to send OTP');
        $this->_phoneFormId = array();
        $this->_formDocuments = MoOTPDocs::NINJA_FORMS_AJAX_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('ninja_form_enable_type');
        $this->_formDetails = maybe_unserialize(get_mo_option('ninja_form_otp_enabled'));
        if(empty($this->_formDetails)) return;
        foreach ($this->_formDetails as $key => $value) {
            array_push($this->_phoneFormId,'input[id=nf-field-'.$value['phonekey'].']');
        }

        add_action( 'ninja_forms_after_form_display'	            , array($this,'enqueue_nj_form_script'),  99 , 1);
        add_filter( 'ninja_forms_submit_data'			            , array($this,'_handle_nj_ajax_form_submit') , 99 ,1);

        $otpType = $this->getVerificationType();
        if($otpType) {
            add_filter('ninja_forms_localize_field_settings_' . $otpType, array($this, '_add_button'), 99, 2);
        }

        $this->routeData();
    }

    
    function routeData()
    {
        if(!array_key_exists('option', $_GET)) return;
        switch (trim($_GET['option']))
        {
            case "miniorange-nj-ajax-verify":
                $this->_send_otp_nj_ajax_verify($_POST);		break;
        }
    }


    
    function enqueue_nj_form_script($form_id)
    {
        if(array_key_exists($form_id,$this->_formDetails))
        {
            $formData = $this->_formDetails[$form_id];
            $formKeyVals = array_keys($this->_formDetails);
            wp_register_script( 'njscript', MOV_URL . 'includes/js/ninjaformajax.min.js', array('jquery'), MOV_VERSION, true );
            wp_localize_script('njscript', 'moninjavars', array(
                'imgURL'		=> MOV_URL. "includes/images/loader.gif",
                'siteURL' 		=> 	site_url(),
                'otpType'       =>  $this->_otpType==$this->_typePhoneTag ? VerificationType::PHONE : VerificationType::EMAIL,
                'forms'         =>  $this->_formDetails,
                'formKeyVals'    =>  $formKeyVals,
            ));
            wp_enqueue_script('njscript');
        }
        return $form_id;
    }


    
    function _add_button($settings,$form)
    {
        $formId = $form->get_id();
        if(!array_key_exists($formId,$this->_formDetails)) return $settings;
        $formData = $this->_formDetails[$formId];
        $fieldKey = $this->_otpType == $this->_typePhoneTag ? "phonekey" : "emailkey";
        if($settings['id']==$formData[$fieldKey]) {
            $settings['afterField']='
                <div id="nf-field-4-container" class="nf-field-container submit-container  label-above ">
                    <div class="nf-before-field">
                        <nf-section></nf-section>
                    </div>
                    <div class="nf-field">
                        <div class="field-wrap submit-wrap">
                            <div class="nf-field-label"></div>
                            <div class="nf-field-element">
                                <input  id="miniorange_otp_token_submit_'.$formId.'" class="ninja-forms-field nf-element"
                                        value="'.mo_($this->_buttonText).'" type="button">
                            </div>
                        </div>
                    </div>
                    <div class="nf-after-field">
                        <nf-section>
                            <div class="nf-input-limit"></div>
                            <div class="nf-error-wrap nf-error"></div>
                        </nf-section>
                    </div>
                </div>
                <div id="mo_message_'.$formId.'" hidden="" style="background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;"></div>';
        }
        return $settings;
    }


    
    function _handle_nj_ajax_form_submit($data)
    {
        if(!array_key_exists($data['id'],$this->_formDetails)) return $data;

        $formData = $this->_formDetails[$data['id']];
        $data = $this->checkIfOtpVerificationStarted($formData,$data);

        if(isset($data['errors']['fields'])) return $data;

        if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0)
            $data = $this->processEmail($formData,$data);
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $data = $this->processPhone($formData,$data);
        if(!isset($data['errors']['fields']))
            $data = $this->processOTPEntered($data,$formData);

        return $data;
    }


    
    function processOTPEntered($data, $formData)
    {
        $verify_field = $formData['verifyKey'];
        $otpType = $this->getVerificationType();
        $this->validateChallenge($otpType,NULL,$data['fields'][$verify_field]['value']);
        if(!SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpType))
            $data['errors']['fields'][$verify_field]=MoUtility::_get_invalid_otp_method();
        else
            $this->unsetOTPSessionVariables();
        return $data;
    }


    
    function checkIfOtpVerificationStarted($formData, $data)
    {
        if(SessionUtils::isOTPInitialized($this->_formSessionVar)) return $data;

        if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0)
            $data['errors']['fields'][$formData['emailkey']]=MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE);
        else
            $data['errors']['fields'][$formData['phonekey']]=MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE);

        return $data;
    }


    
    function processEmail($formData, $data)
    {
        $field_id = $formData['emailkey'];
        if(!SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,$data['fields'][$field_id]['value'])) {
            $data['errors']['fields'][$field_id] = MoMessages::showMessage(MoMessages::EMAIL_MISMATCH);
        }
        return $data;
    }


    
    function processPhone($formData, $data)
    {
        $field_id = $formData['phonekey'];
        if(!SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,$data['fields'][$field_id]['value'])) {
            $data['errors']['fields'][$field_id] = MoMessages::showMessage(MoMessages::PHONE_MISMATCH);
        }
        return $data;
    }


    
    function _send_otp_nj_ajax_verify($data)
    {

        MoUtility::initialize_transaction($this->_formSessionVar);
        if($this->_otpType==$this->_typePhoneTag)
            $this->_send_nj_ajax_otp_to_phone($data);
        else
            $this->_send_nj_ajax_otp_to_email($data);
    }


    
    function _send_nj_ajax_otp_to_phone($data)
    {
        if(!array_key_exists('user_phone', $data) || !isset($data['user_phone'])) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $this->setSessionAndStartOTPVerification(trim($data['user_phone']), NULL, trim($data['user_phone']), VerificationType::PHONE);
        }
    }


    
    function _send_nj_ajax_otp_to_email($data)
    {
        if(!array_key_exists('user_email', $data) || !isset($data['user_email'])) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $this->setSessionAndStartOTPVerification($data['user_email'], $data['user_email'], NULL, VerificationType::EMAIL);            

        }
    }


    
    function setSessionAndStartOTPVerification($sessionValue, $userEmail, $phoneNumber, $otpType)
    {
        if($otpType===VerificationType::PHONE) {
            SessionUtils::addPhoneVerified($this->_formSessionVar, $sessionValue);
        } else {
            SessionUtils::addEmailVerified($this->_formSessionVar, $sessionValue);
        }
        $this->sendChallenge('',$userEmail,NULL,$phoneNumber,$otpType);
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {
        SessionUtils::addStatus($this->_formSessionVar,self::VERIFICATION_FAILED,$otpType);
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

        if($this->isFormEnabled() && ($this->_otpType == $this->_typePhoneTag)) {
            $selector = array_merge($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function getFieldId($id,$data)
    {
        global $wpdb;
        if($data=="email")
        {
            return $wpdb->get_var("SELECT id FROM {$wpdb->prefix}nf3_fields where `parent_id`= $id and  `key` ='".$data."'");
        }
        return $wpdb->get_var("SELECT id FROM {$wpdb->prefix}nf3_fields where `key` ='".$data."'");
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;
        if(isset($_POST['mo_customer_validation_ninja_form_enable'])) return;

        $form = $this->parseFormDetails();

        $this->_formDetails = !empty($form) ? $form : "";
        $this->_otpType = $this->sanitizeFormPOST('nja_enable_type');
        $this->_isFormEnabled = $this->sanitizeFormPOST('nja_enable');
        $this->_buttonText = $this->sanitizeFormPOST('nja_button_text');

        update_mo_option('ninja_form_enable',0);
        update_mo_option('nja_enable', $this->_isFormEnabled);
        update_mo_option('ninja_form_enable_type',$this->_otpType);
        update_mo_option('ninja_form_otp_enabled',maybe_serialize($this->_formDetails));
        update_mo_option('nja_button_text',$this->_buttonText);
    }


    function parseFormDetails()
    {
        $form = array();
        if(!array_key_exists('ninja_ajax_form',$_POST)) return array();
        foreach (array_filter($_POST['ninja_ajax_form']['form']) as $key => $value)
        {
            $form[sanitize_text_field($value)]= array(
                'emailkey'=> $this->getFieldId(sanitize_text_field($value),sanitize_text_field($_POST['ninja_ajax_form']['emailkey'][$key])),
                'phonekey'=> $this->getFieldId(sanitize_text_field($value),sanitize_text_field($_POST['ninja_ajax_form']['phonekey'][$key])),
                'verifyKey'=> $this->getFieldId(sanitize_text_field($value),sanitize_text_field($_POST['ninja_ajax_form']['verifyKey'][$key])),
                'phone_show'=>sanitize_text_field($_POST['ninja_ajax_form']['phonekey'][$key]),
                'email_show'=>sanitize_text_field($_POST['ninja_ajax_form']['emailkey'][$key]),
                'verify_show'=>sanitize_text_field($_POST['ninja_ajax_form']['verifyKey'][$key])
            );
        }
        return $form;
    }

}