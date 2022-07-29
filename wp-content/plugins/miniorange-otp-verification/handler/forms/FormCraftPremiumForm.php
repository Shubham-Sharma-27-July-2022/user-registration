<?php

namespace OTP\Handler\Forms;

use mysql_xdevapi\Session;
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


class FormCraftPremiumForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::FORMCRAFT;
        $this->_typePhoneTag = 'mo_formcraft_phone_enable';
        $this->_typeEmailTag = 'mo_formcraft_email_enable';
        $this->_formKey = 'FORMCRAFTPREMIUM';
        $this->_formName = mo_('FormCraft (Premium Version)');
        $this->_isFormEnabled = get_mo_option('fcpremium_enable');
        $this->_phoneFormId = array();
        $this->_formDocuments = MoOTPDocs::FORMCRAFT_PREMIUM;
        parent::__construct();
    }

    
    function handleForm()
    {
        if(!MoUtility::getActivePluginVersion('FormCraft')) return; 
        $this->_otpType = get_mo_option('fcpremium_enable_type');
        $this->_formDetails = maybe_unserialize(get_mo_option('fcpremium_otp_enabled'));
        if(empty($this->_formDetails)) return;
        if($this->isFormCraftVersion3Installed()) {
            foreach ($this->_formDetails as $key => $value) {
                array_push($this->_phoneFormId,'input[name^='.$value['phonekey'].']');
            }
        } else {
            foreach ($this->_formDetails as $key => $value) {
                array_push($this->_phoneFormId,'.nform_li input[name^='.$value['phonekey'].']');
            }
        }

        add_action( 'wp_ajax_formcraft_submit', array($this,'validate_formcraft_form_submit'),1);
        add_action( 'wp_ajax_nopriv_formcraft_submit', array($this,'validate_formcraft_form_submit'),1);
        add_action( 'wp_ajax_formcraft3_form_submit', array($this,'validate_formcraft_form_submit'),1);
        add_action( 'wp_ajax_nopriv_formcraft3_form_submit', array($this,'validate_formcraft_form_submit'),1);
        add_action( 'wp_enqueue_scripts', array($this,'enqueue_script_on_page'));
        $this->routeData();
    }

    
    function routeData()
    {
        if(!array_key_exists('option', $_GET)) return;

        switch (trim($_GET['option']))
        {
            case "miniorange-formcraftpremium-verify":
                $this->_handle_formcraft_form($_POST);										break;
            case "miniorange-formcraftpremium-form-otp-enabled":
                wp_send_json($this->isVerificationEnabledForThisForm(sanitize_text_field($_POST['form_id'])));	break;
        }
    }


    
    function _handle_formcraft_form($data)
    {

        if(!$this->isVerificationEnabledForThisForm(sanitize_text_field($_POST['form_id']))) return;
        MoUtility::initialize_transaction($this->_formSessionVar);
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $this->_send_otp_to_phone($data);
        else
            $this->_send_otp_to_email($data);
    }


    
    function _send_otp_to_phone($data)
    {
        if(array_key_exists('user_phone', $data) && !MoUtility::isBlank(sanitize_text_field($data['user_phone']))) {
            SessionUtils::addPhoneVerified($this->_formSessionVar,sanitize_text_field($data['user_phone']));
            $this->sendChallenge('test','',null, trim($data['user_phone']),VerificationType::PHONE);
        } else {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }


    
    function _send_otp_to_email($data)
    {
        if(array_key_exists('user_email', $data) && !MoUtility::isBlank(sanitize_email($data['user_email']))) {
            SessionUtils::addEmailVerified($this->_formSessionVar,sanitize_email($data["user_email"]));
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

        $formData = $this->parseSubmittedData($_POST,$id);
        $this->checkIfVerificationNotStarted($formData);

        $phone = is_array($formData['phone']['value']) ? $formData['phone']['value'][0] : $formData['phone']['value'];
        $email = is_array($formData['email']['value']) ? $formData['email']['value'][0] : $formData['email']['value'];
        $otp   = is_array($formData['otp']['value']) ? $formData['otp']['value'][0] : $formData['otp']['value'];

        $otpType = $this->getVerificationType();
        if($otpType===VerificationType::PHONE
            && !SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,$phone)) {
            $this->sendJSONErrorMessage(
                MoMessages::showMessage(MoMessages::PHONE_MISMATCH), $formData['phone']['field']
            );
        } elseif ($otpType===VerificationType::EMAIL
            && !SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,$email)) {
            $this->sendJSONErrorMessage(
                MoMessages::showMessage(MoMessages::EMAIL_MISMATCH),$formData['email']['field']
            );
        }
        if(MoUtility::isBlank($formData['otp']['value'])) {
            $this->sendJSONErrorMessage(MoUtility::_get_invalid_otp_method(), $formData['otp']['field']);
        }
        SessionUtils::setFormOrFieldId($this->_formSessionVar,$formData['otp']['field']);
        $this->validateChallenge($otpType,NULL,$otp);
    }


    
    function enqueue_script_on_page()
    {
        wp_register_script( 'fcpremiumscript', MOV_URL . 'includes/js/formcraftpremium.min.js?version='.MOV_VERSION , array('jquery') );
        wp_localize_script( 'fcpremiumscript', 'mofcpvars', array(
            'imgURL'		=> 	MOV_LOADER_URL,
            'formCraftForms'=> 	$this->_formDetails,
            'siteURL' 		=> 	site_url(),
            'otpType' 		=>  $this->_otpType,
            'buttonText'	=> 	mo_('Click here to send OTP'),
            'buttonTitle'	=> 	$this->_otpType==$this->_typePhoneTag ?
                mo_('Please enter a Phone Number to enable this field.' )
                : mo_('Please enter an email address to enable this field.' ),
            'ajaxurl'       => 	wp_ajax_url(),
            'typePhone'		=>  $this->_typePhoneTag,
            'countryDrop'	=> get_mo_option('show_dropdown_on_form'),
            'version3' 		=> $this->isFormCraftVersion3Installed(),
        ));
        wp_enqueue_script('fcpremiumscript');
    }


    
    function parseSubmittedData($post,$id)
    {
        $data = array();
        $form = $this->_formDetails[$id];
        foreach ($post as $key => $value) {
            if(strpos($key, 'field')===FALSE) continue;
            $this->getValueAndFieldFromPost($data,'email',$key,str_replace(" ","_",$form['emailkey']),$value);
            $this->getValueAndFieldFromPost($data,'phone',$key,str_replace(" ","_",$form['phonekey']),$value);
            $this->getValueAndFieldFromPost($data,'otp',$key,str_replace(" ","_",$form['verifyKey']),$value);
        }
        return $data;
    }


    function getValueAndFieldFromPost(&$data,$dataKey,$postKey,$checkKey,$value)
    {
        if(is_null($data[$dataKey]) && strpos($postKey,$checkKey,0)!==FALSE){
            $data[$dataKey]['value'] = $this->isFormCraftVersion3Installed() && $dataKey=='otp' ? $value[0] : $value;
            $index = strpos($postKey, 'field', 0);
            $data[$dataKey]['field'] = $this->isFormCraftVersion3Installed() ? $postKey
                : substr($postKey, $index, strpos($postKey,'_',$index) - $index);
        }
    }

    
    function isVerificationEnabledForThisForm($id)
    {
        return array_key_exists($id,$this->_formDetails);
    }


    
    function sendJSONErrorMessage($errors,$field)
    {
        if($this->isFormCraftVersion3Installed())
        {
            $response['failed'] =  mo_("Please correct the errors and try again");
            $response['errors'][$field] = $errors;
        }
        else
        {
            $response['errors'] =  mo_("Please correct the errors and try again");
            $response[$field][0] = $errors;
        }
        echo json_encode($response);
        die();
    }


    
    function checkIfVerificationNotStarted($formData)
    {
        if(SessionUtils::isOTPInitialized($this->_formSessionVar)) return;

        if($this->_otpType==$this->_typePhoneTag)
            $this->sendJSONErrorMessage( MoMessages::showMessage(MoMessages::PLEASE_VALIDATE) , $formData['phone']['field']);
        else
            $this->sendJSONErrorMessage( MoMessages::showMessage(MoMessages::PLEASE_VALIDATE) , $formData['email']['field']);
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) return;
        $form_id = SessionUtils::getFormOrFieldId($this->_formSessionVar);
        $this->sendJSONErrorMessage( MoUtility::_get_invalid_otp_method() , $form_id );
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

        if($this->isFormEnabled() && $this->_otpType==$this->_typePhoneTag) {
            $selector = array_merge($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function getFieldId($data,$formData)
    {
        foreach ($formData as $form) {
            if ($form['elementDefaults']['main_label'] == $data) {
                return $form['identifier'];
            }
        }
        return NULL;
    }


    
    function getFormCraftFormDataFromID($id)
    {
        global $wpdb,$fc_forms_table;
        $meta = $wpdb->get_var( "SELECT meta_builder FROM $fc_forms_table WHERE id=$id" );
        $meta = json_decode(stripcslashes($meta),1);
        return $meta['fields'];
    }

    
    function isFormCraftVersion3Installed()
    {
        return MoUtility::getActivePluginVersion('FormCraft') == 3 ? true : false ;
    }

    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;
        if(!MoUtility::getActivePluginVersion('FormCraft')) return;         $form = array();

        foreach (array_filter($_POST['fcpremium_form']['form']) as $key => $value)
        {
            $value = sanitize_text_field($value);
            !$this->isFormCraftVersion3Installed() ? $this->processAndGetFormData($_POST,$key,$value,$form)
                : $this->processAndGetForm3Data($_POST,$key,$value,$form);
        }

        $this->_isFormEnabled = $this->sanitizeFormPOST('fcpremium_enable');
        $this->_otpType = $this->sanitizeFormPOST('fcpremium_enable_type');
        $this->_formDetails = !empty($form) ? $form : "";

        update_mo_option('fcpremium_enable',$this->_isFormEnabled);
        update_mo_option('fcpremium_enable_type',$this->_otpType);
        update_mo_option('fcpremium_otp_enabled',maybe_serialize($this->_formDetails));
    }


    
    function processAndGetFormData($post,$key,$value,&$form)
    {
        $form[$value]= array(
            'emailkey'=> str_replace(" "," ", sanitize_text_field($post['fcpremium_form']['emailkey'][$key])).'_email_email_',
            'phonekey'=> str_replace(" "," ", sanitize_text_field($post['fcpremium_form']['phonekey'][$key])).'_text_',
            'verifyKey'=> str_replace(" "," ", sanitize_text_field($post['fcpremium_form']['verifyKey'][$key])).'_text_',
            'phone_show'=> sanitize_text_field($post['fcpremium_form']['phonekey'][$key]),
            'email_show'=> sanitize_text_field($post['fcpremium_form']['emailkey'][$key]),
            'verify_show'=> sanitize_text_field($post['fcpremium_form']['verifyKey'][$key])
        );
    }


    
    function processAndGetForm3Data($post,$key,$value,&$form)
    {
        $formData = $this->getFormCraftFormDataFromID($value);
        if(MoUtility::isBlank($formData)) return;
        $form[$value]= array(
            'emailkey'=> $this->getFieldId(sanitize_text_field($post['fcpremium_form']['emailkey'][$key]),$formData),
            'phonekey'=> $this->getFieldId(sanitize_text_field($post['fcpremium_form']['phonekey'][$key]),$formData),
            'verifyKey'=> $this->getFieldId(sanitize_text_field($post['fcpremium_form']['verifyKey'][$key]),$formData),
            'phone_show'=> sanitize_text_field($post['fcpremium_form']['phonekey'][$key]),
            'email_show'=> sanitize_text_field($post['fcpremium_form']['emailkey'][$key]),
            'verify_show'=> sanitize_text_field($post['fcpremium_form']['verifyKey'][$key])
        );
    }
}