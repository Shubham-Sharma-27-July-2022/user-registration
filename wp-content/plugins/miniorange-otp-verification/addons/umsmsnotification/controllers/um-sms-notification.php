<?php

use OTP\Addons\UmSMSNotification\Helper\UltimateMemberNotificationsList;
use OTP\Addons\UmSMSNotification\Helper\UltimateMemberSMSNotificationMessages;
use OTP\Helper\MoUtility;

$notification_settings 	= maybe_unserialize(get_umsn_option('notification_settings'));
$notification_settings 	= $notification_settings ? $notification_settings : UltimateMemberNotificationsList::instance();
$sms 					= '';

if(isset( $_GET[ 'sms' ]))
{
	$sms = sanitize_text_field($_GET['sms']);
	$smsnotification = $controller . '/smsnotifications/';
	switch($_GET['sms'])
	{
		case 'um_new_customer_notif':
			include $smsnotification . 'um-new-customer-notif.php';				    break;
		case 'um_new_customer_admin_notif':
			include $smsnotification . 'um-new-customer-admin-notif.php';			break;
	}
}
else
{
	include UMSN_DIR . '/views/um-sms-notification.php';
}



function show_notifications_table( UltimateMemberNotificationsList $notifications)
{
	foreach ($notifications as $notification => $property)
	{
		$url = add_query_arg( array('sms' => $property->page), $_SERVER['REQUEST_URI'] );

		echo '	<tr>
                    <td class="umsn-table-list-status">
                        <span class="'.($property->isEnabled ? "status-enabled" : "" ).'"></span>
                    </td>
                    <td class="umsn-table-list-name">
                        <a href="'.$url.'">'.$property->title.'</a>';

                        mo_draw_tooltip(
                            UltimateMemberSMSNotificationMessages::showMessage($property->tooltipHeader),
                            UltimateMemberSMSNotificationMessages::showMessage($property->tooltipBody)
                        );

		echo'		</td>
                    <td class="umsn-table-list-recipient" style="word-wrap: break-word;">
                        '.$property->notificationType.'
                    </td>
                    <td class="umsn-table-list-status-actions">
                        <a class="button alignright tips" href="'.$url.'">Configure</a>
                    </td>
                </tr>';
	}
}
