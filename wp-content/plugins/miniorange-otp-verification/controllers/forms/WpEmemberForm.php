<?php

use OTP\Handler\Forms\WpEmemberForm;

$handler                      = WpEmemberForm::instance();
$emember_enabled 			  = $handler->isFormEnabled() ? "checked" : "";
$emember_hidden 			  = $emember_enabled== "checked" ? "" : "hidden";
$emember_enable_type		  = $handler->getOtpTypeEnabled();
$form_settings_link 		  = admin_url().'admin.php?page=eMember_settings_menu&tab=4';
$emember_type_phone 		  = $handler->getPhoneHTMLTag();
$emember_type_email 		  = $handler->getEmailHTMLTag();
$emember_type_both 		  	  = $handler->getBothHTMLTag();
$form_name                    = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/WpEmemberForm.php';