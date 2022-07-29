<?php

namespace OTP\Addons\WcSMSNotification\Helper\Notifications;

use OTP\Addons\WcSMSNotification\Helper\MoWcAddOnMessages;
use OTP\Addons\WcSMSNotification\Helper\MoWcAddOnUtility;
use OTP\Addons\WcSMSNotification\Helper\WcOrderStatus;
use OTP\Helper\MoUtility;
use OTP\Objects\SMSNotification;


class WooCommerceAdminOrderstatusNotification extends SMSNotification
{
    public static $instance;
    public static $statuses;

    function __construct()
    {
        parent::__construct();
        $this->title 			= 'Order Status';
        $this->page 			= 'wc_admin_order_status_notif';
        $this->isEnabled 		= FALSE;
        $this->tooltipHeader 	= 'NEW_ORDER_NOTIF_HEADER';
        $this->tooltipBody 		= 'NEW_ORDER_NOTIF_BODY';
        $this->recipient 		= MoWcAddOnUtility::getAdminPhoneNumber();
        $this->smsBody 			= MoWcAddOnMessages::showMessage(MoWcAddOnMessages::ADMIN_STATUS_SMS);
        $this->defaultSmsBody 	= MoWcAddOnMessages::showMessage(MoWcAddOnMessages::ADMIN_STATUS_SMS);
        $this->availableTags 	= '{site-name},{order-number},{order-status},{username}{order-date}';
        $this->pageHeader 		= mo_("ORDER ADMIN STATUS NOTIFICATION SETTINGS");
        $this->pageDescription 	= mo_("SMS notifications settings for Order Status SMS sent to the admins");
        $this->notificationType = mo_("Administrator");
        self::$instance 		= $this;
        self::$statuses 		= WcOrderStatus::getAllStatus();
    }


    
    public static function getInstance()
    {
        return self::$instance === null ? new self() : self::$instance;
    }


    
    function sendSMS(array $args)
    {
        if(!$this->isEnabled) return;
        $orderDetails 	= $args['orderDetails'];
        $new_status	 	= $args['new_status'];
        if(MoUtility::isBlank($orderDetails)) return;
        if(!in_array($new_status,self::$statuses)) return;
        $this->setNotifInSession($this->page);
        $userdetails 	= get_userdata($orderDetails->get_customer_id());
        $siteName 		= get_bloginfo();
        $username 		= MoUtility::isBlank($userdetails) ? "" : $userdetails->user_login;
        $phoneNumbers 	= maybe_unserialize($this->recipient);
        $dateCreated 	= $orderDetails->get_date_created()->date_i18n();
        $orderNo 		= $orderDetails->get_order_number();

        $replacedString = [
            'site-name'     =>  $siteName,
            'username'      =>  $username,
            'order-date'    =>  $dateCreated,
            'order-number'  =>  $orderNo,
            'order-status'  =>  $new_status
        ];
        $replacedString = apply_filters('mo_wc_admin_order_notif_string_replace',$replacedString);
        $smsBody 		= MoUtility::replaceString($replacedString, $this->smsBody);

        if(MoUtility::isBlank($phoneNumbers)) return;
        foreach ($phoneNumbers as $phoneNumber) {
            MoUtility::send_phone_notif($phoneNumber, $smsBody);
        }
    }
}