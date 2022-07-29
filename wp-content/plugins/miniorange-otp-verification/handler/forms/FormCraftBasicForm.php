<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;


class FormCraftBasicForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::FORMCRAFT;
        $this->_typePhoneTag = 'mo_formcraft_phone_enable';
        $this->_typeEmailTag = 'mo_formcraft_email_enable';
        $this->_formKey = 'FORMCRAFTBASIC';
        $this->_formName = mo_('FormCraft Basic (Free Version)');
        $this->_isFormEnabled = get_mo_option('formcraft_enable');
        $this->_phoneFormId = array();
        $this->_formDocuments = MoOTPDocs::FORMCRAFT_BASIC_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        if(!$this->isFormCraftPluginInstalled()) return; 
        $this->_otpType = get_mo_option('formcraft_enable_type');
        $this->_formDetails = maybe_unserialize(get_mo_option('formcraft_otp_enabled'));
        if(empty($this->_formDetails)) return;
        foreach ($this->_formDetails as $key => $value) {
            array_push($this->_phoneFormId,"[data-id=".$key."] input[name=".$value['phonekey']."]");
        }

        add_action( 'wp_ajax_formcraft_basic_form_submit', array($this,'validate_formcraft_form_submit'),1);
        add_action( 'wp_ajax_nopriv_formcraft_basic_form_submit', array($this,'validate_formcraft_form_submit'),1);

        add_action( 'wp_ajax_unset_formcraft_basic_session', array($this,'unsetOTPSessionVariables'));
        add_action( 'wp_ajax_nopriv_unset_formcraft_basic_session', array($this,'unsetOTPSessionVariables'));

        add_action( 'wp_enqueue_scripts', array($this,'enqueue_script_on_page'));
        $this->routeData();
    }

    
    function routeData()
    {
        if(!array_key_exists('option', $_GET)) return;

        switch (trim($_GET['option']))
        {
            case "miniorange-formcraft-verify":
                $this->_handle_formcraft_form($_POST);										break;
            case "miniorange-formcraft-form-otp-enabled":
                wp_send_json($this->isVerificationEnabledForThisForm(sanitize_text_field($_POST['form_id'])));	break;
        }
    }


    
    function _handle_formcraft_form($data)
    {

        if(!$this->isVerificationEnabledForThisForm(sanitize_text_field($_POST['form_id']))) return;
        MoUtility::initialize_transaction($this->_formSessionVar);
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0)
            $this->_send_otp_to_phone($data);
        else
            $this->_send_otp_to_email($data);
    }


    
    function _send_otp_to_phone($data)
    {
        if(array_key_exists('user_phone', $data) && !MoUtility::isBlank($data['user_phone'])) {
            SessionUtils::addPhoneVerified($this->_formSessionVar,$data['user_phone']);
            $this->sendChallenge('test','',null, trim($data['user_phone']),VerificationType::PHONE);
        } else {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }


    
    function _send_otp_to_email($data)
    {
        if(array_key_exists('user_email', $data) && !MoUtility::isBlank($data['user_email'])) {
            SessionUtils::addEmailVerified($this->_formSessionVar,$data["user_email"]);
            $this->sendChallenge('test',$data['user_email'],null,$data['user_email'],VerificationType::EMAIL);
        } else {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }


    
    function validate_formcraft_form_submit()
    {

        $id = sanitize_text_field($_POST['id']);
        if(!$this->isVerificationEnabledForThisForm($id)) return;

        $this->checkIfVerificationNotStarted($id);
        $formData = $this->_formDetails[$id];
        $otpType = $this->getVerificationType();

        if($otpType===VerificationType::PHONE
            && !SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,sanitize_text_field($_POST[$formData['phonekey']]))) {
            $this->sendJSONErrorMessage([
                "errors" => [
                    $this->_formDetails[$id]['phonekey'] => MoMessages::showMessage(MoMessages::PHONE_MISMATCH)
                ]
            ]);
        } elseif ($otpType===VerificationType::EMAIL
            && !SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,sanitize_text_field($_POST[$formData['emailkey']]))) {
            $this->sendJSONErrorMessage([
                "errors" => [
                    $this->_formDetails[$id]['emailkey'] => MoMessages::showMessage(MoMessages::EMAIL_MISMATCH)
                ]
            ]);
        }

        if(!MoUtility::sanitizeCheck($_POST,$formData['verifyKey'])) {
            $this->sendJSONErrorMessage([
                "errors" => [
                    $this->_formDetails[$id]['verifyKey'] => MoUtility::_get_invalid_otp_method()
                ]
            ]);
        }
        SessionUtils::setFormOrFieldId($this->_formSessionVar,$id);

        if (!SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpType)) {
            
            $this->validateChallenge($otpType,NULL,sanitize_text_field($_POST[$formData['verifyKey']]));    
        }
        
    }


    
    function enqueue_script_on_page()
    {
        wp_register_script( 'formcraftscript', MOV_URL . 'includes/js/formcraftbasic.min.js?version='.MOV_VERSION , array('jquery') );
        wp_localize_script( 'formcraftscript', 'mofcvars', array(
            'imgURL'		=> 	MOV_LOADER_URL,
            'formCraftForms'=> 	$this->_formDetails,
            'siteURL' 		=> 	site_url(),
            'ajaxURL'       =>  wp_ajax_url(),
            'otpType' 		=>  $this->_otpType,
            'buttonText'	=> 	mo_('Click here to send OTP'),
            'buttonTitle'	=> 	$this->_otpType===$this->_typePhoneTag ?
                                mo_('Please enter a Phone Number to enable this field.' )
                                : mo_('Please enter an email address to enable this field.' ),
            'ajaxurl'       => wp_ajax_url(),
            'typePhone'		=>  $this->_typePhoneTag,
            'countryDrop'	=> get_mo_option('show_dropdown_on_form'),
        ));
        wp_enqueue_script('formcraftscript');
    }


    
    function isVerificationEnabledForThisForm($id)
    {
        return array_key_exists($id,$this->_formDetails);
    }


    
    function sendJSONErrorMessage($errors)
    {
        $response['failed'] = mo_('Please correct the errors');
        $response['errors'] = $errors;
        echo json_encode($response);
        die();
    }


    
    function checkIfVerificationNotStarted($id)
    {
        if(SessionUtils::isOTPInitialized($this->_formSessionVar)) return;

        $errorMessage = MoMessages::showMessage(MoMessages::PLEASE_VALIDATE);
        if($this->_otpType===$this->_typePhoneTag) {
            $this->sendJSONErrorMessage([
                "errors" => [ $this->_formDetails[$id]['phonekey'] => $errorMessage ]
            ]);
        } else {
            $this->sendJSONErrorMessage([
                "errors" => [ $this->_formDetails[$id]['emailkey'] => $errorMessage ]
            ]);
        }
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        $formID = SessionUtils::getFormOrFieldId($this->_formSessionVar);
        SessionUtils::addStatus($this->_formSessionVar,self::VERIFICATION_FAILED,$otpType);
        $this->sendJSONErrorMessage([
            "errors" => [ $this->_formDetails[$formID]['verifyKey'] =>  MoUtility::_get_invalid_otp_method() ]
        ]);
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
        wp_send_json(MoUtility::createJson(
                    "unset variable success", MoConstants::SUCCESS_JSON_TYPE
            ));
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && $this->_otpType===$this->_typePhoneTag) {
            $selector = array_merge($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function isFormCraftPluginInstalled()
    {
        return MoUtility::getActivePluginVersion('FormCraft') < 3 ? true : false ;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;
        if(!$this->isFormCraftPluginInstalled()) return;         if(!array_key_exists('formcraft_form',$_POST)) return;
        foreach (array_filter($_POST['formcraft_form']['form']) as $key => $value)
        {
            $value = sanitize_text_field($value);
            $formData = $this->getFormCraftFormDataFromID($value);
            if(MoUtility::isBlank($formData)) continue;
            $fieldIds = $this->getFieldIDs($_POST,$key,$formData);

            $form[$value]= array(
                'emailkey'=> $fieldIds['emailKey'],
                'phonekey'=> $fieldIds['phoneKey'],
                'verifyKey'=> $fieldIds['verifyKey'],
                'phone_show'=> sanitize_text_field($_POST['formcraft_form']['phonekey'][$key]),
                'email_show'=> sanitize_text_field($_POST['formcraft_form']['emailkey'][$key]),
                'verify_show'=> sanitize_text_field($_POST['formcraft_form']['verifyKey'][$key])
            );
        }

        $this->_isFormEnabled = $this->sanitizeFormPOST('formcraft_enable');
        $this->_otpType = $this->sanitizeFormPOST('formcraft_enable_type');
        $this->_formDetails = !empty($form) ? $form : "";

        update_mo_option('formcraft_enable',$this->_isFormEnabled);
        update_mo_option('formcraft_enable_type',$this->_otpType);
        update_mo_option('formcraft_otp_enabled',maybe_serialize($this->_formDetails));
    }


    
    private function getFieldIDs($data,$key,$formData)
    {
        $fieldIds = array('emailKey'=>'','phoneKey'=>'','verifyKey'=>'');
        if(empty($data)) return $fieldIds;
        foreach ($formData as $form) {
            if(strcasecmp($form['elementDefaults']['main_label'],sanitize_text_field($data['formcraft_form']['emailkey'][$key]))===0)
                $fieldIds['emailKey']=$form['identifier'];
            if(strcasecmp($form['elementDefaults']['main_label'],sanitize_text_field($data['formcraft_form']['phonekey'][$key]))===0)
                $fieldIds['phoneKey']=$form['identifier'];
            if(strcasecmp($form['elementDefaults']['main_label'],sanitize_text_field($data['formcraft_form']['verifyKey'][$key]))===0)
                $fieldIds['verifyKey']=$form['identifier'];
        }
        return $fieldIds;
    }


    
    function getFormCraftFormDataFromID($id)
    {
        global $wpdb,$forms_table;
        $meta = $wpdb->get_var( "SELECT meta_builder FROM $forms_table WHERE id=$id" );
        $meta = json_decode(stripcslashes($meta),1);
        return $meta['fields'];
    }
}