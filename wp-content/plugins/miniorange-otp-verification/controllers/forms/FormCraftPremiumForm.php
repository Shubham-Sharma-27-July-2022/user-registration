<?php

use OTP\Handler\Forms\FormCraftPremiumForm;

$handler 				   = FormCraftPremiumForm::instance();
$fcpremium_enabled		   = $handler->isFormEnabled() ? "checked" : "";
$fcpremium_hidden		   = $fcpremium_enabled== "checked" ? "" : "hidden";
$fcpremium_enabled_type    = $handler->getOtpTypeEnabled();
$fcpremium_list 		   = admin_url().'admin.php?page=formcraft-dashboard';
$fcpremium_otp_enabled     = $handler->getFormDetails();
$fcpremium_type_phone 	   = $handler->getPhoneHTMLTag();
$fcpremium_type_email 	   = $handler->getEmailHTMLTag();
$form_name                 = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/FormCraftPremiumForm.php';