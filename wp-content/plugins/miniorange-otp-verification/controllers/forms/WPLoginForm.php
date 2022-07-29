<?php

use OTP\Handler\Forms\WPLoginForm;

$handler                = WPLoginForm::instance();
$wp_login_enabled 		= (Boolean) $handler->isFormEnabled() ? "checked" : "";
$wp_login_hidden 		= $wp_login_enabled == "checked" ? "" : "hidden";
$wp_login_enabled_type 	= (Boolean) $handler->savePhoneNumbers() ? "checked" : "";
$wp_login_field_key    	= $handler->getPhoneKeyDetails();
$wp_login_admin			= (Boolean) $handler->byPassCheckForAdmins() ? "checked" : "";
$wp_login_with_phone 	= (Boolean) $handler->allowLoginThroughPhone() ? "checked" : "";
$wp_handle_duplicates   = (Boolean) $handler->restrictDuplicates() ? "checked" : "";
$wp_enabled_type        = $handler->getOtpTypeEnabled();
$wp_phone_type          = $handler->getPhoneHTMLTag();
$wp_email_type          = $handler->getEmailHTMLTag();
$form_name              = $handler->getFormName();
$skip_pass              = $handler->getSkipPasswordCheck() ? "checked" : "" ;
$skip_pass_fallback_div = $handler->getSkipPasswordCheck() ? "block" : "hidden";
$skip_pass_fallback     = $handler->getSkipPasswordCheckFallback() ? "checked" : "";
$user_field_text        = $handler->getUserLabel();
$otpd_enabled           = $handler->isDelayOtp() ? "checked" : "";
$otpd_enabled_div       = $handler->isDelayOtp() ? "block" : "hidden";
$otpd_time_interval     = $handler->getDelayOtpInterval();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/WPLoginForm.php';