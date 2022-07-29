<?php

use OTP\Helper\MoConstants;
use OTP\Helper\MoUtility;
use OTP\Objects\Tabs;

$form_action 	  			= MoConstants::HOSTNAME.'/moas/login';
$redirect_url	  			= MoConstants::HOSTNAME .'/moas/initializepayment';
$free_plan_name 			= 'FREE';
$gateway_plus_addon_name	= 'CUSTOM GATEWAY <br/>WITH ADDONS';
$twilio_gateway_plus_addon_name	= 'TWILIO GATEWAY <br/>WITH ADDONS';
$enterprise_name	        = 'ENTERPRISE PLAN';
$mo_gateway_plan_name		= 'MINIORANGE GATEWAY <br/>WITH ADDONS';
$free_plan_price			= 'Free';
$gateway_plus_addon			= '$29';
$twilio_gateway_plus_addon	= '$49';
$enterprise_addon		    = '$99';
$mo_gateway_plan			= '$0';
$vl 						= MoUtility::mclv() && !MoUtility::isMG();
$nonce              		= $adminHandler->getNonceValue();

$formSettings = add_query_arg( array('page' => $tabDetails->_tabDetails[Tabs::FORMS]->_menuSlug), $_SERVER['REQUEST_URI'] );

$free_plan_features = [
	"<span class='unavailable'></span>"	. mo_(" Premium Integration With Twilio SMS Gateway"),
	"<span class='available'></span>" 	. mo_(" 40+ registration forms supported"),
	"<span class='available'></span>" 	. mo_(" WooCommerce Forms"),
	"<span class='available'></span>" 	. mo_(" Contact Form 7 Forms"),
	"<span class='available'></span>" 	. mo_(" WooCommerce SMS Notifications"),
	"<span class='available'></span>" 	. mo_(" Ultimate Member SMS Notifications"),
	"<span class='available'></span>" 	. mo_(" Passwordless Login"),
	"<span class='available'></span>" 	. mo_(" Password Reset Over OTP"),
	"<span class='unavailable'></span>" . mo_(" Wordpress Admin Notification"),
	"<span class='unavailable'></span>" . mo_(" OTP Verification for Android/IOS App"),
	"<span class='unavailable'></span>" . mo_(" OTP Over Phone Call"),
	"<span class='unavailable'></span>" . mo_(" Custom SMS/SMTP Gateway"),
	"<span class='unavailable'></span>" . mo_(" Back Up SMS Gateway"),
	"<span class='available'></span>" 	. mo_(" Custom Email Template"),
	"<span class='available'></span>" 	. mo_(" Custom SMS Template"),
	"<span class='available'></span>" 	. mo_(" Block Email Domains"),
	"<span class='available'></span>" 	. mo_(" Block SMS numbers"),
	"<span class='unavailable'></span>" . mo_(" Globally banned OTP"),
	"<span class='unavailable'></span>" . mo_(" Globally banned Phone Number"),
	"<span class='available'></span>" 	. mo_(" Send Custom SMS Messages"),
	"<span class='available'></span>" 	. mo_(" Country Code Dropdown for form"),
	"<span class='available'></span>" 	. mo_(" Custom OTP Length"),
	"<span class='available'></span>" 	. mo_(" Custom OTP Validity Time"),
	"<span class='available'></span>" 	. mo_(" OTP pop-up Customization"),
];

