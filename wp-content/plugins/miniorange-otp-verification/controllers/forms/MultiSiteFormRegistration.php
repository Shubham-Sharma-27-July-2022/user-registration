<?php

use OTP\Handler\Forms\MultiSiteFormRegistration;

$handler 			    = MultiSiteFormRegistration::instance();
$multisite_enabled      = $handler->isFormEnabled()  ? "checked" : "";
$multisite_hidden	    = $multisite_enabled== "checked" ? "" : "hidden";
$multisite_enabled_type	= $handler->getOtpTypeEnabled();

$multisite_type_phone 	= $handler->getPhoneHTMLTag();
$multisite_type_email 	= $handler->getEmailHTMLTag();
$form_name              = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/MultiSiteFormRegistration.php';