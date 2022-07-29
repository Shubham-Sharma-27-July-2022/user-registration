<?php

use OTP\Handler\Forms\WPClientRegistration;


$handler 				    = WPClientRegistration::instance();
$wp_client_enabled 			= $handler->isFormEnabled() ? "checked" : "";
$wp_client_hidden 			= $wp_client_enabled=="checked" ? "" : "hidden";
$wp_client_enable_type 		= $handler->getOtpTypeEnabled();
$wp_client_type_phone 		= $handler->getPhoneHTMLTag();
$wp_client_type_email 		= $handler->getEmailHTMLTag();
$wp_client_type_both	 	= $handler->getBothHTMLTag();
$form_name                  = $handler->getFormName();
$restrict_duplicates        = $handler->restrictDuplicates() ? "checked" : "";

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/WPClientRegistration.php';