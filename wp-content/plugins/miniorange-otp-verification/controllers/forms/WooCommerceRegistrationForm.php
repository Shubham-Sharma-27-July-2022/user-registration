<?php

use OTP\Handler\Forms\WooCommerceRegistrationForm;
use OTP\Helper\MoUtility;


$handler 				  = WooCommerceRegistrationForm::instance();
$woocommerce_registration = (Boolean) $handler->isFormEnabled()  ? "checked" : "";
$wc_hidden 				  = $woocommerce_registration=="checked" ? "" : "hidden";
$wc_enable_type			  = $handler->getOtpTypeEnabled();
$wc_restrict_duplicates   = (Boolean) $handler->restrictDuplicates() ? "checked" : "";
$wc_reg_type_phone 		  = $handler->getPhoneHTMLTag();
$wc_reg_type_email 		  = $handler->getEmailHTMLTag();
$wc_reg_type_both 		  = $handler->getBothHTMLTag();
$form_name                = $handler->getFormName();
$redirect_page            = $handler->redirectToPage();
$redirect_page_id         = MoUtility::isBlank($redirect_page) ? '' : get_page_by_title($redirect_page)->ID;
$is_ajax_form             = $handler->isAjaxForm();
$is_ajax_mode_enabled     = $is_ajax_form ? "checked" : "";
$wc_button_text           = $handler->getButtonText();
$is_redirect_after_registration_enabled = $handler->isredirectToPageEnabled() ? "checked" : "";

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/WooCommerceRegistrationForm.php';