<?php

use OTP\Handler\Forms\FormidableForm;

$handler 				    = FormidableForm::instance();
$frm_form_enabled		    = $handler->isFormEnabled() ? "checked" : "";
$frm_form_hidden		    = $frm_form_enabled== "checked" ? "" : "hidden";
$frm_form_enabled_type      = $handler->getOtpTypeEnabled();
$frm_form_list 		        = admin_url().'admin.php?page=formidable';
$frm_form_otp_enabled       = $handler->getFormDetails();
$frm_form_type_phone 	    = $handler->getPhoneHTMLTag();
$frm_form_type_email 	    = $handler->getEmailHTMLTag();
$button_text                = $handler->getButtonText();
$form_name                  = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/FormidableForm.php';