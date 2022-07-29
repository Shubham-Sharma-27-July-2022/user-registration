<?php

use OTP\Handler\Forms\UltimateMemberProfileForm;

$handler                        = UltimateMemberProfileForm::instance();
$um_acc_enabled 			    = $handler->isFormEnabled() ? "checked" : "";
$um_acc_hidden 				    = $um_acc_enabled=="checked" ? "" : "hidden";
$um_acc_enabled_type		    = $handler->getOtpTypeEnabled();
$um_profile_field_key    	    = $handler->getPhoneKeyDetails();
$um_acc_forms 				    = admin_url().'edit.php?post_type=um_form';
$um_acc_type_phone	 		    = $handler->getPhoneHTMLTag();
$um_acc_type_email	 		    = $handler->getEmailHTMLTag();
$um_acc_type_both	 		    = $handler->getBothHTMLTag();
$um_acc_restrict_duplicates     = $handler->restrictDuplicates()? "checked" : "";
$form_name                      = $handler->getFormName();
$um_acc_button_text             = $handler->getButtonText();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/UltimateMemberProfileForm.php';