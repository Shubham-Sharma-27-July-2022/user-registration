<?php

if(! defined( 'ABSPATH' )) exit;

define('UMSN_DIR', plugin_dir_path(__FILE__));
define('UMSN_URL', plugin_dir_url(__FILE__));
define('UMSN_VERSION', '1.0.0');
define('UMSN_CSS_URL', UMSN_URL . 'includes/css/settings.min.css?version='.UMSN_VERSION);





function get_umsn_option($string, $prefix=null)
{
    $string = ($prefix==null ? "mo_um_sms_" : $prefix) . $string;
    return get_mo_option($string,'');
}


function update_umsn_option($optionName,$value,$prefix=null)
{
    $optionName = ($prefix===null ? "mo_um_sms_" : $prefix) . $optionName;
    update_mo_option($optionName,$value,'');
}
