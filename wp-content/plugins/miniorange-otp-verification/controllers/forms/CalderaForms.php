<?php

use OTP\Handler\Forms\CalderaForms;

$handler 							= CalderaForms::instance();
$is_caldera_enabled               	= (Boolean) $handler->isFormEnabled()  ? "checked" : "";
$is_caldera_hidden		    	  	= $is_caldera_enabled== "checked" ? "" : "hidden";
$caldera_enabled_type  			  	= $handler->getOtpTypeEnabled();
$caldera_list_of_forms_otp_enabled  = $handler->getFormDetails();
$caldera_form_list				  	= admin_url().'admin.php?page=caldera-forms';
$button_text 					  	= $handler->getButtonText();
$caldera_phone_type 		      	= $handler->getPhoneHTMLTag();
$caldera_email_type 		      	= $handler->getEmailHTMLTag();
$form_name                          = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/CalderaForms.php';
