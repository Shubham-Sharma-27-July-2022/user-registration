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


class VisualFormBuilder extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::VISUAL_FORM;
        $this->_typePhoneTag = 'mo_visual_form_phone_enable';
        $this->_typeEmailTag = 'mo_visual_form_email_enable';
        $this->_typeBothTag = 'mo_visual_form_both_enable';
        $this->_formKey = 'VISUAL_FORM';
        $this->_formName = mo_('Visual Form Builder');
        $this->_phoneFormId = [];
        $this->_isFormEnabled = get_mo_option('visual_form_enable');
        $this->_buttonText = get_mo_option("visual_form_button_text");
        $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText : mo_("Click Here to send OTP");
        $this->_generateOTPAction = "miniorange-vf-send-otp";
        $this->_validateOTPAction = "miniorange-vf-verify-code";
        $this->_formDocuments = MoOTPDocs::VISUAL_FORM_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('visual_form_enable_type');
        $this->_formDetails = maybe_unserialize(get_mo_option('visual_form_otp_enabled'));
        if(empty($this->_formDetails) || !$this->_isFormEnabled) return;
        foreach($this->_formDetails as $key => $value) {
            array_push($this->_phoneFormId, '#' . $value['phonekey']);
        }
        add_action('wp_enqueue_scripts', array($this, 'mo_enqueue_vf'));
        add_action("wp_ajax_{$this->_generateOTPAction}", [$this,'_send_otp_vf_ajax']);
        add_action("wp_ajax_nopriv_{$this->_generateOTPAction}", [$this,'_send_otp_vf_ajax']);
        add_action("wp_ajax_{$this->_validateOTPAction}", [$this,'processFormAndValidateOTP']);
        add_action("wp_ajax_nopriv_{$this->_validateOTPAction}", [$this,'processFormAndValidateOTP']);
    }

    
    function mo_enqueue_vf()
    {
        wp_register_script( 'vfscript', MOV_URL . 'includes/js/vfscript.min.js',array('jquery') );
        wp_localize_script( 'vfscript', 'movfvar', array(
            'siteURL' 		=> 	wp_ajax_url(),
            'otpType'       =>  strcasecmp($this->_otpType,$this->_typePhoneTag),
            'formDetails'   =>  $this->_formDetails,
            'buttontext'    =>  $this->_buttonText,
            'imgURL'        =>  MOV_LOADER_URL,
            'fieldText'     =>  mo_('Enter OTP here'),
            'gnonce'        =>  wp_create_nonce($this->_nonce),
            'nonceKey'      =>  wp_create_nonce($this->_nonceKey),
            'vnonce'        =>  wp_create_nonce($this->_nonce),
            'gaction'       =>  $this->_generateOTPAction,
            'vaction'       =>  $this->_validateOTPAction
        ) );
        wp_enqueue_script( 'vfscript' );
    }

    
    function _send_otp_vf_ajax()
    {

        $this->validateAjaxRequest();
        if ( $this->_otpType == $this->_typePhoneTag)
            $this->_send_vf_otp_to_phone($_POST);
        else
            $this->_send_vf_otp_to_email($_POST);
    }

    
    function _send_vf_otp_to_phone($data)
    {
        if(!MoUtility::sanitizeCheck('user_phone',$data)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $this->startOTPVerification(sanitize_text_field(trim($data['user_phone'])), NULL, sanitize_text_field(trim($data['user_phone'])),VerificationType::PHONE);
        }
    }

    
    function _send_vf_otp_to_email($data)
    {
        if(!MoUtility::sanitizeCheck('user_email',$data)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $this->startOTPVerification(sanitize_email($data['user_email']), sanitize_email($data['user_email']), NULL, VerificationType::EMAIL);
        }
    }

    
    private function startOTPVerification($sessionValue, $userEmail, $phoneNumber, $otpType)
    {
        MoUtility::initialize_transaction($this->_formSessionVar);
        if($otpType===VerificationType::PHONE) {
            SessionUtils::addPhoneVerified($this->_formSessionVar,$sessionValue);
        }else{
            SessionUtils::addEmailVerified($this->_formSessionVar,$sessionValue);
        }
        $this->sendChallenge('',$userEmail,NULL,$phoneNumber,$otpType);
    }

    
    function processFormAndValidateOTP()
    {

        $this->validateAjaxRequest();
        $this->checkIfVerificationNotStarted();
        $this->checkIntegrityAndValidateOTP($_POST);
    }

    
    function checkIfVerificationNotStarted()
    {
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)){
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }

    

    private function checkIntegrityAndValidateOTP($post)
    {

        $this->checkIntegrity($post);
        $this->validateChallenge($this->getVerificationType(),NULL,sanitize_text_field($post['otp_token']));
    }

    
    private function checkIntegrity($post)
    {
        if($this->isPhoneVerificationEnabled()) {
            if(!SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,sanitize_text_field($post['sub_field']))) {
                wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::PHONE_MISMATCH), MoConstants::ERROR_JSON_TYPE
                ));
            }
        } else if(!SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,sanitize_text_field($post['sub_field']))) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::EMAIL_MISMATCH), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }

    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        wp_send_json(MoUtility::createJson(
            MoUtility::_get_invalid_otp_method(), MoConstants::ERROR_JSON_TYPE
        ));
    }

    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        $this->unsetOTPSessionVariables();
        wp_send_json(MoUtility::createJson(
            MoConstants::SUCCESS, MoConstants::SUCCESS_JSON_TYPE
        ));
    }

    
    function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId, $this->_formSessionVar]);
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
        $otpVerType = $this->getVerificationType();
        return $otpVerType==VerificationType::PHONE || $otpVerType===VerificationType::BOTH;
    }


    
    function handleFormOptions()
    { 
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        if(!function_exists('is_plugin_active')) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if( !is_plugin_active( 'visual-form-builder/visual-form-builder.php' ) ) return;

        $form = $this->parseFormDetails();

        $this->_isFormEnabled = $this->sanitizeFormPOST('visual_form_enable');
        $this->_otpType = $this->sanitizeFormPOST('visual_form_enable_type');
        $this->_formDetails = !empty($form) ? $form : "";
        $this->_buttonText = $this->sanitizeFormPOST('visual_form_button_text');

        if($this->basicValidationCheck(BaseMessages::VISUAL_FORM_CHOOSE)) {
            update_mo_option('visual_form_button_text', $this->_buttonText);
            update_mo_option('visual_form_enable', $this->_isFormEnabled);
            update_mo_option('visual_form_enable_type', $this->_otpType);
            update_mo_option('visual_form_otp_enabled', maybe_serialize($this->_formDetails));
        }
    }



    
    function parseFormDetails()
    {
        $form = array();
        if(!array_key_exists('visual_form',$_POST)) return array();

        foreach (array_filter($_POST['visual_form']['form']) as $key => $value)
        {
            $form[$value]= array(
                'emailkey'=> $this->getFieldID(sanitize_text_field($_POST['visual_form']['emailkey'][$key]),$value),
                'phonekey'=> $this->getFieldID(sanitize_text_field($_POST['visual_form']['phonekey'][$key]),$value),
                'phone_show'=>sanitize_text_field($_POST['visual_form']['phonekey'][$key]),
                'email_show'=>sanitize_text_field($_POST['visual_form']['emailkey'][$key]),
            );
        }
        return $form;
    }

    
    private function getFieldID($key, $formId)
    {
        global $wpdb;
        $query = "SELECT * FROM ".VFB_WP_FIELDS_TABLE_NAME." where field_name ='".$key."'and form_id = '".$formId."'";
        $result = $wpdb->get_row($query);
        return !MoUtility::isBlank($result) ? 'vfb-'.$result->field_id : '';
    }

}