<?php

use OTP\Handler\Forms\PieRegistrationForm;

$handler                  = PieRegistrationForm::instance();
$pie_enabled 			  = $handler->isFormEnabled() ? "checked" : "";
$pie_hidden 			  = $pie_enabled== "checked" ? "" : "hidden";
$pie_enable_type		  = $handler->getOtpTypeEnabled();
$pie_field_key    	 	  = $handler->getPhoneKeyDetails();
$pie_type_phone 		  = $handler->getPhoneHTMLTag();
$pie_type_email 		  = $handler->getEmailHTMLTag();
$pie_type_both 		  	  = $handler->getBothHTMLTag();
$form_name                = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/PieRegistrationForm.php';