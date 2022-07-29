<?php

use OTP\Addons\WcSMSNotification\Helper\MoWcAddOnMessages;
use OTP\Addons\WcSMSNotification\Helper\WooCommerceNotificationsList;
use OTP\Helper\MoUtility;

$notification_settings 	= get_wc_option('notification_settings');
$notification_settings 	= $notification_settings ? maybe_unserialize($notification_settings)
                                                : WooCommerceNotificationsList::instance();
$sms 					= '';

if(isset( $_GET[ 'sms' ]))
{
    $sms = sanitize_text_field($_GET['sms']);
    $smsnotification = $controller . '/smsnotifications/';
    switch($_GET['sms'])
    {
        case 'wc_new_customer_notif':
            include $smsnotification . 'wc-new-customer-notif.php';				    break;
        case 'wc_customer_note_notif':
            include $smsnotification . 'wc-customer-note-notif.php';				break;
        case 'wc_order_cancelled_notif':
            include $smsnotification . 'wc-order-cancelled-customer-notif.php';	    break;
        case 'wc_order_completed_notif':
            include $smsnotification . 'wc-order-completed-customer-notif.php';	    break;
        case 'wc_order_failed_notif':
            include $smsnotification . 'wc-order-failed-customer-notif.php';		break;
        case 'wc_order_on_hold_notif':
            include $smsnotification . 'wc-order-onhold-customer-notif.php';		break;
        case 'wc_order_processing_notif':
            include $smsnotification . 'wc-order-processing-customer-notif.php';	break;
        case 'wc_order_refunded_notif':
            include $smsnotification . 'wc-order-refunded-customer-notif.php';		break;
        case 'wc_admin_order_status_notif':
            include $smsnotification . 'wc-order-status-admin-notif.php';			break;
        case 'wc_order_pending_notif':
            include $smsnotification . 'wc-order-pending-customer-notif.php';		break;
    }
}
else
{
    include MSN_DIR . '/views/wc-sms-notification.php';
}



function show_notifications_table( WooCommerceNotificationsList $notifications)
{
    foreach ($notifications as $notification => $property)
    {
        $url = add_query_arg( array('sms' => $property->page), $_SERVER['REQUEST_URI'] );

        echo '	<tr>
                    <td class="msn-table-list-status">
                        <span class="'.($property->isEnabled ? "status-enabled" : "" ).'"></span>
                    </td>
                    <td class="msn-table-list-name">
                        <a href="'.$url.'">'.$property->title.'</a>';

                        mo_draw_tooltip(
                            MoWcAddOnMessages::showMessage($property->tooltipHeader),
                            MoWcAddOnMessages::showMessage($property->tooltipBody)
                        );

        echo'		</td>
                    <td class="msn-table-list-recipient" style="word-wrap: break-word;">
                        '.$property->notificationType.'
                    </td>
                    <td class="msn-table-list-status-actions">
                        <a class="button alignright tips" href="'.$url.'">Configure</a>
                    </td>
                </tr>';
    }
}
