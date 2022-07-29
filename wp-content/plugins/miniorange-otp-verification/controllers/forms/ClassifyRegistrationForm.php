<?php

use OTP\Handler\Forms\ClassifyRegistrationForm;

$handler                 = ClassifyRegistrationForm::instance();
$classify_enabled 		 = $handler->isFormEnabled() ? "checked" : "";
$classify_hidden 		 = $classify_enabled== "checked" ? "" : "hidden";
$classify_enabled_type 	 = $handler->getOtpTypeEnabled();
$classify_type_phone 	 = $handler->getPhoneHTMLTag();
$classify_type_email	 = $handler->getEmailHTMLTag();
$form_name               = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/ClassifyRegistrationForm.php';