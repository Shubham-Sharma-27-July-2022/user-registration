<?php

use OTP\Handler\Forms\BuddyPressRegistrationForm;


$handler 				= BuddyPressRegistrationForm::instance();
$bbp_enabled 			= $handler->isFormEnabled() ? "checked" : "";
$bbp_hidden 			= $bbp_enabled=="checked" ? "" : "hidden";
$bbp_enable_type 		= $handler->getOtpTypeEnabled();
$bbp_fields 			= admin_url().'users.php?page=bp-profile-setup';
$bbp_field_key			= $handler->getPhoneKeyDetails();
$automatic_activation   = $handler->disableAutoActivation() ? "checked" : "";
$bbp_type_phone 		= $handler->getPhoneHTMLTag();
$bbp_type_email 		= $handler->getEmailHTMLTag();
$bbp_type_both	 		= $handler->getBothHTMLTag();
$form_name              = $handler->getFormName();
$restrict_duplicates    = $handler->restrictDuplicates() ? "checked" : "";

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/BuddyPressRegistrationForm.php';


