<?php

use OTP\Handler\Forms\RegistrationMagicForm;

$handler 				  = RegistrationMagicForm::instance();
$crf_enabled 			  = $handler->isFormEnabled() ? "checked" : "";
$crf_hidden  			  = $crf_enabled== "checked" ? "" : "hidden";
$crf_enable_type		  = $handler->getOtpTypeEnabled();
$crf_form_list  		  = admin_url().'admin.php?page=rm_form_manage';
$crf_form_otp_enabled     = $handler->getFormDetails();
$crf_type_phone 		  = $handler->getPhoneHTMLTag();
$crf_type_email 		  = $handler->getEmailHTMLTag();
$crf_type_both  		  = $handler->getBothHTMLTag();
$form_name                = $handler->getFormName();
$restrict_duplicates    = $handler->restrictDuplicates() ? "checked" : "";

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR .'views/forms/RegistrationMagicForm.php';
