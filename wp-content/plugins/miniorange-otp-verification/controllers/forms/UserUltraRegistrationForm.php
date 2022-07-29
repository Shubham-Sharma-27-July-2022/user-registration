<?php

use OTP\Handler\Forms\UserUltraRegistrationForm;

$handler                  = UserUltraRegistrationForm::instance();
$uultra_enabled           = $handler->isFormEnabled() ? "checked" : "";
$uultra_hidden 			  = $uultra_enabled == "checked" ? "" : "hidden";
$uultra_enable_type		  = $handler->getOtpTypeEnabled();
$uultra_form_list 		  = admin_url().'admin.php?page=userultra&tab=fields';
$uultra_field_key    	  = $handler->getPhoneKeyDetails();
$uultra_type_phone 		  = $handler->getPhoneHTMLTag();
$uultra_type_email 		  = $handler->getEmailHTMLTag();
$uultra_type_both 		  = $handler->getBothHTMLTag();
$form_name                = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/UserUltraRegistrationForm.php';