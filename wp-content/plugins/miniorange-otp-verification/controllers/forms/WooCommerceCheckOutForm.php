<?php

use OTP\Handler\Forms\WooCommerceCheckOutForm;

$handler                    = WooCommerceCheckOutForm::instance();
$wc_checkout 			    = $handler->isFormEnabled() ? "checked" : "";
$wc_checkout_hidden		    = $wc_checkout=="checked" ? "" : "hidden";
$wc_checkout_enable_type    = $handler->getOtpTypeEnabled();
$guest_checkout 		    = $handler->isGuestCheckoutOnlyEnabled()  ? "checked" : "";
$checkout_button 		    = $handler->showButtonInstead() ? "checked" : "";
$checkout_popup 		    = $handler->isPopUpEnabled()  ? "checked" : "";
$checkout_payment_plans     = $handler->getPaymentMethods();
$checkout_selection         = $handler->isSelectivePaymentEnabled() ? "checked" : "";
$checkout_selection_hidden  = $checkout_selection=="checked" ? "" : "hidden";
$wc_type_phone 			    = $handler->getPhoneHTMLTag();
$wc_type_email 			    = $handler->getEmailHTMLTag();
$button_text                = $handler->getButtonText();
$form_name                  = $handler->getFormName();
$disable_autologin          = $handler->isAutoLoginDisabled()  ? "checked" : "";
$restrict_duplicates        = $handler->restrictDuplicates() ? "checked" : "";

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/WooCommerceCheckOutForm.php';