$gateway_plus_addon_features = [
	"<span class='unavailable'></span>"	. mo_(" Premium Integration With Twilio SMS Gateway"),
	"<span class='available'></span>" 	. mo_(" 40+ registration forms supported"),
	"<span class='available'></span>" 	. mo_(" WooCommerce Forms"),
	"<span class='available'></span>" 	. mo_(" Contact Form 7 Forms"),
	"<span class='available'></span>" 	. mo_(" WooCommerce SMS Notifications"),
	"<span class='available'></span>" 	. mo_(" Ultimate Member SMS Notifications"),
	"<span class='available'></span>" 	. mo_(" Passwordless Login"),
	"<span class='available'></span>" 	. mo_(" Password Reset Over OTP"),
	"<span class='unavailable'></span>" . mo_(" Wordpress Admin Notification"),
	"<span class='unavailable'></span>" . mo_(" OTP Verification for Android/IOS App"),
	"<span class='unavailable'></span>" . mo_(" OTP Over Phone Call"),
	"<span class='available'></span>" 	. mo_(" Custom SMS/SMTP Gateway"),
	"<span class='unavailable'></span>" . mo_(" Back Up SMS Gateway"),
	"<span class='available'></span>" 	. mo_(" Custom Email Template"),
	"<span class='available'></span>" 	. mo_(" Custom SMS Template"),
	"<span class='available'></span>" 	. mo_(" Block Email Domains"),
	"<span class='available'></span>" 	. mo_(" Block SMS numbers"),
	"<span class='unavailable'></span>" . mo_(" Globally banned OTP"),
	"<span class='unavailable'></span>" . mo_(" Globally banned Phone Number"),
	"<span class='available'></span>" 	. mo_(" Send Custom SMS Messages"),
	"<span class='available'></span>" 	. mo_(" Country Code Dropdown for form"),
	"<span class='available'></span>" 	. mo_(" Custom OTP Length"),
	"<span class='available'></span>" 	. mo_(" Custom OTP Validity Time"),
	"<span class='available'></span>" 	. mo_(" OTP pop-up Customization"),

];

$twilio_gateway_plus_addon_features = [
	"<span class='available'></span>"	. mo_(" Premium Integration With Twilio SMS Gateway"),
	"<span class='available'></span>" 	. mo_(" 40+ registration forms supported"),
	"<span class='available'></span>" 	. mo_(" WooCommerce Forms"),
	"<span class='available'></span>" 	. mo_(" Contact Form 7 Forms"),
	"<span class='available'></span>" 	. mo_(" WooCommerce SMS Notifications"),
	"<span class='available'></span>" 	. mo_(" Ultimate Member SMS Notifications"),
	"<span class='available'></span>" 	. mo_(" Passwordless Login"),
	"<span class='available'></span>" 	. mo_(" Password Reset Over OTP"),
	"<span class='unavailable'></span>" . mo_(" Wordpress Admin Notification"),
	"<span class='unavailable'></span>" . mo_(" OTP Verification for Android/IOS App"),
	"<span class='unavailable'></span>" . mo_(" OTP Over Phone Call"),
	"<span class='available'></span>" 	. mo_(" Custom SMS/SMTP Gateway"),
	"<span class='unavailable'></span>" . mo_(" Back Up SMS Gateway"),
	"<span class='available'></span>" 	. mo_(" Custom Email Template"),
	"<span class='available'></span>" 	. mo_(" Custom SMS Template"),
	"<span class='available'></span>" 	. mo_(" Block Email Domains"),
	"<span class='available'></span>" 	. mo_(" Block SMS numbers"),
	"<span class='unavailable'></span>" . mo_(" Globally banned OTP"),
	"<span class='unavailable'></span>" . mo_(" Globally banned Phone Number"),
	"<span class='available'></span>" 	. mo_(" Send Custom SMS Messages"),
	"<span class='available'></span>" 	. mo_(" Country Code Dropdown for form"),
	"<span class='available'></span>" 	. mo_(" Custom OTP Length"),
	"<span class='available'></span>" 	. mo_(" Custom OTP Validity Time"),
	"<span class='available'></span>" 	. mo_(" OTP pop-up Customization"),
];


