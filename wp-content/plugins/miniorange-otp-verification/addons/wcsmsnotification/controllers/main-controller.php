<?php

use OTP\Addons\WcSMSNotification\Handler\WooCommerceNotifications;

$registerd 		= WooCommerceNotifications::instance()->moAddOnV();
	$disabled  	 	= !$registerd ? "disabled" : "";
	$current_user 	= wp_get_current_user();
	$controller 	= MSN_DIR . 'controllers/';
    $addon          = add_query_arg( array('page' => 'addon'), remove_query_arg('addon',$_SERVER['REQUEST_URI']));

	if(isset( $_GET[ 'addon' ]))
	{
		switch($_GET['addon'])
		{
			case 'woocommerce_notif':
				include $controller . 'wc-sms-notification.php'; break;
		}
	}