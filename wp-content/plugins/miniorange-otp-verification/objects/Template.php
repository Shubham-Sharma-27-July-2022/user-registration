<?php

namespace OTP\Objects;

use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoUtility;

if(! defined( 'ABSPATH' )) exit;


abstract class Template extends BaseActionHandler implements MoITemplate
{

    
	protected $key;

    
	protected $templateEditorID;

    
	protected $nonce;

    
	protected $preview = FALSE;

    
	protected $jqueryUrl;

    
	protected $img;

    
	public $paneContent;

    
	public $messageDiv;

    
    protected $successMessageDiv;

    
	public static $templateEditor   = [
		'wpautop' => false,
        'media_buttons' => false,
        'textarea_rows' => 20,
        'tabindex' => '',
		'tabfocus_elements' => ':prev,:next',
        'editor_css' => '',
        'editor_class' => '',
        'teeny' => false,
        'dfw' => false,
		'tinymce' => false,
        'quicktags' => true
	];

    
    protected $requiredTags = [ "{{JQUERY}}","{{GO_BACK_ACTION_CALL}}","{{FORM_ID}}",
        "{{REQUIRED_FIELDS}}","{{REQUIRED_FORMS_SCRIPTS}}" ];


    protected function __construct()
	{
	    parent::__construct();

        $this->jqueryUrl =  '';

        $this->img = "<div style='display:table;text-align:center;'>".
                        "<img src='{{LOADER_CSV}}'>".
                      "</div>";

        $this->paneContent      = "<div style='text-align:center;width: 100%;height: 450px;display: block;".
                                        "margin-top: 40%;vertical-align: middle;'>".
                                        "{{CONTENT}}".
                                  "</div>";

        $this->messageDiv       = "<div style='font-style: italic;font-weight: 600;color: #23282d;".
                                        "font-family:Segoe UI,Helvetica Neue,sans-serif;".
                                        "color:#942828;'>".
                                    "{{MESSAGE}}".
                                  "</div>";

        $this->successMessageDiv= "<div style='font-style: italic;font-weight: 600;color: #23282d;".
                                        "font-family:Segoe UI,Helvetica Neue,sans-serif;color:#138a3d;'>".
                                            "{{MESSAGE}}".
                                   "</div>";

		$this->img = str_replace("{{LOADER_CSV}}",MOV_LOADER_URL,$this->img);
		$this->_nonce = 'mo_popup_options';
		add_filter( 'mo_template_defaults', array($this,'getDefaults'), 1,1);
		add_filter( 'mo_template_build', array($this,'build'), 1,5);
		add_action( 'admin_post_mo_preview_popup', array($this,'showPreview'));
		add_action( 'admin_post_mo_popup_save', array($this,'savePopup'));
	}


	
	public function showPreview()
	{
		if(array_key_exists('popuptype',$_POST)
		   && $_POST['popuptype']!=$this->getTemplateKey()) return;
		if(!$this->isValidRequest()) return;
		$message = "<i>" . mo_("PopUp Message shows up here.") . "</i>";
		$otp_type = VerificationType::TEST;
		$template = stripslashes($_POST[$this->getTemplateEditorId()]);
		$this->validateRequiredFields($template);
		$from_both = false;
		$this->preview = TRUE;
		wp_send_json(MoUtility::createJson(
		    $this->parse($template,$message,$otp_type,$from_both),
			MoConstants::SUCCESS_JSON_TYPE
        ));
	}

	
	public function savePopup()
	{
		if(!$this->isTemplateType() || !$this->isValidRequest()) return;
		$template = stripslashes($_POST[$this->getTemplateEditorId()]);
		$this->validateRequiredFields($template);
		$email_templates = maybe_unserialize(get_mo_option('custom_popups'));
		$email_templates[$this->getTemplateKey()] = $template;
		update_mo_option('custom_popups',$email_templates);
		wp_send_json(MoUtility::createJson(
		    $this->showSuccessMessage(MoMessages::showMessage(MoMessages::TEMPLATE_SAVED)),
			MoConstants::SUCCESS_JSON_TYPE
        ));
	}


	
	public function build($template,$templateType,$message,$otp_type,$from_both)
	{
		if(strcasecmp($templateType,$this->getTemplateKey())!=0) return $template;
		$email_templates = maybe_unserialize(get_mo_option('custom_popups'));
		$template = $email_templates[$this->getTemplateKey()];
		return $this->parse($template,$message,$otp_type,$from_both);
	}


	
	protected function validateRequiredFields($template)
	{
		foreach($this->requiredTags as $tag) {
			if (strpos($template, $tag) === FALSE) {
				$message = str_replace(
				    "{{MESSAGE}}",
                    MoMessages::showMessage(MoMessages::REQUIRED_TAGS,array('TAG'=>$tag)),
                    $this->messageDiv
                );
				wp_send_json(MoUtility::createJson(
				    str_replace("{{CONTENT}}", $message,$this->paneContent),
                    MoConstants::ERROR_JSON_TYPE)
                );
			}
		}
		if(MoUtility::checkForScriptTags($template))
		{
			$message = str_replace(
				    "{{MESSAGE}}",
                    MoMessages::showMessage(MoMessages::INVALID_SCRIPTS),
                    $this->messageDiv
                );
			wp_send_json(MoUtility::createJson(
				    str_replace("{{CONTENT}}", $message ,$this->paneContent),
                    MoConstants::ERROR_JSON_TYPE)
                );
		}
	}


	
	protected function showSuccessMessage($message)
	{
		$message = str_replace("{{MESSAGE}}",$message,$this->successMessageDiv);
		return str_replace("{{CONTENT}}",$message,$this->paneContent);
	}


	
	protected function showMessage($message)
	{
		$message = str_replace("{{MESSAGE}}",$message,$this->messageDiv);
		return str_replace("{{CONTENT}}",$message,$this->paneContent);
	}


	
	protected function isTemplateType()
	{
		return array_key_exists('popuptype',$_POST) && strcasecmp($_POST['popuptype'],$this->getTemplateKey())==0;
	}

	

	
	public function getTemplateKey() { return $this->key; }

	
	public function getTemplateEditorId(){ return $this->templateEditorID; }
}