<?php

use OTP\Handler\Forms\Edumalog;

$handler                = Edumalog::instance();
$edumalog_enabled 		= $handler->isFormEnabled() ? "checked" : "";
$edumalog_hidden 		= $edumalog_enabled== "checked" ? "" : "hidden";
$edumalog_enabled_type = $handler->getOtpTypeEnabled();
$edumalog_type_phone 	= $handler->getPhoneHTMLTag();
$edumalog_type_email 	= $handler->getEmailHTMLTag();
$edumalog_phone_field_key = $handler->getPhoneKeyDetails();
$form_name              = $handler->getFormName();
$edumalog_log_bypass  = $handler->byPassCheckForAdmins() ? "checked" : "";

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/Edumalog.php';