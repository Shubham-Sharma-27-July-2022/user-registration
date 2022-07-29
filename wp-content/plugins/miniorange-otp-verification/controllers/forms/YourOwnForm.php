<?php

use OTP\Helper\MoMessages;
use OTP\Helper\MoUtility;
use OTP\Handler\Forms\YourOwnForm;

$handler 				= YourOwnForm::instance();
$custom_form_enabled 		= (Boolean) $handler->isFormEnabled() ? "checked" : "";
$custom_form_hidden 		= $custom_form_enabled== "checked" ? "" : "hidden";
$custom_form_enabled_type 	= $handler->getOtpTypeEnabled();
$custom_form_field_list 	= admin_url().'admin.php?page=custom_form';
$custom_form_field_key 		= $handler->getEmailKeyDetails();
$custom_form_type_phone 	= $handler->getPhoneHTMLTag();
$custom_form_type_email 	= $handler->getEmailHTMLTag();
$form_name          		= $handler->getFormName();
$button_text 				= $handler->getButtonText();



$custom_form_submit_selector 	= 	$handler->getSubmitKeyDetails();
$custom_form_field_selector 	= 	$handler->getFieldKeyDetails();

include MOV_DIR . 'views/forms/YourOwnForm.php';