<?php

use OTP\Handler\Forms\ProfileBuilderRegistrationForm;

$handler            = ProfileBuilderRegistrationForm::instance();
$pb_enabled         = $handler->isFormEnabled() ? "checked" : "";
$pb_hidden 	        = $pb_enabled=="checked" ? "" : "hidden";
$pb_enable_type     = $handler->getOtpTypeEnabled();
$pb_phone_key       = $handler->getPhoneKeyDetails();
$pb_fields          = admin_url() . 'admin.php?page=manage-fields';
$pb_reg_type_phone  = $handler->getPhoneHTMLTag();
$pb_reg_type_email 	= $handler->getEmailHTMLTag();
$pb_reg_type_both 	= $handler->getBothHTMLTag();
$form_name          = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/ProfileBuilderRegistrationForm.php';