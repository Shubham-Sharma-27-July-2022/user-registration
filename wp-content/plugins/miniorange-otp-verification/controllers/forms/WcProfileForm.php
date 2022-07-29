<?php

use OTP\Handler\Forms\WcProfileForm;

$handler                        = WcProfileForm::instance();
$wc_acc_enabled 			    = $handler->isFormEnabled() ? "checked" : "";
$wc_acc_hidden 				    = $wc_acc_enabled=="checked" ? "" : "hidden";
$wc_acc_enabled_type		    = $handler->getOtpTypeEnabled();
$wc_profile_field_key    	    = $handler->getPhoneKeyDetails();
$wc_acc_forms 				    = admin_url().'my-account/edit-account/';
$wc_acc_type_phone	 		    = $handler->getPhoneHTMLTag();
$wc_acc_type_email              = $handler->getEmailHTMLTag();
$wc_acc_restrict_duplicates     = $handler->restrictDuplicates()? "checked" : "";
$form_name                      = $handler->getFormName();
$wc_acc_button_text             = $handler->getButtonText();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/WcProfileForm.php';