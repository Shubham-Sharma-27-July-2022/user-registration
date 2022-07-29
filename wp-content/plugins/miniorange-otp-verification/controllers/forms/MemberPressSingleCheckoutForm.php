<?php

use OTP\Handler\Forms\MemberPressSingleCheckoutForm;

$handler 			= MemberPressSingleCheckoutForm::instance();
$mrp_single_registration   = $handler->isFormEnabled()  ? "checked" : "";
$mrp_single_default_hidden	= $mrp_single_registration== "checked" ? "" : "hidden";
$mrp_single_default_type	= $handler->getOtpTypeEnabled();
$mrp_single_field_key		= $handler->getPhoneKeyDetails();
$mrp_single_fields			= admin_url().'admin.php?page=memberpress-options#mepr-fields';
$mrp_singlereg_phone_type 	= $handler->getPhoneHTMLTag();
$mrp_singlereg_email_type 	= $handler->getEmailHTMLTag();
$mrp_singlereg_both_type 	= $handler->getBothHTMLTag();
$form_name          = $handler->getFormName();
$mpr_single_anon_only      = $handler->bypassForLoggedInUsers() ? "checked" : "";

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/MemberPressSingleCheckoutForm.php';