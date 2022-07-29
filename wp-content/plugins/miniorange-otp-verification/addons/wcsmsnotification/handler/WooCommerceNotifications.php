<?php

namespace OTP\Addons\WcSMSNotification\Handler;

use OTP\Addons\WcSMSNotification\Helper\MoWcAddOnMessages;
use OTP\Addons\WcSMSNotification\Helper\MoWcAddOnUtility;
use OTP\Addons\WcSMSNotification\Helper\WcOrderStatus;
use OTP\Addons\WcSMSNotification\Helper\WooCommerceNotificationsList;
use OTP\Helper\MoConstants;
use OTP\Helper\MoUtility;
use OTP\Helper\MoOTPDocs;
use OTP\Objects\BaseAddOnHandler;
use OTP\Objects\SMSNotification;
use OTP\Traits\Instance;
use \WC_Emails;
use \WC_Order;


class WooCommerceNotifications extends BaseAddOnHandler
{
    use Instance;

    
    private $notificationSettings;

    
    function __construct()
    {
        parent::__construct();
        if(!$this->moAddOnV()) return;
        $this->notificationSettings = get_wc_option('notification_settings')
            ? get_wc_option('notification_settings') : WooCommerceNotificationsList::instance();

        add_action( 'woocommerce_created_customer_notification', array( $this, 'mo_send_new_customer_sms_notif' ), 1, 3 );
        add_action( 'woocommerce_new_customer_note_notification', array( $this, 'mo_send_new_customer_sms_note'), 1, 1 );
        add_action( 'woocommerce_order_status_changed', array( $this, 'mo_send_admin_order_sms_notif' ), 1, 3 );
        add_action( 'woocommerce_order_status_changed', array( $this, 'mo_customer_order_hold_sms_notif' ), 1, 3 );
        add_action( 'add_meta_boxes', array( $this,'add_custom_msg_meta_box' ), 1);
                add_action( 'admin_init',  array( $this, '_handle_admin_actions' ) );
    }


    
    function _handle_admin_actions()
    {
        if(!current_user_can('manage_options')) return;
        if(array_key_exists('option',$_GET) && sanitize_text_field($_GET['option'])=="mo_send_order_custom_msg")
            $this->_send_custom_order_msg($_POST);
    }


    
    function mo_send_new_customer_sms_notif($customer_id, $new_customer_data = array(), $password_generated = false)
    {
        $this->notificationSettings->getWcNewCustomerNotif()->sendSMS(
            array( 'customer_id'=>$customer_id, 'new_customer_data' => $new_customer_data, 'password_generated' => $password_generated )
        );
    }


    
    function mo_send_new_customer_sms_note($args)
    {
        $this->notificationSettings->getWcCustomerNoteNotif()->sendSMS(
            array('orderDetails' => wc_get_order($args['order_id']))
        );
    }


    
    function mo_send_admin_order_sms_notif($order_id, $old_status, $new_status)
    {
        $order = new WC_Order( $order_id );
        if(!is_a($order,'WC_Order')) return;
        $this->notificationSettings->getWcAdminOrderStatusNotif()->sendSMS(
            array('orderDetails' =>$order, 'new_status'=>$new_status, 'old_status'=> $old_status)
        );
    }


    
    function mo_customer_order_hold_sms_notif($order_id, $old_status, $new_status)
    {
        $order = new WC_Order( $order_id );
        if(!is_a($order,'WC_Order')) return;
        
        if(strcasecmp($new_status,WcOrderStatus::ON_HOLD)==0)
            $notification = $this->notificationSettings->getWcOrderOnHoldNotif();
        elseif(strcasecmp($new_status,WcOrderStatus::PROCESSING)==0)
            $notification = $this->notificationSettings->getWcOrderProcessingNotif();
        elseif(strcasecmp($new_status,WcOrderStatus::COMPLETED)==0)
            $notification = $this->notificationSettings->getWcOrderCompletedNotif();
        elseif(strcasecmp($new_status,WcOrderStatus::REFUNDED)==0)
            $notification = $this->notificationSettings->getWcOrderRefundedNotif();
        elseif(strcasecmp($new_status,WcOrderStatus::CANCELLED)==0)
            $notification = $this->notificationSettings->getWcOrderCancelledNotif();
        elseif(strcasecmp($new_status,WcOrderStatus::FAILED)==0)
            $notification = $this->notificationSettings->getWcOrderFailedNotif();
        elseif(strcasecmp($new_status,WcOrderStatus::PENDING)==0)
            $notification = $this->notificationSettings->getWcOrderPendingNotif();
        else
           return;
        $notification->sendSMS( array('orderDetails' =>$order) );
    }


    
    function unhook($email_class)
    {
        $newOrderEmail = array( $email_class->emails['WC_Email_New_Order'], 'trigger' );
        $processingOrder = array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' );
        $completedOrder = array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' );
        $newCustomer = array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' );

        remove_action('woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
        remove_action('woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
        remove_action('woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );
        remove_action('woocommerce_order_status_pending_to_processing_notification', $newOrderEmail);
        remove_action('woocommerce_order_status_pending_to_completed_notification', $newOrderEmail);
        remove_action('woocommerce_order_status_pending_to_on-hold_notification', $newOrderEmail);
        remove_action('woocommerce_order_status_failed_to_processing_notification', $newOrderEmail);
        remove_action('woocommerce_order_status_failed_to_completed_notification', $newOrderEmail);
        remove_action('woocommerce_order_status_failed_to_on-hold_notification', $newOrderEmail);
        remove_action('woocommerce_order_status_pending_to_processing_notification', $processingOrder );
        remove_action('woocommerce_order_status_pending_to_on-hold_notification', $processingOrder );
        remove_action( 'woocommerce_order_status_completed_notification', $completedOrder);
        remove_action( 'woocommerce_new_customer_note_notification',  $newCustomer);
    }


    
    function add_custom_msg_meta_box()
    {
        add_meta_box(
            'mo_wc_custom_sms_meta_box',
            'Custom SMS',
            array($this,'mo_show_send_custom_msg_box'),
            'shop_order',
            'side',
            'default'
        );
    }


    
    function mo_show_send_custom_msg_box($data)
    {
        $orderDetails = new WC_Order($data->ID);
        $phone_numbers = MoWcAddOnUtility::getCustomerNumberFromOrder($orderDetails);
        include MSN_DIR . 'views/custom-order-msg.php';
    }


    
    function _send_custom_order_msg($POST)
    {
        if(!array_key_exists('numbers',$POST) || MoUtility::isBlank(sanitize_text_field($POST['numbers'])))
        {
            MoUtility::createJson(
                MoWcAddOnMessages::showMessage(MoWcAddOnMessages::INVALID_PHONE), MoConstants::ERROR_JSON_TYPE
            );
        }
        else
        {
            foreach (explode(";",$POST['numbers']) as $number) {
                if(MoUtility::send_phone_notif($number,$POST['msg'])) {
                    wp_send_json(MoUtility::createJson(
                        MoWcAddOnMessages::showMessage(MoWcAddOnMessages::SMS_SENT_SUCCESS), MoConstants::SUCCESS_JSON_TYPE
                    ));
                } else {
                    wp_send_json(MoUtility::createJson(
                        MoWcAddOnMessages::showMessage(MoWcAddOnMessages::ERROR_SENDING_SMS), MoConstants::ERROR_JSON_TYPE
                    ));
                }
            }
        }
    }


    
    function setAddonKey()
    {
        $this->_addOnKey = 'wc_sms_notification_addon';
    }

    
    function setAddOnDesc()
    {
        $this->_addOnDesc = mo_("Allows your site to send order and WooCommerce notifications to buyers, "
            ."sellers and admins. Click on the settings button to the right to see the list of notifications "
            ."that go out.");
    }

    
    function setAddOnName()
    {
        $this->_addOnName = mo_("WooCommerce SMS Notification");
    }

    
    function setAddOnDocs()
    {
    $this->_addOnDocs = MoOTPDocs::WOCOMMERCE_SMS_NOTIFICATION_LINK['guideLink'];
    }

    
    function setAddOnVideo()
    {
    $this->_addOnVideo = MoOTPDocs::WOCOMMERCE_SMS_NOTIFICATION_LINK['videoLink'];
    }
    
    function setSettingsUrl()
    {
        $this->_settingsUrl = add_query_arg( array('addon'=> 'woocommerce_notif'), $_SERVER['REQUEST_URI'] );
    }

}