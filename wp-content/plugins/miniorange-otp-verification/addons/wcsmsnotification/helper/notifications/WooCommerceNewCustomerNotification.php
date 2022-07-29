<?php

namespace OTP\Addons\WcSMSNotification\Helper\Notifications;

use OTP\Addons\WcSMSNotification\Helper\MoWcAddOnMessages;
use OTP\Helper\MoUtility;
use OTP\Objects\SMSNotification;


class WooCommerceNewCustomerNotification extends SMSNotification
{
    public static $instance;

    function __construct()
    {
        parent::__construct();
        $this->title 			= 'New Account';
        $this->page 			= 'wc_new_customer_notif';
        $this->isEnabled 		= FALSE;
        $this->tooltipHeader 	= 'NEW_CUSTOMER_NOTIF_HEADER';
        $this->tooltipBody 		= 'NEW_CUSTOMER_NOTIF_BODY';
        $this->recipient 		= 'customer';
        $this->smsBody 			=  get_wc_option('woocommerce_registration_generate_password','') === 'yes'
                                    ? MoWcAddOnMessages::showMessage(MoWcAddOnMessages::NEW_CUSTOMER_SMS_WITH_PASS)
                                    : MoWcAddOnMessages::showMessage(MoWcAddOnMessages::NEW_CUSTOMER_SMS);
        $this->defaultSmsBody	=  get_wc_option('woocommerce_registration_generate_password','') === 'yes'
                                    ? MoWcAddOnMessages::showMessage(MoWcAddOnMessages::NEW_CUSTOMER_SMS_WITH_PASS)
                                    : MoWcAddOnMessages::showMessage(MoWcAddOnMessages::NEW_CUSTOMER_SMS);
        $this->availableTags 	= '{site-name},{username},{accountpage-url}';
        $this->pageHeader 		= mo_("NEW ACCOUNT NOTIFICATION SETTINGS");
        $this->pageDescription 	= mo_("SMS notifications settings for New Account creation SMS sent to the users");
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
        $this->setNotifInSession($this->page);
        $customer_id 	= $args['customer_id'];
        $customer_data  = $args['new_customer_data'];
        $siteName 		= get_bloginfo();
        $username 		= get_userdata($customer_id)->user_login;
        $phoneNumber 	= get_user_meta( $customer_id, 'billing_phone', TRUE );
        $postedPhoneNo  = MoUtility::sanitizeCheck('billing_phone',$_POST);
        $phoneNumber 	= MoUtility::isBlank($phoneNumber) && $postedPhoneNo ? $postedPhoneNo : $phoneNumber;
        $accountpage 	= wc_get_page_permalink( 'myaccount' );

        $replacedString = [
            'site-name'         => get_bloginfo() ,
            'username'          => $username,
            'accountpage-url'   => $accountpage
        ];
        $replacedString = apply_filters('mo_wc_new_customer_notif_string_replace',$replacedString);
        $smsBody 		= MoUtility::replaceString($replacedString, $this->smsBody);

        if(MoUtility::isBlank($phoneNumber)) return;
        MoUtility::send_phone_notif($phoneNumber, $smsBody);
    }
}