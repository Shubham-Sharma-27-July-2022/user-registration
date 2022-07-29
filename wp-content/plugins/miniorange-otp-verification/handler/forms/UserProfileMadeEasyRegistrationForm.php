<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoPHPSessions;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationLogic;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;


class UserProfileMadeEasyRegistrationForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = FALSE;
        $this->_formSessionVar = FormSessionVars::UPME_REG;
        $this->_typePhoneTag = 'mo_upme_phone_enable';
        $this->_typeEmailTag = 'mo_upme_email_enable';
        $this->_typeBothTag = 'mo_upme_both_enable';
        $this->_formKey = 'UPME_FORM';
        $this->_formName = mo_("UserProfile Made Easy Registration Form");
        $this->_isFormEnabled = get_mo_option('upme_default_enable');
        $this->_formDocuments = MoOTPDocs::UPME_FORM_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('upme_enable_type');
        $this->_phoneKey = get_mo_option('upme_phone_key');
        $this->_phoneFormId = 'input[name='.$this->_phoneKey.']';
        
        add_filter( 'insert_user_meta', array($this,'miniorange_upme_insert_user'),1,3);
        add_filter( 'upme_registration_custom_field_type_restrictions', array($this,'miniorange_upme_check_phone') , 1, 2);

        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType()))
            $this->unsetOTPSessionVariables();
        elseif(array_key_exists('upme-register-form',$_POST) && !SessionUtils::isOTPInitialized($this->_formSessionVar))
            $this->_handle_upme_form_submit($_POST);
    }


    
    function isPhoneVerificationEnabled()
    {
        $otpVerType = $this->getVerificationType();
        return $otpVerType===VerificationType::PHONE || $otpVerType===VerificationType::BOTH;
    }


    
    function _handle_upme_form_submit($POSTED)
    {
        $mobile_number = '';
        foreach($POSTED as $key => $value)
        {
            if($key == $this->_phoneKey)
            {
                $mobile_number = $value;
                break;
            }
        }
        $this->miniorange_upme_user(sanitize_text_field($_POST['user_login']),sanitize_email($_POST['user_email']),$mobile_number);
    }


    
    function miniorange_upme_insert_user($meta, $user, $update)
    {

        $file_upload = MoPHPSessions::getSessionVar('file_upload');
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar) || !$file_upload) return $meta;
        foreach ($file_upload as $key => $value)
        {
            $current_field_url = get_user_meta($user->ID, $key, true);
            if('' != $current_field_url) upme_delete_uploads_folder_files($current_field_url);
            update_user_meta($user->ID, $key, $value);
        }
        return $meta;
    }


    
    function miniorange_upme_check_phone($errors,$fields)
    {
        
        global $phoneLogic;
        if(empty($errors))
            if($fields['meta'] ==$this->_phoneKey)
                if(!MoUtility::validatePhoneNumber($fields['value']))
                    $errors[] = str_replace("##phone##",$fields['value'],$phoneLogic->_get_otp_invalid_format_message());
        return $errors;
    }


    
    function miniorange_upme_user($user_name,$user_email,$phone_number)
    {
        global $upme_register;
        $upme_register->prepare($_POST);
        $upme_register->handle();
        $file_upload = array();

        if(!MoUtility::isBlank($upme_register->errors)) return;


        MoUtility::initialize_transaction($this->_formSessionVar);

        $this->processFileUpload($file_upload);
        MoPHPSessions::addSessionVar('file_upload',$file_upload);
        $this->processAndStartOTPVerification($user_name,$user_email,$phone_number);
    }


    
    function processFileUpload(&$file_upload)
    {
        if(empty($_FILES)) return;

        $upload_dir =  wp_upload_dir();
        $target_path = $upload_dir['basedir'] . "/upme/";
        if (!is_dir($target_path)) mkdir($target_path, 0777);

        foreach ($_FILES as $key => $array)
        {
            $base_name = sanitize_file_name(basename($array['name']));
            $target_path = $target_path . time() . '_' . $base_name;
            $nice_url = $upload_dir['baseurl'] . "/upme/";
            $nice_url = $nice_url . time() . '_' . $base_name;
            move_uploaded_file($array['tmp_name'], $target_path);
            $file_upload[$key]=$nice_url;
        }
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId, $this->_formSessionVar]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && $this->isPhoneVerificationEnabled()) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        $otpVerType = $this->getVerificationType();
        $fromBoth = $otpVerType===VerificationType::BOTH ? TRUE : FALSE;
        miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,
            MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth);
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {
        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }


    
    function processAndStartOTPVerification($user_name,$user_email,$phone_number)
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $this->sendChallenge($user_name,$user_email,null,$phone_number,VerificationType::PHONE);
        else if(strcasecmp($this->_otpType,$this->_typeBothTag)==0)
            $this->sendChallenge($user_name,$user_email,null,$phone_number,VerificationType::BOTH);
        else
            $this->sendChallenge($user_name,$user_email,null,$phone_number,VerificationType::EMAIL);
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('upme_default_enable');
        $this->_otpType = $this->sanitizeFormPOST('upme_enable_type');
        $this->_phoneKey = $this->sanitizeFormPOST('upme_phone_field_key');

        update_mo_option('upme_default_enable',$this->_isFormEnabled);
        update_mo_option('upme_enable_type',$this->_otpType);
        update_mo_option('upme_phone_key',$this->_phoneKey);
    }
}