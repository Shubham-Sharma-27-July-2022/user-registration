<?php

use OTP\Handler\Forms\UserProfileMadeEasyRegistrationForm;

$handler                = UserProfileMadeEasyRegistrationForm::instance();
$upme_enabled 			= $handler->isFormEnabled() ? "checked" : "";
$upme_hidden 			= $upme_enabled== "checked" ? "" : "hidden";
$upme_enable_type		= $handler->getOtpTypeEnabled();
$upme_field_list 		= admin_url().'admin.php?page=upme-field-customizer';
$upme_field_key    	 	= $handler->getPhoneKeyDetails();
$upme_type_phone 		= $handler->getPhoneHTMLTag();
$upme_type_email 		= $handler->getEmailHTMLTag();
$upme_type_both 		= $handler->getBothHTMLTag();
$form_name              = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/UserProfileMadeEasyRegistrationForm.php';