<?php

use OTP\Handler\Forms\SimplrRegistrationForm;

$handler                  = SimplrRegistrationForm::instance();
$simplr_enabled			  = $handler->isFormEnabled() ? "checked" : "";
$simplr_hidden			  = $simplr_enabled=="checked" ? "" : "hidden";
$simplr_enabled_type  	  = $handler->getOtpTypeEnabled();
$simplr_fields_page       = admin_url().'options-general.php?page=simplr_reg_set&regview=fields&orderby=name&order=desc';
$simplr_field_key  		  = $handler->getPhoneKeyDetails();
$simplr_type_phone 		  = $handler->getPhoneHTMLTag();
$simplr_type_email 		  = $handler->getEmailHTMLTag();
$simplr_type_both 		  = $handler->getBothHTMLTag();
$form_name                = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/SimplrRegistrationForm.php';