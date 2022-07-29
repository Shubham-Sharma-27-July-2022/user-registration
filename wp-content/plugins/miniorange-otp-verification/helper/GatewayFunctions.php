<?php

namespace OTP\Helper;

if(! defined( 'ABSPATH' )) exit;

use OTP\Objects\IGatewayFunctions;
use OTP\Objects\NotificationSettings;
use OTP\Traits\Instance;

class GatewayFunctions implements IGatewayFunctions
{
    use Instance;

    
    private $gateway;

    
    private $pluginTypeToClass = [
        "MiniOrangeGateway"             => "OTP\Helper\MiniOrangeGateway",
        "CustomGatewayWithAddons"       => "OTP\Helper\CustomGatewayWithAddons",
        "CustomGatewayWithoutAddons"    => "OTP\Helper\CustomGatewayWithoutAddons",
        "TwilioGatewayWithAddons"       => "OTP\Helper\TwilioGatewayWithAddons",
        "EnterpriseGatewayWithAddons"   => "OTP\Helper\EnterpriseGatewayWithAddons",
    ];

    public function __construct()
    {
        
        $pluginType = $this->pluginTypeToClass[MOV_TYPE];
        $this->gateway = $pluginType::instance();
    }

    
    public function isMG()
    {
        return $this->gateway->isMG();
    }

    
    public function loadAddons($folder)
    {
        $this->gateway->loadAddons($folder);
    }

    
    function registerAddOns()
    {
        $this->gateway->registerAddOns();
    }

    
    public function showAddOnList()
    {
        $this->gateway->showAddOnList();
    }

    
    function hourlySync()
    {
        $this->gateway->hourlySync();
    }

    
    public function custom_wp_mail_from_name($original_email_from)
    {
        return $this->gateway->custom_wp_mail_from_name($original_email_from);
    }

    
    public function flush_cache()
    {
        $this->gateway->flush_cache();
    }

    
    public function _vlk($post)
    {
        $this->gateway->_vlk($post);
    }

    
    public function _mo_configure_sms_template($posted)
    {
        $this->gateway->_mo_configure_sms_template($posted);
    }

    
    public function _mo_configure_email_template($posted)
    {
        $this->gateway->_mo_configure_email_template($posted);
    }

    
    public function mo_send_otp_token($authType, $email, $phone)
    {
        return $this->gateway->mo_send_otp_token($authType,$email,$phone);
    }

    
    public function mclv()
    {
        return $this->gateway->mclv();
    }


    
     public function isGatewayConfig()
    {
        return $this->gateway->isGatewayConfig();
    }
    
    
    
    public function showConfigurationPage($disabled)
    {
        $this->gateway->showConfigurationPage($disabled);
    }

    
    public function mo_validate_otp_token($txId, $otp_token)
    {
        return $this->gateway->mo_validate_otp_token($txId,$otp_token);
    }

    
    public function mo_send_notif(NotificationSettings $settings)
    {
        return $this->gateway->mo_send_notif($settings);
    }

    
    public function getApplicationName()
    {
        return $this->gateway->getApplicationName();
    }

    
    public function getConfigPagePointers()
    {
        return $this->gateway->getConfigPagePointers();
    }
}