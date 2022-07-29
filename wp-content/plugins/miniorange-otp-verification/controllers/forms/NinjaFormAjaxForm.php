<?php

use OTP\Handler\Forms\NinjaFormAjaxForm;

$handler 						  = NinjaFormAjaxForm::instance();
$ninja_ajax_form_enabled		  = $handler->isFormEnabled() ? "checked" : "";
$ninja_ajax_form_hidden		  	  = $ninja_ajax_form_enabled== "checked" ? "" : "hidden";
$ninja_ajax_form_enabled_type  	  = $handler->getOtpTypeEnabled();
$ninja_ajax_form_list 		      = admin_url().'admin.php?page=ninja-forms';
$ninja_ajax_form_otp_enabled      = $handler->getFormDetails();
$ninja_ajax_form_type_phone 	  = $handler->getPhoneHTMLTag();
$ninja_ajax_form_type_email 	  = $handler->getEmailHTMLTag();
$button_text                      = $handler->getButtonText();
$form_name                        = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/NinjaFormAjaxForm.php';