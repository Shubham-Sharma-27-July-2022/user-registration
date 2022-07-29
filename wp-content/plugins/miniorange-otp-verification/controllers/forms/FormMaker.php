<?php

use OTP\Handler\Forms\FormMaker;

$handler                        = FormMaker::instance();
$formMaker_form_enabled 		= (Boolean) $handler->isFormEnabled() ? "checked" : "";
$formMaker_form_hidden 		    = $formMaker_form_enabled== "checked" ? "" : "hidden";
$formmaker_form_list 		    = admin_url().'admin.php?page=manage_fm';
$formMaker_form_enabled_type  	= $handler->getOtpTypeEnabled();
$formMaker_form_type_email      = $handler->getEmailHTMLTag();
$formMaker_form_type_phone      = $handler->getPhoneHTMLTag();
$formMaker_form_otp_enabled     = $handler->getFormDetails();
$form_name                      = $handler->getFormName();
$button_text                    = $handler->getButtonText();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/FormMaker.php';