<?php

use OTP\Handler\Forms\PaidMembershipForm;

$handler 				  = PaidMembershipForm::instance();
$pmpro_enabled 		   	  = $handler->isFormEnabled() ? "checked" : "";
$pmpro_hidden 			  = $pmpro_enabled== "checked" ? "" : "hidden";
$pmpro_enabled_type 	  = $handler->getOtpTypeEnabled();
$pmpro_type_phone 		  = $handler->getPhoneHTMLTag();
$pmpro_type_email 		  = $handler->getEmailHTMLTag();
$form_name                = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/PaidMembershipForm.php';
