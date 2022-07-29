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


class UserProRegistrationForm extends FormHandler implements IFormHandler
{
    use Instance;

    
    private $_userAjaxCheck;

    
    private $_userFieldMeta;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::USERPRO_FORM;
        $this->_typePhoneTag = 'mo_userpro_registration_phone_enable';
        $this->_typeEmailTag = 'mo_userpro_registration_email_enable';
        $this->_phoneFormId = "input[data-label='Phone Number']";
        $this->_userAjaxCheck = "mo_phone_validation";
        $this->_userFieldMeta = "verification_form";
        $this->_formKey = 'USERPRO_FORM';
        $this->_formName = mo_("UserPro Form");
        $this->_isFormEnabled = get_mo_option('userpro_default_enable');
        $this->_formDocuments = MoOTPDocs::USERPRO_FORM_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('userpro_enable_type');
        $this->_disableAutoActivate = get_mo_option('userpro_verify');
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0)
        {
            add_action('wp_ajax_userpro_side_validate', array($this,'validate_userpro_phone'),1);
            add_action('wp_ajax_nopriv_userpro_side_validate', array($this,'validate_userpro_phone'),1);
        }

        if(!$this->_isFormEnabled) return;
        add_filter('userpro_register_validation',array($this,'_process_userpro_form_submit'),1,2);
        add_action('userpro_after_new_registration',array($this,'_auto_verify_user'),1,1);
        add_shortcode('mo_verify_email_userpro', array($this,'_userpro_email_shortcode') );
        add_shortcode('mo_verify_phone_userpro', array($this,'_userpro_phone_shortcode') );

        $this->routeData();
    }


    
    function routeData()
    {
        if(!array_key_exists('option', $_GET)) return;
        switch (trim($_GET['option']))
        {
            case "miniorange-userpro-form":
                $this->_send_otp($_POST);			break;
        }
    }


    
    function _auto_verify_user($user_id)
    {
        if($this->_disableAutoActivate) update_user_meta($user_id,'userpro_verified', 1);
    }


    
    function validate_userpro_phone()
    {
        if($this->checkIfUserHasNotSubmittedTheFormForValidation()) return;

        $message = MoUtility::_get_invalid_otp_method();
        if(strcasecmp(sanitize_text_field($_POST['ajaxcheck']),$this->_userAjaxCheck)!=0) return;
        if(!MoUtility::validatePhoneNumber("+".trim($_POST['input_value']))) {
            wp_send_json(array('error' => $message));
        }
    }


    
    function checkIfUserHasNotSubmittedTheFormForValidation()
    {
        return isset($_POST['action']) && isset($_POST['ajaxcheck'])
                && isset($_POST['input_value']) && sanitize_text_field($_POST['action']) != 'userpro_side_validate' ? TRUE : FALSE;
    }


    
    function _send_otp($getData)
    {

        MoUtility::initialize_transaction($this->_formSessionVar);
        $this->processEmailAndStartOTPVerificationProcess($getData);
        $this->processPhoneAndStartOTPVerificationProcess($getData);
        $this->sendErrorMessageIfOTPVerificationNotStarted();
    }


    
    function processEmailAndStartOTPVerificationProcess($getData)
    {
        if(!array_key_exists('user_email', $getData) || !isset($getData['user_email'])) return;
        SessionUtils::addEmailVerified($this->_formSessionVar,sanitize_email($getData['user_email']));
        $this->sendChallenge('',sanitize_email($getData['user_email']),null,sanitize_email($getData['user_email']),VerificationType::EMAIL);
    }


    
    function processPhoneAndStartOTPVerificationProcess($getData)
    {
        if(!array_key_exists('user_phone', $getData) || !isset($getData['user_phone'])) return;
        SessionUtils::addPhoneVerified($this->_formSessionVar,sanitize_text_field($getData['user_phone']));
        $this->sendChallenge('','',null, sanitize_text_field(trim($getData['user_phone'])),"phone");
    }


    
    function sendErrorMessageIfOTPVerificationNotStarted()
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }


    
    function _process_userpro_form_submit($output,$form)
    {


        if(!$this->checkIfValidFormSubmition($output,$form)) return $output;
        $otpVerType = $this->getVerificationType();

        if($otpVerType===VerificationType::EMAIL && !SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,$form['user_email'])) {
            $output['user_email'] = MoMessages::showMessage(MoMessages::EMAIL_MISMATCH);
        }elseif($otpVerType===VerificationType::PHONE && !SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,$form['phone_number'])) {
            $output['phone_number'] = MoMessages::showMessage(MoMessages::PHONE_MISMATCH);
        }

        $this->processOTPEntered($output,$form);
        return $output;
    }


    
    function checkIfValidFormSubmition(&$output,$form)
    {
        if(array_key_exists($this->_userFieldMeta,$form) && !Sessionutils::isOTPInitialized($this->_formSessionVar))
        {
            $output[$this->_userFieldMeta] =  MoMessages::showMessage(MoMessages::PLEASE_VALIDATE);
            return FALSE;
        }
        return TRUE;
    }


    
    function processOTPEntered(&$output,$form)
    {
        if(!empty($output)) return;
        $otpVerType = $this->getVerificationType();
        $this->validateChallenge($otpVerType,NULL,$form[$this->_userFieldMeta]);
        if(!SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$otpVerType))
            $output[$this->_userFieldMeta] = MoUtility::_get_invalid_otp_method();
        else
            $this->unsetOTPSessionVariables();
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar, self::VERIFICATION_FAILED, $otpType);
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar, self::VALIDATED, $otpType);
    }


    
    function _userpro_phone_shortcode()
    {
        $img = "<div style='display:table;text-align:center;'>".
                    "<img src='".MOV_URL. "includes/images/loader.gif' alt='loading...'>".
                "</div>";

        $htmlContent =  "<div style='margin-top: 2%;'><button type='button' class='button alt'".
                            " style='width:100%;height:30px;font-family: Roboto;font-size: 12px !important;' id='miniorange_otp_token_submit'".
                                " title='".mo_('Please Enter a phone number to enable this')."'>".mo_('Click Here to Verify Phone').
                            "</button></div><div style='margin-top:2%'>".
                        "<div id='mo_message' hidden='' style='background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;''></div></div>";

        $script = '    <script>
                            jQuery(document).click(function(e){
                                $mo=jQuery;
                                var unique_id;
                                if($mo("#miniorange_otp_token_submit").length===0){
                                    unique_id=$mo("#unique_id").val();
                                    var phone_field="#phone_number-"+unique_id;
                                    if($mo(phone_field).length) {
                                        $mo("' . $htmlContent . '").insertAfter(phone_field);
                                    }
                                }
                                if(e.target.id==="miniorange_otp_token_submit"){
                                    unique_id=$mo("#unique_id").val();
                                    var user_phone="phone_number-"+unique_id;
                                    var phone =$mo("input[name="+user_phone+"]").val();
                                    $mo("#mo_message").empty();
                                    $mo("#mo_message").append("'.$img.'");
                                    $mo("#mo_message").show();
                                    $mo.ajax({
                                        url:"'.site_url().'/?option=miniorange-userpro-form",
                                        type:"POST",data:{user_phone:phone},
                                        crossDomain:!0,
                                        dataType:"json",
                                        success:function(o){
                                            if(o.result==="success"){
                                                $mo("#mo_message").empty();
                                                $mo("#mo_message").append(o.message);
                                                $mo("#mo_message").css("border-top","3px solid green");
                                                $mo("input[name=phone_verify]").focus();
                                            }else{
                                                $mo("#mo_message").empty();
                                                $mo("#mo_message").append(o.message);
                                                $mo("#mo_message").css("border-top","3px solid red");
                                                $mo("input[name=phone_verify]").focus();
                                            }
                                        },
                                        error:function(o,e,n){}
                                    });
                                }
                            });
                        </script>';
        return $script;
    }


    
    function _userpro_email_shortcode()
    {
        $img = "<div style='display:table;text-align:center;'>".
                    "<img src='".MOV_URL. "includes/images/loader.gif' alt='loading...'>".
                "</div>";
$htmlContent =  "<div style='margin-top: 2%;'><button type='button' class='button alt'".
                            " style='width:100%;height:30px;font-family: Roboto;font-size: 12px !important;' id='miniorange_otp_token_submit'".
                                " title='".mo_('Please Enter a Email address to enable this')."'>".mo_('Click Here to Verify Email').
                            "</button></div><div style='margin-top:2%'>".
                        "<div id='mo_message' hidden='' style='background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;''></div></div>";

        $script = '    <script>
                            jQuery(document).click(function(e){
                                $mo=jQuery;
                                var unique_id;
                                if($mo("#miniorange_otp_token_submit").length===0){
                                    unique_id =$mo("#unique_id").val();
                                    var email_field="#user_email-"+unique_id;
                                    if($mo(email_field).length) {
                                        $mo("' . $htmlContent . '").insertAfter(email_field);
                                    }
                                }
                                if(e.target.id==="miniorange_otp_token_submit"){
                                    unique_id=$mo("#unique_id").val();
                                    var user_email="user_email-"+unique_id;
                                    var email =$mo("input[name="+user_email+"]").val();
                                    $mo("#mo_message").empty();
                                    $mo("#mo_message").append("'.$img.'");
                                    $mo("#mo_message").show();
                                    $mo.ajax({
                                        url:"'.site_url().'/?option=miniorange-userpro-form",
                                        type:"POST",data:{user_email:email},
                                        crossDomain:!0,
                                        dataType:"json",
                                        success:function(o){
                                            if(o.result==="success"){
                                                $mo("#mo_message").empty();
                                                $mo("#mo_message").append(o.message);
                                                $mo("#mo_message").css("border-top","3px solid green");
                                                $mo("input[name=email_verify]").focus();
                                            }else{
                                                $mo("#mo_message").empty();
                                                $mo("#mo_message").append(o.message);
                                                $mo("#mo_message").css("border-top","3px solid red");
                                                $mo("input[name=email_verify]").focus();
                                            }
                                        },
                                        error:function(o,e,n){}
                                    });
                                }
                            });
                        </script>';
        return $script;
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId, $this->_formSessionVar]);
    }

    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && $this->_otpType===$this->_typePhoneTag) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('userpro_registration_enable');
        $this->_otpType = $this->sanitizeFormPOST('userpro_registration_type');
        $this->_disableAutoActivate = $this->sanitizeFormPOST('userpro_verify');

        update_mo_option('userpro_default_enable',$this->_isFormEnabled);
        update_mo_option('userpro_enable_type',$this->_otpType);
        update_mo_option('userpro_verify', $this->_disableAutoActivate);
    }
}