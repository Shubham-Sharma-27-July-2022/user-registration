<?php

use OTP\Handler\Forms\DefaultWordPressRegistrationForm;

$handler                  = DefaultWordPressRegistrationForm::instance();
$default_registration 	  = (Boolean) $handler->isFormEnabled()  ? "checked" : "";
$wp_default_hidden		  = $default_registration== "checked" ? "" : "hidden";
$wp_default_type		  = $handler->getOtpTypeEnabled();
$wp_handle_reg_duplicates = (Boolean) $handler->restrictDuplicates() ? "checked" : "";
$wpreg_phone_type 		  = $handler->getPhoneHTMLTag();
$wpreg_email_type 		  = $handler->getEmailHTMLTag();
$wpreg_both_type 		  = $handler->getBothHTMLTag();
$form_name                = $handler->getFormName();
$auto_activate_users      = $handler->disableAutoActivation() ? "": "checked";

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/DefaultWordPressRegistrationForm.php';