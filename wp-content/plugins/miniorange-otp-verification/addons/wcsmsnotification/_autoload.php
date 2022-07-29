<?php

if(! defined( 'ABSPATH' )) exit;

define('MSN_DIR', plugin_dir_path(__FILE__));
define('MSN_URL', plugin_dir_url(__FILE__));
define('MSN_VERSION', '1.0.0');
define('MSN_CSS_URL', MSN_URL . 'includes/css/settings.min.css?version='.MSN_VERSION);
define('MSN_JS_URL', MSN_URL . 'includes/js/settings.min.js?version='.MSN_VERSION);






function get_wc_option($string,$prefix=null)
{
    $string = ($prefix===null ? "mo_wc_sms_" : $prefix) . $string;
    return get_mo_option($string,'');
}



function update_wc_option($optionName,$value,$prefix=null)
{
    $optionName = ($prefix===null ? "mo_wc_sms_" : $prefix) . $optionName;
    update_mo_option($optionName,$value,'');
}