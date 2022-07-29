<?php

namespace OTP\Helper;

use OTP\Objects\BaseAddOnHandler;
use OTP\Objects\FormHandler;
use OTP\Traits\Instance;

if(! defined( 'ABSPATH' )) exit;


final class PremiumAddonList
{
    use Instance;
    
    private $_premiumaddon;

    
    private function __construct() { $this->_premiumaddon = array(
        "otp_control"=>["name"=>"Limit OTP Request ","description" => "Allows you to block OTP from being sent out before the set timer is up. Click on the button below for further details."],
        "wp_sms_notification_addon"=>["name"=>"WordPress SMS Notification to Admin & User on Registration","description" => "Allows your site to send out custom SMS notifications to Customers and Administrators when a new user registers on your Wordpress site. Click on the button below for further details."],
        "wc_pass_reset_addon"=>["name"=>"WooCommerce Password Reset Over OTP ","description" => "Allows your users to reset their password using OTP instead of email links. Click on the button below for further details."],
        "wp_pass_reset_addon"=>["name"=>"WordPress Password Reset Over OTP","description" => "Allows your users to reset their password using OTP instead of email links. Click on the button below for further details."],
        "mo_country_code_dropdown"=>["name"=>"Country Code Dropdown ","description" => "Allows you to enable the country code dropdown on any field of your choice.Includes the country code and the country flag for selection."]
    ); }



    public function getAddOnName(){ return $this->_addOnName; }
 
    public function getPremiumAddOnList() { return $this->_premiumaddon; }

}