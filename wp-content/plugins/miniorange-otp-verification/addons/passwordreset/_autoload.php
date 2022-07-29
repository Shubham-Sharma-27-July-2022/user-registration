<?php

if(! defined( 'ABSPATH' )) exit;

define('UMPR_DIR', plugin_dir_path(__FILE__));
define('UMPR_URL', plugin_dir_url(__FILE__));
define('UMPR_VERSION', '1.0.0');
define('UMPR_CSS_URL', UMPR_URL . 'includes/css/settings.min.css?version='.UMPR_VERSION);





function get_umpr_option($string,$prefix=null)
{
    $string = ($prefix==null ? "mo_um_pr_" : $prefix) . $string;
    return get_mo_option($string,'');
}



function update_umpr_option($optionName,$value,$prefix=null)
{
    $optionName = ($prefix===null ? "mo_um_pr_" : $prefix) . $optionName;
    update_mo_option($optionName,$value,'');
}