$enterprise_features = [
	"<span class='available'></span>"		. mo_(" Premium Integration With Twilio SMS Gateway"),
	"<span class='available'></span>" 		. mo_(" 40+ registration forms supported"),
	"<span class='available'></span>" 		. mo_(" WooCommerce Forms"),
	"<span class='available'></span>" 		. mo_(" Contact Form 7 Forms"),
	"<span class='available'></span>" 	    . mo_(" WooCommerce SMS Notifications"),
	"<span class='available'></span>" 	    . mo_(" Ultimate Member SMS Notifications"),
	"<span class='available'></span>" 	    . mo_(" Passwordless Login"),
	"<span class='available'></span>" 	    . mo_(" Password Reset Over OTP"),
	"<span class='available'></span>"       . mo_(" Wordpress Admin Notification"),
	"<span class='available'></span>" 	    . mo_(" OTP Verification for Android/IOS App"),
	"<span class='available'></span>"       . mo_(" OTP Over Phone Call"),
	"<span class='available'></span>" 		. mo_(" Custom SMS/SMTP Gateway"),
	"<span class='available'></span>" 		. mo_(" Back Up SMS Gateway"),
	"<span class='available'></span>" 		. mo_(" Custom Email Template"),
	"<span class='available'></span>" 		. mo_(" Custom SMS Template"),
	"<span class='available'></span>" 		. mo_(" Block Email Domains"),
	"<span class='available'></span>" 		. mo_(" Block SMS numbers"),
	"<span class='available'></span>" 		. mo_(" Globally banned OTP"),
	"<span class='available'></span>" 		. mo_(" Globally banned Phone Number"),
	"<span class='available'></span>" 	    . mo_(" Send Custom SMS Messages"),
	"<span class='available'></span>" 		. mo_(" Country Code Dropdown for form"),
	"<span class='available'></span>" 		. mo_(" Custom OTP Length"),
	"<span class='available'></span>" 		. mo_(" Custom OTP Validity Time"),
	"<span class='available'></span>" 		. mo_(" OTP pop-up Customization"),
];


$mo_gateway_plan_features = [
	"<span class='unavailable'></span>"		. mo_(" Premium Integration With Twilio SMS Gateway"),
	"<span class='available'></span>" 		. mo_(" 40+ registration forms supported"),
	"<span class='available'></span>" 		. mo_(" WooCommerce Forms"),
	"<span class='available'></span>" 		. mo_(" Contact Form 7 Forms"),
	"<span class='available'></span>" 		. mo_(" WooCommerce SMS Notifications"),
	"<span class='available'></span>" 		. mo_(" Ultimate Member SMS Notifications"),
	"<span class='available'></span>" 		. mo_(" Passwordless Login"),
	"<span class='available'></span>" 		. mo_(" Password Reset Over OTP"),
	"<span class='unavailable'></span>"     . mo_(" Wordpress Admin Notification"),
	"<span class='unavailable'></span>" 	. mo_(" OTP Verification for Android/IOS App"),
	"<span class='unavailable'></span>"     . mo_(" OTP Over Phone Call"),
	"<span class='unavailable'></span>"		. mo_(" Custom SMS/SMTP Gateway"),
	"<span class='unavailable'></span>"     . mo_(" Back Up SMS Gateway"),
	"<span class='available'></span>" 		. mo_(" Custom Email Template"),
	"<span class='available'></span>" 		. mo_(" Custom SMS Template"),
	"<span class='available'></span>" 		. mo_(" Block Email Domains"),
	"<span class='available'></span>" 		. mo_(" Block SMS numbers"),
	"<span class='unavailable'></span>"     . mo_(" Globally banned OTP"),
	"<span class='unavailable'></span>" 	. mo_(" Globally banned Phone Number"),
	"<span class='available'></span>" 		. mo_(" Send Custom SMS Messages"),
	"<span class='available'></span>" 		. mo_(" Country Code Dropdown for form"),
	"<span class='available'></span>" 		. mo_(" Custom OTP Length"),
	"<span class='available'></span>" 		. mo_(" Custom OTP Validity Time"),
	"<span class='available'></span>" 		. mo_(" OTP pop-up Customization"),
];

include MOV_DIR . 'views/pricing.php';