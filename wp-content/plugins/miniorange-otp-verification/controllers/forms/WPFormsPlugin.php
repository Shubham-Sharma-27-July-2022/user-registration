<?php

use OTP\Handler\Forms\WPFormsPlugin;

$handler 						    = WPFormsPlugin::instance();
$is_wpform_enabled                  = (Boolean) $handler->isFormEnabled()  ? "checked" : "";
$is_wpform_hidden		    	    = $is_wpform_enabled== "checked" ? "" : "hidden";
$wpform_enabled_type  			    = $handler->getOtpTypeEnabled();
$wpform_list_of_forms_otp_enabled   = $handler->getFormDetails();
$wpform_form_list				    = admin_url().'admin.php?page=wpforms-overview';
$button_text 					    = $handler->getButtonText();
$wpform_phone_type 		            = $handler->getPhoneHTMLTag();
$wpform_email_type 		            = $handler->getEmailHTMLTag();
$wpform_both_type 		            = $handler->getBothHTMLTag();
$form_name                          = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/WPFormsPlugin.php';