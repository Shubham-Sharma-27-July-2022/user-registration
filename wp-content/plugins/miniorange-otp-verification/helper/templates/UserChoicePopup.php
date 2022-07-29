<?php

namespace OTP\Helper\Templates;

if(! defined( 'ABSPATH' )) exit;

use OTP\Objects\MoITemplate;
use OTP\Objects\Template;
use OTP\Traits\Instance;

class UserChoicePopup extends Template implements MoITemplate
{
    use Instance;

	protected function __construct()
	{
		$this->key = "USERCHOICE";
		$this->templateEditorID = "customEmailMsgEditor2";
		parent::__construct();
	}

    
	public function getDefaults($templates)
	{
		if(!is_array($templates)) $templates = array();
		$templates[$this->getTemplateKey()] = file_get_contents(MOV_DIR . 'includes/html/userchoice.min.html');
		return $templates;
	}

	public function parse($template,$message,$otp_type,$from_both)
	{
		$requiredScripts = $this->getRequiredFormsSkeleton($otp_type,$from_both);
		$extraPostData = $this->preview ? "" : extra_post_data();
		$extraFormFields = '{{EXTRA_POST_DATA}}<input type="hidden" name="option" value="miniorange-validate-otp-choice-form" />';

		$template = str_replace("{{JQUERY}}",$this->jqueryUrl,$template);
		$template = str_replace("{{FORM_ID}}",'mo_validate_form',$template);
		$template = str_replace("{{GO_BACK_ACTION_CALL}}",'mo_validation_goback();',$template);
		$template = str_replace("{{MO_CSS_URL}}",MOV_CSS_URL,$template);
		$template = str_replace("{{REQUIRED_FORMS_SCRIPTS}}",$requiredScripts,$template);
		$template = str_replace("{{HEADER}}",mo_("Validate OTP (One Time Passcode)"),$template);
		$template = str_replace("{{GO_BACK}}",mo_('&larr; Go Back'),$template);
		$template = str_replace("{{MESSAGE}}",mo_($message),$template);
		$template = str_replace("{{BUTTON_TEXT}}",mo_("Validate OTP"),$template);
		$template = str_replace("{{REQUIRED_FIELDS}}",$extraFormFields,$template);
		$template = str_replace('{{EXTRA_POST_DATA}}',$extraPostData,$template);
		return $template;
	}

    
	private function getRequiredFormsSkeleton($otp_type,$from_both)
	{
		$requiredFields = '<form name="f" method="post" action="" id="validation_goBack_form">
				<input id="validation_goBack" name="option" value="validation_goBack" type="hidden"/>
			</form>{{SCRIPTS}}';
		$requiredFields = str_replace('{{SCRIPTS}}',$this->getRequiredScripts(),$requiredFields);
		return $requiredFields;
	}

	
	private function getRequiredScripts()
	{
		$scripts = '<style>.mo_customer_validation-modal{display:block!important}</style>';
		if(!$this->preview) {
			$scripts .= '<script>function mo_validation_goback(){document.getElementById("validation_goBack_form").submit();}</script>';
		} else {
			$scripts .=  '<script>$mo=jQuery;$mo("#mo_validate_form").submit(function(e){e.preventDefault();});</script>';
		}
		return $scripts;
	}
}