<?php

use OTP\Handler\Forms\Edumareg;

$handler                = Edumareg::instance();
$edumareg_enabled 		= $handler->isFormEnabled() ? "checked" : "";
$edumareg_hidden 		= $edumareg_enabled== "checked" ? "" : "hidden";
$edumareg_enabled_type = $handler->getOtpTypeEnabled();
$edumareg_type_phone 	= $handler->getPhoneHTMLTag();
$edumareg_type_email 	= $handler->getEmailHTMLTag();
$form_name              = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/Edumareg.php';