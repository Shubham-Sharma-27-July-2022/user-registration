<?php



namespace OTP\Addons\WcSMSNotification;

use OTP\Addons\WcSMSNotification\Handler\WooCommerceNotifications;
use OTP\Addons\WcSMSNotification\Helper\MoWcAddOnMessages;
use OTP\Addons\WcSMSNotification\Helper\WooCommerceNotificationsList;
use OTP\Helper\AddOnList;
use OTP\Objects\AddOnInterface;
use OTP\Objects\BaseAddOn;
use OTP\Traits\Instance;

if(! defined( 'ABSPATH' )) exit;
include '_autoload.php';

final class WooCommerceSmsNotification extends BaseAddon implements AddOnInterface
{
    use Instance;

    public function __construct()
	{
	    parent::__construct();
		add_action( 'admin_enqueue_scripts'					    , array( $this, 'mo_sms_notif_settings_style'   ) );
		add_action( 'admin_enqueue_scripts'					    , array( $this, 'mo_sms_notif_settings_script' 	) );
        add_action( 'mo_otp_verification_delete_addon_options'	, array( $this, 'mo_sms_notif_delete_options' 	) );
	}

	
	function mo_sms_notif_settings_style()
	{
		wp_enqueue_style( 'mo_sms_notif_admin_settings_style', MSN_CSS_URL);
	}


	
	function mo_sms_notif_settings_script()
	{
		wp_register_script( 'mo_sms_notif_admin_settings_script', MSN_JS_URL , array('jquery') );
		wp_localize_script( 'mo_sms_notif_admin_settings_script', 'mocustommsg', array(
			'siteURL' 		=> 	admin_url(),
		));
		wp_enqueue_script('mo_sms_notif_admin_settings_script');
	}

    
    function initializeHandlers()
    {
        
        $list = AddOnList::instance();
        
        $handler = WooCommerceNotifications::instance();
        $list->add($handler->getAddOnKey(),$handler);
    }

    
    function initializeHelpers()
    {
        MoWcAddOnMessages::instance();
        WooCommerceNotificationsList::instance();
    }


    
    function show_addon_settings_page()
    {
        include MSN_DIR . '/controllers/main-controller.php';
    }


    
	function mo_sms_notif_delete_options()
    {
        delete_site_option('mo_wc_sms_notification_settings');
    }
}