<?php

use OTP\Handler\Forms\WooCommerceProductVendors;

$handler 				    = WooCommerceProductVendors::instance();
$wc_pv_registration         = (Boolean) $handler->isFormEnabled()  ? "checked" : "";
$wc_pv_hidden 				= $wc_pv_registration=="checked" ? "" : "hidden";
$wc_pv_enable_type			= $handler->getOtpTypeEnabled();
$wc_pv_restrict_duplicates  = (Boolean) $handler->restrictDuplicates() ? "checked" : "";
$wc_pv_reg_type_phone 		= $handler->getPhoneHTMLTag();
$wc_pv_reg_type_email 		= $handler->getEmailHTMLTag();
$wc_pv_reg_type_both 		= $handler->getBothHTMLTag();
$form_name                  = $handler->getFormName();
$is_ajax_form               = $handler->isAjaxForm();
$is_ajax_mode_enabled       = $is_ajax_form ? "checked" : "";
$wc_pv_button_text          = $handler->getButtonText();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/WooCommerceProductVendors.php';