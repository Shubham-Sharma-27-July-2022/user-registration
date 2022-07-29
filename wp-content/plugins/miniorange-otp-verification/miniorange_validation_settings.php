<?php

/**
 * Plugin Name: Email Verification / SMS verification / Mobile Verification
 * Plugin URI: http://miniorange.com
 * Description: Email & SMS OTP Verification for all forms. WooCommerce SMS Notification. PasswordLess Login. External Gateway for OTP Verification. 24/7 support.
 * Version: 3.8.3
 * Author: miniOrange
 * Author URI: http://miniorange.com
 * Text Domain: miniorange-otp-verification
 * Domain Path: /lang
 * WC requires at least: 2.0.0
 * WC tested up to: 5.6.0
 * License: GPL2
 */

use OTP\MoOTP;

if(! defined( 'ABSPATH' )) exit;
define('MOV_PLUGIN_NAME', plugin_basename(__FILE__));
$dirName = substr(MOV_PLUGIN_NAME,0,strpos(MOV_PLUGIN_NAME,"/"));
define("MOV_NAME",$dirName);
include '_autoload.php';
MoOTP::instance(); //initialize the main class
