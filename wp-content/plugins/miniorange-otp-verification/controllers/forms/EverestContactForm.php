<?php


use OTP\Handler\Forms\EverestContactForm;

$handler 							        = EverestContactForm::instance();
$is_everest_contact_enabled               	= (Boolean) $handler->isFormEnabled()  ? "checked" : "";
$is_everest_contact_hidden		    	  	= $is_everest_contact_enabled== "checked" ? "" : "hidden";
$everest_contact_enabled_type  			  	= $handler->getOtpTypeEnabled();
$everest_contact_list_of_forms_otp_enabled  = $handler->getFormDetails();
$everest_contact_form_list				  	= admin_url().'admin.php?page=evf-builder';
$button_text 					  	        = $handler->getButtonText();
$everest_contact_phone_type 		      	= $handler->getPhoneHTMLTag();
$everest_contact_email_type 		      	= $handler->getEmailHTMLTag();
$form_name                                  = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/EverestContactForm.php';
