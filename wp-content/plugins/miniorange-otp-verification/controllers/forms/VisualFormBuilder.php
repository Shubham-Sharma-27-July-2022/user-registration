<?php

use OTP\Handler\Forms\VisualFormBuilder;

$handler 				    = VisualFormBuilder::instance();
$visual_form_enabled		= $handler->isFormEnabled() ? "checked" : "";
$visual_form_hidden		    = $visual_form_enabled== "checked" ? "" : "hidden";
$visual_form_enabled_type   = $handler->getOtpTypeEnabled();
$visual_form_list 		    = admin_url().'admin.php?page=visual-form-builder';
$visual_form_otp_enabled    = $handler->getFormDetails();
$visual_form_type_phone 	= $handler->getPhoneHTMLTag();
$visual_form_type_email 	= $handler->getEmailHTMLTag();
$button_text                = $handler->getButtonText();
$form_name                  = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/VisualFormBuilder.php';