<?php


namespace OTP\Addons\PasswordReset;

use OTP\Addons\PasswordReset\Handler\UMPasswordResetAddOnHandler;
use OTP\Addons\PasswordReset\Helper\UMPasswordResetMessages;
use OTP\Helper\AddOnList;
use OTP\Objects\AddOnInterface;
use OTP\Objects\BaseAddOn;
use OTP\Traits\Instance;

if(! defined( 'ABSPATH' )) exit;
include '_autoload.php';

final class UltimateMemberPasswordReset extends BaseAddOn implements AddOnInterface
{
    use Instance;

    public function __construct()
	{
	    parent::__construct();
		add_action( 'admin_enqueue_scripts'					    , array( $this, 'um_pr_notif_settings_style'   ) );
        add_action( 'mo_otp_verification_delete_addon_options'	, array( $this, 'um_pr_notif_delete_options' 	) );
	}

	
	function um_pr_notif_settings_style()
	{
		wp_enqueue_style( 'um_pr_notif_admin_settings_style', UMPR_CSS_URL);
	}

    
    function initializeHandlers()
    {
        
        $list = AddOnList::instance();
        
        $handler = UMPasswordResetAddOnHandler::instance();
        $list->add($handler->getAddOnKey(),$handler);
    }

    
    function initializeHelpers()
    {
        UMPasswordResetMessages::instance();
    }


    
    function show_addon_settings_page()
    {
        include UMPR_DIR . 'controllers/main-controller.php';
    }

	
	function um_pr_notif_delete_options()
    {
        delete_site_option('mo_um_pr_pass_enable');
        delete_site_option('mo_um_pr_pass_button_text');
        delete_site_option('mo_um_pr_enabled_type');
    }
}