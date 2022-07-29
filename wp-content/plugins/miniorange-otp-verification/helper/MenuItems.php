<?php

namespace OTP\Helper;

if(! defined( 'ABSPATH' )) exit;

use OTP\MoOTP;
use OTP\Objects\PluginPageDetails;
use OTP\Objects\TabDetails;
use OTP\Traits\Instance;


final class MenuItems
{
    use Instance;

    
    private $_callback;

    
    private $_menuSlug;

    
    private $_menuLogo;

    
    private $_tabDetails;

    
    private function __construct()
    {
        $this->_callback  = [   MoOTP::instance(), 'mo_customer_validation_options' ];
        $this->_menuLogo  =  MOV_ICON;
        
        $tabDetails = TabDetails::instance();
        $this->_tabDetails = $tabDetails->_tabDetails;
        $this->_menuSlug = $tabDetails->_parentSlug;
        $this->addMainMenu();
        $this->addSubMenus();
    }

    private function addMainMenu()
    {
        add_menu_page (
            'OTP Verification' ,
            'OTP Verification' ,
            'manage_options',
            $this->_menuSlug ,
            $this->_callback,
            $this->_menuLogo
        );
    }

    private function addSubMenus()
    {
        
        foreach ($this->_tabDetails as $tabDetail) {
            
            add_submenu_page(
                $this->_menuSlug,
                $tabDetail->_pageTitle,
                $tabDetail->_menuTitle,
                'manage_options',
                $tabDetail->_menuSlug,
                $this->_callback
            );
        }
    }
}