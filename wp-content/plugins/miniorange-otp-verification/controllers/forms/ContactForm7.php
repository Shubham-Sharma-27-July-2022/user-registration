<?php

use OTP\Handler\Forms\ContactForm7;

$handler            = ContactForm7::instance();
$cf7_enabled 		= (Boolean) $handler->isFormEnabled() ? "checked" : "";
$cf7_hidden 		= $cf7_enabled== "checked" ? "" : "hidden";
$cf7_enabled_type 	= $handler->getOtpTypeEnabled();
$cf7_field_list 	= admin_url().'admin.php?page=wpcf7';
$cf7_field_key 		= $handler->getEmailKeyDetails();
$cf7_type_phone 	= $handler->getPhoneHTMLTag();
$cf7_type_email 	= $handler->getEmailHTMLTag();
$form_name          = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/ContactForm7.php';