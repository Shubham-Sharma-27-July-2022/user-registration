<?php

namespace OTP\Addons\WcSMSNotification\Helper\Notifications;

use OTP\Addons\WcSMSNotification\Helper\MoWcAddOnMessages;
use OTP\Addons\WcSMSNotification\Helper\MoWcAddOnUtility;
use OTP\Helper\MoUtility;
use OTP\Objects\SMSNotification;


class WooCommerceOrderProcessingNotification extends SMSNotification
{
    public static $instance;

    function __construct()
    {
        parent::__construct();
        $this->title 			= 'Processing Order';
        $this->page 			= 'wc_order_processing_notif';
        $this->isEnabled 		= FALSE;
        $this->tooltipHeader 	= 'ORDER_PROCESSING_NOTIF_HEADER';
        $this->tooltipBody 		= 'ORDER_PROCESSING_NOTIF_BODY';
        $this->recipient 		= "customer";
        $this->smsBody 			= MoWcAddOnMessages::showMessage(MoWcAddOnMessages::PROCESSING_ORDER_SMS);
        $this->defaultSmsBody 	= MoWcAddOnMessages::showMessage(MoWcAddOnMessages::PROCESSING_ORDER_SMS);
        $this->availableTags 	= '{site-name},{order-number},{username}{order-date}';
        $this->pageHeader 		= mo_("ORDER PROCESSING NOTIFICATION SETTINGS");
        $this->pageDescription 	= mo_("SMS notifications settings for Order Processing SMS sent to the users");
        $this->notificationType = mo_("Customer");
        self::$instance 		= $this;
    }


    
    public static function getInstance()
    {
        return self::$instance === null ? new self() : self::$instance;
    }

    
    function sendSMS(array $args)
    {
        if(!$this->isEnabled) return;
        $orderDetails 	= $args['orderDetails'];
        if(MoUtility::isBlank($orderDetails)) return;
        $this->setNotifInSession($this->page);
        $userdetails 	= get_userdata($orderDetails->get_customer_id());
        $siteName 		= get_bloginfo();
        $username 		= MoUtility::isBlank($userdetails) ? "" : $userdetails->user_login;
        $phoneNumber 	= MoWcAddOnUtility::getCustomerNumberFromOrder($orderDetails);
        $dateCreated 	= $orderDetails->get_date_created()->date_i18n();
        $orderNo 		= $orderDetails->get_order_number();

        $replacedString = [
            'site-name'     =>  $siteName,
            'username'      =>  $username,
            'order-date'    =>  $dateCreated,
            'order-number'  =>  $orderNo
        ];
        $replacedString = apply_filters('mo_wc_customer_order_processing_notif_string_replace',$replacedString);
        $smsBody 		= MoUtility::replaceString($replacedString, $this->smsBody);

        if(MoUtility::isBlank($phoneNumber)) return;
        MoUtility::send_phone_notif($phoneNumber, $smsBody);
    }
}