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


class WpMemberForm extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::WPMEMBER_REG;
        $this->_emailKey = 'user_email';
        $this->_phoneKey = get_mo_option( 'wp_member_reg_phone_field_key');
        $this->_phoneFormId = "input[name=$this->_phoneKey]";
        $this->_formKey = 'WP_MEMBER_FORM';
        $this->_typePhoneTag = "mo_wpmember_reg_phone_enable";
        $this->_typeEmailTag = "mo_wpmember_reg_email_enable";
        $this->_formName = mo_("WP-Members");
        $this->_isFormEnabled = get_mo_option('wp_member_reg_enable');
        $this->_formDocuments = MoOTPDocs::WP_MEMBER_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('wp_member_reg_enable_type');
        add_filter('wpmem_register_form_rows', array($this,'wpmember_add_button'),99,2);
        add_action('wpmem_pre_register_data', array($this,'validate_wpmember_submit'),99,1);

        $this->routeData();
    }


    
    function routeData()
    {
        if(!array_key_exists('option', $_REQUEST)) return;
        switch (trim($_REQUEST['option']))
        {
            case "miniorange-wpmember-form":
                $this->_handle_wp_member_form($_POST);		break;
        }
    }

    
    function _handle_wp_member_form($data)
    {

        MoUtility::initialize_transaction($this->_formSessionVar);

        if($this->_otpType===$this->_typeEmailTag) {
            $this->processEmailAndStartOTPVerificationProcess($data);
        }
        if($this->_otpType===$this->_typePhoneTag) {
            $this->processPhoneAndStartOTPVerificationProcess($data);
        }
    }


    
    function processEmailAndStartOTPVerificationProcess($data)
    {
        if(MoUtility::sanitizeCheck('user_email',$data)) {
            $user_email = sanitize_email($data['user_email']);
            SessionUtils::addEmailVerified($this->_formSessionVar, $user_email);
            $this->sendChallenge(null, $user_email, null, '', VerificationType::EMAIL, null, null, false);
        } else {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }


    
    function processPhoneAndStartOTPVerificationProcess($data)
    {
        if(MoUtility::sanitizeCheck('user_phone',$data)) {
            $user_phone = sanitize_text_field($data['user_phone']);
            SessionUtils::addPhoneVerified($this->_formSessionVar, $user_phone);
            $this->sendChallenge(null, '', null, $user_phone, VerificationType::PHONE, null, null, false);
        } else {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }

    
    function wpmember_add_button($rows, $tag)
    {
        foreach($rows as $key=>$field)
        {
            if(strcasecmp($this->_otpType,$this->_typePhoneTag)===0 && $key===$this->_phoneKey) {
                $rows[$key]['field'] .= $this->_add_shortcode_to_wpmember("phone",$field['meta']);
                break;
            } else if(strcasecmp($this->_otpType,$this->_typeEmailTag)===0 && $key===$this->_emailKey) {
                $rows[$key]['field'] .= $this->_add_shortcode_to_wpmember("email",$field['meta']);
                break;
            }
        }
        return $rows;
    }


    
    function validate_wpmember_submit($fields)
    {
        global $wpmem_themsg;

        $otpType = $this->getVerificationType();
        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) {
            $wpmem_themsg = MoMessages::showMessage(MoMessages::PLEASE_VALIDATE);
        }

       else if(!$this->validate_submitted($fields,$otpType)) return;
       $this->validateChallenge($otpType,NULL,$fields['validate_otp']);
    }


    
    function validate_submitted($fields,$otpType)
    {
        global $wpmem_themsg;

        if($otpType===VerificationType::EMAIL
            && !SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,$fields[$this->_emailKey]))
        {
            $wpmem_themsg =  MoMessages::showMessage(MoMessages::EMAIL_MISMATCH);
            return false;
        }
        else if($otpType==VerificationType::PHONE
            && !SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,$fields[$this->_phoneKey]))
        {
            $wpmem_themsg =  MoMessages::showMessage(MoMessages::PHONE_MISMATCH);
            return false;
        }
        else
            return true;
    }


    
    function _add_shortcode_to_wpmember($mo_type,$field)
    {
        $img  			= "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";
        $field_content  = "<div style='margin-top: 2%;'><button type='button' class='button alt' style='width:100%;height:30px;";
        $field_content .= "font-family: Roboto;font-size: 12px !important;' id='miniorange_otp_token_submit' ";
        $field_content .= "title='Please Enter an '".$mo_type."'to enable this.'>Click Here to Verify ". $mo_type."</button></div>";
        $field_content .= "<div style='margin-top:2%'><div id='mo_message' hidden='' style='background-color: #f7f6f7;padding: ";
        $field_content .= "1em 2em 1em 3.5em;'></div></div>";
        $field_content .= '<script>jQuery(document).ready(function(){$mo=jQuery;$mo("#miniorange_otp_token_submit").click(function(o){ ';
        $field_content .= 'var e=$mo("input[name='.$field.']").val(); $mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'"),';
        $field_content .= '$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=miniorange-wpmember-form",type:"POST",';
        $field_content .= 'data:{user_'.$mo_type.':e},crossDomain:!0,dataType:"json",success:function(o){ ';
        $field_content .= 'if(o.result==="success"){$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),';
        $field_content .= '$mo("#mo_message").css("border-top","3px solid green"),$mo("input[name=email_verify]").focus()}else{';
        $field_content .= '$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid red")';
        $field_content .= ',$mo("input[name=phone_verify]").focus()} ;},error:function(o,e,n){}})});});</script>';

        return $field_content;
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {
        global $wpmem_themsg;

        $wpmem_themsg =  MoUtility::_get_invalid_otp_method();
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        $this->unsetOTPSessionVariables();
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

        $this->_isFormEnabled = $this->sanitizeFormPOST('wp_member_reg_enable');
        $this->_otpType = $this->sanitizeFormPOST('wp_member_reg_enable_type');
        $this->_phoneKey = $this->sanitizeFormPOST('wp_member_reg_phone_field_key');

        if($this->basicValidationCheck(BaseMessages::WP_MEMBER_CHOOSE)) {
            update_mo_option('wp_member_reg_phone_field_key', $this->_phoneKey);
            update_mo_option('wp_member_reg_enable', $this->_isFormEnabled);
            update_mo_option('wp_member_reg_enable_type', $this->_otpType);
        }
    }
}