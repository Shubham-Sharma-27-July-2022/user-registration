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
use \WPCF7_FormTag;
use WPCF7_Validation;


class ContactForm7 extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar 	= FormSessionVars::CF7_FORMS;
        $this->_typePhoneTag = 'mo_cf7_contact_phone_enable';
        $this->_typeEmailTag = 'mo_cf7_contact_email_enable';
        $this->_formKey = 'CF7_FORM';
        $this->_formName = mo_('Contact Form 7 - Contact Form');
        $this->_isFormEnabled = get_mo_option('cf7_contact_enable');
        $this->_generateOTPAction = "miniorange-cf7-contact";
        $this->_formDocuments = MoOTPDocs::CF7_FORM_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('cf7_contact_type');
        $this->_emailKey = get_mo_option('cf7_email_key');
        $this->_phoneKey = 'mo_phone';
        $this->_phoneFormId = [
            '.class_'.$this->_phoneKey,
            'input[name='.$this->_phoneKey.']'
        ];

        add_filter( 'wpcf7_validate_text*'	, array($this,'validateFormPost'), 1 , 2 );
        add_filter( 'wpcf7_validate_email*'	, array($this,'validateFormPost'), 1 , 2 );
        add_filter( 'wpcf7_validate_email'	, array($this,'validateFormPost'), 1 , 2 );
        add_filter( 'wpcf7_validate_tel*'	, array($this,'validateFormPost'), 1 , 2 );
        add_action( 'wpcf7_before_send_mail', array($this, 'unsetSession'), 1 , 1);

        add_shortcode('mo_verify_email', array($this,'_cf7_email_shortcode') );
        add_shortcode('mo_verify_phone', array($this,'_cf7_phone_shortcode') );

        add_action("wp_ajax_nopriv_{$this->_generateOTPAction}"  ,[$this,'_handle_cf7_contact_form']);
        add_action("wp_ajax_{$this->_generateOTPAction}"         ,[$this,'_handle_cf7_contact_form']);
    }


    
    function _handle_cf7_contact_form()
    {
        $data = $_POST;
        $this->validateAjaxRequest();

        MoUtility::initialize_transaction($this->_formSessionVar);

        if(MoUtility::sanitizeCheck('user_email',$data))
        {
            SessionUtils::addEmailVerified($this->_formSessionVar,$data['user_email']);
            $this->sendChallenge('test',$data['user_email'],null,$data['user_email'],VerificationType::EMAIL);
        }
        else if(MoUtility::sanitizeCheck('user_phone',$data))
        {
            SessionUtils::addPhoneVerified($this->_formSessionVar,trim($data['user_phone']));
            $this->sendChallenge('test','',null, trim($data['user_phone']),VerificationType::PHONE);
        }
        else
        {
            if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
                wp_send_json( MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::ENTER_PHONE),
                    MoConstants::ERROR_JSON_TYPE
                ));
            else
                wp_send_json( MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::ENTER_EMAIL),
                    MoConstants::ERROR_JSON_TYPE
                ));
        }
    }


    
    function validateFormPost($result, $tag)
    {

        $tag = new WPCF7_FormTag( $tag );
        $name = $tag->name;
        $value = isset( $_POST[$name] ) ? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) ) : '';

        if ( 'email' == $tag->basetype && $name==$this->_emailKey && strcasecmp($this->_otpType,$this->_typeEmailTag)==0) {
            SessionUtils::addEmailSubmitted($this->_formSessionVar,$value);
        }
        if ( 'tel' == $tag->basetype && $name==$this->_phoneKey && strcasecmp($this->_otpType,$this->_typePhoneTag)==0) {
            SessionUtils::addPhoneSubmitted($this->_formSessionVar,$value);
        }

        if ( 'text' == $tag->basetype && $name=='email_verify' || 'text' == $tag->basetype && $name=='phone_verify') {
            $this->checkIfVerificationCodeNotEntered($name,$result,$tag);
            $this->checkIfVerificationNotStarted($result,$tag);
            if(strcasecmp($this->_otpType,$this->_typeEmailTag)==0) {
                $this->processEmail($result, $tag);
            }
            if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0) {
                $this->processPhoneNumber($result, $tag);
            }
            if(empty($result->get_invalid_fields())) {                 if(!$this->processOTPEntered($name)) {
                    $result->invalidate($tag, MoUtility::_get_invalid_otp_method());
                } 
            }
        }
        return $result;
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VERIFICATION_FAILED,$otpType);
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }


    
    function processOTPEntered($name)
    {
        $otpVerType = $this->getVerificationType();
        if(!SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpVerType))
            $this->validateChallenge($otpVerType,$name,NULL);
        return SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpVerType);
    }


    
    function processEmail(&$result,$tag)
    {
        if(!SessionUtils::isEmailSubmittedAndVerifiedMatch($this->_formSessionVar)) {
            $result->invalidate($tag, mo_(MoMessages::showMessage(MoMessages::EMAIL_MISMATCH)));
        }
    }


    
    function processPhoneNumber(&$result,$tag)
    {
        if(!Sessionutils::isPhoneSubmittedAndVerifiedMatch($this->_formSessionVar)) {
            $result->invalidate($tag, mo_(MoMessages::showMessage(MoMessages::PHONE_MISMATCH)));
        }
    }


    
    function checkIfVerificationNotStarted(&$result,$tag)
    {
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) {
            $result->invalidate($tag, mo_(MoMessages::showMessage(MoMessages::PLEASE_VALIDATE)));
        }
    }


    
    function checkIfVerificationCodeNotEntered($name,&$result,$tag)
    {
        if(!MoUtility::sanitizeCheck($name,$_REQUEST)){
            $result->invalidate($tag, wpcf7_get_message('invalid_required'));
        }
    }


    
    function _cf7_email_shortcode($attrs)
    {
        $emailKey = MoUtility::sanitizeCheck("key",$attrs);
        $buttonId = MoUtility::sanitizeCheck("buttonid",$attrs);
        $messagediv = MoUtility::sanitizeCheck("messagediv",$attrs);
        $emailKey = $emailKey ? "#".$emailKey : "input[name='".$this->_emailKey."']";
        $buttonId = $buttonId ? $buttonId : "miniorange_otp_token_submit";
        $messagediv = $messagediv ? $messagediv : "mo_message";


        $img   = "<div style='display:table;text-align:center;'>".
                    "<img src='".MOV_URL. "includes/images/loader.gif'>".
                  "</div>";
        $img = str_replace('"', "'", $img);
        $html  = '<script>'.
                    'jQuery(document).ready(function(){'.
                        '$mo=jQuery;'.
                        '$mo( "#'.$buttonId.'" ).each(function(index) {'.
                            '$mo(this).on("click", function(){'.
                                'var t = $mo(this).closest("form");'.
                                'var e = t.find("'.$emailKey.'").val();'.
                                'var n = t.find("input[name=\'email_verify\']");'.
                                'var d = t.find("#'.$messagediv.'");'.
                                'd.empty();'.
                                'd.append("'.$img.'");'.
                                'd.show();'.
                                '$mo.ajax({'.
                                    'url:"'.wp_ajax_url().'",'.
                                    'type:"POST",'.
                                    'data:{'.
                                        'user_email:e,'.
                                        'action:"'.$this->_generateOTPAction.'",'.
                                        $this->_nonceKey.':"'.wp_create_nonce($this->_nonce).'"'.
                                    '},'.
                                    'crossDomain:!0,'.
                                    'dataType:"json",'.
                                    'success:function(o){ '.
                                    'if(o.result=="success"){'.
                                        'd.empty(),'.
                                            'd.append(o.message),'.
                                            'd.css("border-top","3px solid green"),'.
                                            'n.focus()'.
                                        '}else{'.
                                            'd.empty(),'.
                                            'd.append(o.message),'.
                                            'd.css("border-top","3px solid red")'.
                                        '}'.
                                    '},'.
                                    'error:function(o,e,n){}'.
                                '})'.
                            '});'.
                        '});'.
                    '});'.
                 '</script>';
        return $html;
    }


    
    function _cf7_phone_shortcode($attrs)
    {
        $phonekey = MoUtility::sanitizeCheck("key",$attrs);
        $buttonId = MoUtility::sanitizeCheck("buttonid",$attrs);
        $messagediv = MoUtility::sanitizeCheck("messagediv",$attrs);
        $phonekey = $phonekey ? "#".$phonekey : "input[name='".$this->_phoneKey."']";
        $buttonId = $buttonId ? $buttonId : "miniorange_otp_token_submit";
        $messagediv = $messagediv ? $messagediv : "mo_message";

        $img   = "<div style='display:table;text-align:center;'>".
                    "<img src='".MOV_URL. "includes/images/loader.gif'>".
                  "</div>";
        $img = str_replace('"', "'", $img);

        $html  = '<script>'.
                    'jQuery(document).ready(function(){'.
                        '$mo=jQuery;$mo( "#'.$buttonId.'" ).each(function(index) {'.
                            '$mo(this).on("click", function(){'.
                                'var t = $mo(this).closest("form");'.
                                'var e = t.find("'.$phonekey.'").val();'.
                                'var n = t.find("input[name=\'phone_verify\']");'.
                                'var d = t.find("#'.$messagediv.'");'.
                                'd.empty();'.
                                'd.append("'.$img.'");'.
                                'd.show();'.
                                '$mo.ajax({'.
                                'url:"'.wp_ajax_url().'",'.
                                    'type:"POST",'.
                                    'data:{'.
                                        'user_phone:e,'.
                                        'action:"'.$this->_generateOTPAction.'",'.
                                        $this->_nonceKey.':"'.wp_create_nonce($this->_nonce).'"'.
                                    '},'.
                                    'crossDomain:!0,'.
                                    'dataType:"json",'.
                                    'success:function(o){ '.
                                        'if(o.result=="success"){'.
                                            'd.empty(),'.
                                            'd.append(o.message),'.
                                            'd.css("border-top","3px solid green"),'.
                                            'n.focus()'.
                                        '}else{'.
                                            'd.empty(),'.
                                            'd.append(o.message),'.
                                            'd.css("border-top","3px solid red")'.
                                        '}'.
                                    '},'.
                                    'error:function(o,e,n){}'.
                                '})'.
                            '});'.
                        '});'.
                    '});'.
                 '</script>';
        return $html;
    }


    

    public function unsetSession($result)
    {
        $this->unsetOTPSessionVariables();
        return $result;
    }

    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->_isFormEnabled && ($this->_otpType == $this->_typePhoneTag)) {
            $selector = array_merge($selector,$this->_phoneFormId);
        }
        return $selector;
    }


    
    private function emailKeyValidationCheck(){
        if($this->_otpType === $this->_typeEmailTag && MoUtility::isBlank($this->_emailKey)){
            do_action(
                'mo_registration_show_message',
                MoMessages::showMessage(BaseMessages::CF7_PROVIDE_EMAIL_KEY),
                MoConstants::ERROR
            );
            return false;
        }
        return true;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('cf7_contact_enable');
        $this->_otpType = $this->sanitizeFormPOST('cf7_contact_type');
        $this->_emailKey = $this->sanitizeFormPOST('cf7_email_field_key');

                if($this->basicValidationCheck(BaseMessages::CF7_CHOOSE)
            && $this->emailKeyValidationCheck()) {
            update_mo_option('cf7_contact_enable', $this->_isFormEnabled);
            update_mo_option('cf7_contact_type', $this->_otpType);
            update_mo_option('cf7_email_key', $this->_emailKey);
        }
    }
}