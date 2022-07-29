<?php

use OTP\Addons\UmSMSNotification\Handler\UltimateMemberSMSNotificationsHandler;


$handler        = UltimateMemberSMSNotificationsHandler::instance();
$registerd 		= $handler->moAddOnV();
$disabled  	 	= !$registerd ? "disabled" : "";
$current_user 	= wp_get_current_user();
$controller 	= UMSN_DIR . 'controllers/';
$addon          = add_query_arg( array('page' => 'addon'), remove_query_arg('addon',$_SERVER['REQUEST_URI']));

if(isset( $_GET[ 'addon' ]))
{
	switch($_GET['addon'])
	{
		case 'um_notif':
			include $controller . 'um-sms-notification.php'; break;
	}
}