<?php

use OTP\Helper\MoUtility;

$otp_blocked_email_domains  = get_mo_option('blocked_domains');
$otp_blocked_phones 		= get_mo_option('blocked_phone_numbers');
$show_trans 				= get_mo_option('show_remaining_trans') ? "checked" : "";
$show_dropdown_on_form 		= get_mo_option('show_dropdown_on_form') ? "checked" : "";
$mo_otp_length 				= get_mo_option('otp_length') ? get_mo_option('otp_length') : 5;
$mo_otp_validity 			= get_mo_option('otp_validity') ? get_mo_option('otp_validity') : 5;
$showTransactionOptions 	= MoUtility::isMG();
$nonce                      = $adminHandler->getNonceValue();
$alphanumeric_disabled      = apply_filters('set_class_exists_aplhanumeric',false) & $disabled !="disabled" ? "" : "disabled";
$globallybanned_disabled    = apply_filters('set_class_exists_globallybanned',false) && $disabled !="disabled" ? "" : "disabled";

include MOV_DIR . 'views/otpsettings.php';