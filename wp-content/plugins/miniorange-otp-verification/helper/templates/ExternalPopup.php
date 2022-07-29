<?php

namespace OTP\Helper\Templates;

if(! defined( 'ABSPATH' )) exit;

use OTP\Objects\MoITemplate;
use OTP\Objects\Template;
use OTP\Traits\Instance;

class ExternalPopup extends Template implements MoITemplate
{
    use Instance;

	protected function __construct()
	{
		$this->key = "EXTERNAL";
		$this->templateEditorID = "customEmailMsgEditor3";
		$this->requiredTags =  array_merge(
		    $this->requiredTags,
            [
                "{{PHONE_FIELD_NAME}}","{{SEND_OTP_BTN_ID}}","{{VERIFICATION_FIELD_NAME}}",
                "{{VALIDATE_BTN_ID}}","{{SEND_OTP_BTN_ID}}","{{VERIFY_CODE_BOX}}"
            ]
        );
		parent::__construct();
	}

    
	public function getDefaults($templates)
	{
		if(!is_array($templates)) $templates = array();
		$templates[$this->getTemplateKey()] = file_get_contents(MOV_DIR . 'includes/html/externalphone.min.html');
		return $templates;
	}

	public function parse($template,$message,$otp_type,$from_both)
	{
		$requiredScripts = $this->getRequiredScripts();
		$extraPostData = $this->preview ? "" : extra_post_data();
		$extraFormFields = '<input type="hidden" name="option" value="mo_ajax_form_validate" />';

		$template = str_replace("{{JQUERY}}",$this->jqueryUrl,$template);
		$template = str_replace("{{FORM_ID}}",'mo_validate_form',$template);
		$template = str_replace("{{GO_BACK_ACTION_CALL}}",'mo_validation_goback();',$template);
		$template = str_replace("{{MO_CSS_URL}}",MOV_CSS_URL,$template);
		$template = str_replace("{{OTP_MESSAGE_BOX}}",'mo_message',$template);
		$template = str_replace("{{REQUIRED_FORMS_SCRIPTS}}",$requiredScripts,$template);
		$template = str_replace("{{HEADER}}",mo_("Validate OTP (One Time Passcode)"),$template);
		$template = str_replace("{{GO_BACK}}",mo_('&larr; Go Back'),$template);
		$template = str_replace("{{MESSAGE}}",mo_($message),$template);
		$template = str_replace("{{REQUIRED_FIELDS}}",$extraFormFields,$template);
		$template = str_replace("{{PHONE_FIELD_NAME}}",'mo_phone_number',$template);
		$template = str_replace("{{OTP_FIELD_TITLE}}",mo_("Enter Code"),$template);
		$template = str_replace("{{VERIFY_CODE_BOX}}",'mo_validate_otp',$template);
		$template = str_replace("{{VERIFICATION_FIELD_NAME}}",'mo_otp_token',$template);
		$template = str_replace("{{VALIDATE_BTN_ID}}",'validate_otp',$template);
		$template = str_replace("{{VALIDATE_BUTTON_TEXT}}",mo_("Validate"),$template);
		$template = str_replace("{{SEND_OTP_TEXT}}",mo_('Send OTP'),$template);
		$template = str_replace("{{SEND_OTP_BTN_ID}}",'send_otp',$template);
		$template = str_replace('{{EXTRA_POST_DATA}}',$extraPostData,$template);
        $template .= $this->getExtraFormFields();

		return $template;
	}

    
	private function getExtraFormFields()
    {
	    $ffields = '<form name="f" method="post" action="" id="validation_goBack_form">
                        <input id="validation_goBack" name="option" value="validation_goBack" type="hidden"/>
                    </form>';
	    return $ffields;
    }

	
	private function getRequiredScripts()
	{
		$scripts = '<style>.mo_customer_validation-modal{display:block!important}</style>';
		if(!$this->preview) {
			$scripts .=
                '<script>function mo_validation_goback(){
               document.getElementById("validation_goBack_form").submit()};'.
                    'jQuery(document).ready(function(){'.
                        '$mo=jQuery,'.
                        '$mo("#send_otp").click(function(o){'.
                            'var e=$mo("input[name=mo_phone_number]").val();'.
                            '$mo("#mo_message").empty(),'.
                            '$mo("#mo_message").append("'.$this->img.'"),'.
                            '$mo("#mo_message").show(),'.
                            '$mo.ajax({'.
                                'url:"'.site_url().'/?option=miniorange-ajax-otp-generate",'.
                                'type:"POST",'.
                                'data:{user_phone:e},'.
                                'crossDomain:!0,'.
                                'dataType:"json",
                                success:function(o){'.
                                    '"success"==o.result?('.
                                        '$mo("#mo_message").empty(),'.
                                        '$mo("#mo_message").append(o.message),'.
                                        '$mo("#mo_message").css("background-color","#8eed8e"),'.
                                        '$mo("#validate_otp").show(),'.
                                        '$mo("#send_otp").val("'.mo_('Resend OTP').'"),'.
                                        '$mo("#mo_validate_otp").show(),'.
                                        '$mo("input[name=mo_validate_otp]").focus()'.
                                    '):('.
                                        '$mo("#mo_message").empty(),'.
                                        '$mo("#mo_message").append(o.message),'.
                                        '$mo("#mo_message").css("background-color","#eda58e"),'.
                                        '$mo("input[name=mo_phone_number]").focus()'.
                                    ')'.
                                '},'.
                                'error:function(o,e,m){}'.
                            '})'.
                        '}),'.
                        '$mo("#validate_otp").click(function(o){'.
                            'var e=$mo("input[name=mo_otp_token]").val(),'.
                            'm=$mo("input[name=mo_phone_number]").val();'.
                            '$mo("#mo_message").empty(),'.
                            '$mo("#mo_message").append("'.$this->img.'"),'.
                            '$mo("#mo_message").show(),'.
                            '$mo.ajax({'.
                                'url:"'.site_url().'/?option=miniorange-ajax-otp-validate",'.
                                'type:"POST",'.
                                'data:{mo_otp_token:e,user_phone:m},'.
                                'crossDomain:!0,'.
                                'dataType:"json",'.
                                'success:function(o){'.
                                    '"success"==o.result?('.
                                        '$mo("#mo_message").empty(),'.
                                        '$mo("#mo_validate_form").submit()'.
                                    '):('.
                                        '$mo("#mo_message").empty(),'.
                                        '$mo("#mo_message").append(o.message),'.
                                        '$mo("#mo_message").css("background-color","#eda58e"),'.
                                        '$mo("input[name=validate_otp]").focus()'.
                                    ')'.
                                '},'.
                                'error:function(o,e,m){}'.
                            '})'.
                        '})'.
                    '});'.
                '</script>';
		} else {
			$scripts .=  '<script>'.
                            '$mo=jQuery,'.
                            '$mo("#mo_validate_form").submit(function(e){'.
                                'e.preventDefault();'.
                            '});'.
                        '</script>';
		}
		return $scripts;
	}
}