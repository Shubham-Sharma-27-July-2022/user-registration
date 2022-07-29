<?php

use OTP\Handler\MoRegistrationHandler;
use OTP\Helper\MoConstants;
use OTP\Helper\MoUtility;

$url = MoConstants::HOSTNAME.'/moas/login'.'?redirectUrl='.MoConstants::HOSTNAME.'/moas/viewlicensekeys';

$handler 	 = MoRegistrationHandler::instance();

if(get_mo_option('registration_status') === 'MO_OTP_DELIVERED_SUCCESS'
		|| get_mo_option('registration_status')  === 'MO_OTP_VALIDATION_FAILURE'
		|| get_mo_option('registration_status')  === 'MO_OTP_DELIVERED_FAILURE')
{
	$admin_phone = get_mo_option('admin_phone') ? get_mo_option('admin_phone') : "";
    $nonce       = $handler->getNonceValue();
	include MOV_DIR . 'views/account/verify.php';
}
else if (get_mo_option ( 'verify_customer' ))
{
	$admin_email = get_mo_option('admin_email') ? get_mo_option('admin_email') : "";
    $nonce       = $handler->getNonceValue();
	include MOV_DIR . 'views/account/login.php';
}
else if (! MoUtility::micr())
{
	$current_user = wp_get_current_user();
	$admin_phone  = get_mo_option('admin_phone') ? get_mo_option('admin_phone') : "";
	$nonce        = $handler->getNonceValue();
    delete_site_option ( 'password_mismatch' );
    update_mo_option ( 'new_registration', 'true' );
	include MOV_DIR . 'views/account/register.php';
}
else if (MoUtility::micr() && !MoUtility::mclv())
{
    $nonce       = $handler->getNonceValue();
    include MOV_DIR . 'views/account/verify-lk.php';
}
else
{
	$customer_id = get_mo_option('admin_customer_key');
	$api_key     = get_mo_option('admin_api_key');
	$token 		 = get_mo_option('customer_token');
	$vl 		 = MoUtility::mclv() && !MoUtility::isMG();
    $nonce       = $adminHandler->getNonceValue();
    $regnonce    = $handler->getNonceValue();
	include MOV_DIR . 'views/account/profile.php';
}