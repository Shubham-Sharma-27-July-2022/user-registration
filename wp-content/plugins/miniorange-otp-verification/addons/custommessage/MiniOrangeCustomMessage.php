<?php



namespace OTP\Addons\CustomMessage;

use OTP\Addons\CustomMessage\Handler\CustomMessages;
use OTP\Addons\CustomMessage\Handler\CustomMessagesShortcode;
use OTP\Helper\AddOnList;
use OTP\Objects\AddOnInterface;
use OTP\Objects\BaseAddOn;
use OTP\Traits\Instance;

if(! defined( 'ABSPATH' )) exit;
include '_autoload.php';

class MiniOrangeCustomMessage extends BaseAddOn implements AddOnInterface
{
    use Instance;

    
    function initializeHandlers()
    {
        
        $list = AddOnList::instance();
        
        $handler = CustomMessages::instance();
        $list->add($handler->getAddOnKey(),$handler);
    }

    
    function initializeHelpers()
    {
        CustomMessagesShortcode::instance();
    }

    
    function show_addon_settings_page()
    {
        include MCM_DIR . 'controllers/main-controller.php';
    }
}