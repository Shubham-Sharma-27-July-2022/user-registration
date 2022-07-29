<?php

use OTP\Helper\MoMessages;
use OTP\Helper\MoUtility;
use OTP\Handler\CustomForm;

$nonce 				    		= $adminHandler->getNonceValue();
$handler 						= CustomForm::instance();
$custom_form_submit_selector 	= $handler->getSubmitKeyDetails();
$custom_form_enabled			= $custom_form_submit_selector !="" || empty($custom_form_submit_selector) ? true : false;
$custom_form_otp_type 			= get_mo_option("cf_enable_type","mo_otp_");
$custom_form_field_selector 	= $handler->getFieldKeyDetails();
$custom_form_type_phone 		= $handler->getPhoneHTMLTag();
$custom_form_type_email 		= $handler->getEmailHTMLTag();
$button_text					= $handler->getButtonText();

include MOV_DIR . 'views/customForm.php';