<?php

use OTP\Handler\Forms\MemberPressRegistrationForm;

$handler 			= MemberPressRegistrationForm::instance();
$mrp_registration   = $handler->isFormEnabled()  ? "checked" : "";
$mrp_default_hidden	= $mrp_registration== "checked" ? "" : "hidden";
$mrp_default_type	= $handler->getOtpTypeEnabled();
$mrp_field_key		= $handler->getPhoneKeyDetails();
$mrp_fields			= admin_url().'admin.php?page=memberpress-options#mepr-fields';
$mrpreg_phone_type 	= $handler->getPhoneHTMLTag();
$mrpreg_email_type 	= $handler->getEmailHTMLTag();
$mrpreg_both_type 	= $handler->getBothHTMLTag();
$form_name          = $handler->getFormName();
$mpr_anon_only      = $handler->bypassForLoggedInUsers() ? "checked" : "";

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/MemberPressRegistrationForm.php';