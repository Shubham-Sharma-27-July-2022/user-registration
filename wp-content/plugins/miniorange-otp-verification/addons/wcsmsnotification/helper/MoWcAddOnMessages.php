<?php

namespace OTP\Addons\WcSMSNotification\Helper;

use OTP\Helper\MoUtility;
use OTP\Objects\BaseMessages;
use OTP\Traits\Instance;


final class MoWcAddOnMessages extends BaseMessages
{
    use Instance;

	private function __construct()
	{
        
		define("MO_WC_ADDON_MESSAGES", serialize( array(
						self::NEW_CUSTOMER_NOTIF_HEADER         =>	mo_( 'NEW ACCOUNT NOTIFICATION'),
			self::NEW_CUSTOMER_NOTIF_BODY           => 	mo_( 'Customers are sent a new account SMS notification when '.
			                                                'they sign up via checkout or account page.'),
			self::NEW_CUSTOMER_SMS_WITH_PASS	    => 	mo_("Thanks for creating an account on {site-name}. Your ".
			                                                "username is {username}. Login Here: {accountpage-url}"),
			self::NEW_CUSTOMER_SMS			        => 	mo_( "Thanks for creating an account on {site-name}. Your ".
			                                                "username is {username}. Login Here: {accountpage-url}"),

						self::CUSTOMER_NOTE_NOTIF_HEADER	    =>	mo_( 'CUSTOMER NOTE NOTIFICATION'),
			self::CUSTOMER_NOTE_NOTIF_BODY 	        => 	mo_( "Customers are sent a new note SMS notification when ".
			                                                "the admin adds a customer note to one of their orders."),
			self::CUSTOMER_NOTE_SMS			        => 	mo_( "Hi {username}, A note has been added to your order number {order-number} with {site-name} ordered on {order-date}"),


						self::NEW_ORDER_NOTIF_HEADER		    =>	mo_( 'ORDER STATUS NOTIFICATION'),
			self::NEW_ORDER_NOTIF_BODY		        =>  mo_( "Recipients will be sent a new sms notification ".
			                                                "notifying that the status of a order has changed ".
			                                                "and they need to process it."),
			self::ADMIN_STATUS_SMS 			        =>  mo_( "{site-name}: Customer Order number {order-number} status changed to {order-status}. Check your dashboard for complete details."),


						self::ORDER_ON_HOLD_NOTIF_HEADER	    =>	mo_( 'ORDER ON HOLD NOTIFICATION'),
			self::ORDER_ON_HOLD_NOTIF_BODY	        =>	mo_( "Customer will be sent a new sms notification notifying".
			                                                " that the status of the order has changed to ON-HOLD."),
			self::ORDER_ON_HOLD_SMS			        =>	mo_( "Hello {username}, Your order number {order-number} with {site-name} ordered on {order-date} has been put on hold."),


						self::ORDER_PROCESSING_NOTIF_HEADER     => mo_(  'PROCESSING ORDER NOTIFICATION'),
			self::ORDER_PROCESSING_NOTIF_BODY       => mo_(  "Customer will be sent a new sms notification notifying ".
			                                                "that the order is currently under processing."),
			self::PROCESSING_ORDER_SMS 		        => mo_(  "Hello {username}, Your order number {order-number} with {site-name} ordered on {order-date} is processing."),

						self::ORDER_COMPLETED_NOTIF_HEADER      => mo_(  'ORDER COMPLETED NOTIFICATION'),
			self::ORDER_COMPLETED_NOTIF_BODY 	    => mo_(  "Customer will be sent a new sms notification ".
			                                                "notifying that the order processing has been completed."),
			self::ORDER_COMPLETED_SMS 			    => mo_(  "Hello {username}, Your order number {order-number} with {site-name} ordered on {order-date} has been processed. It will be delivered shortly."),

						self::ORDER_REFUNDED_NOTIF_HEADER 	    => mo_(  'ORDER REFUNDED NOTIFICATION'),
			self::ORDER_REUNDED_NOTIF_BODY 		    => mo_(  "Customer will be sent a new sms notification notifying ".
			                                                "that the ordered has been refunded."),
			self::ORDER_REFUNDED_SMS 			    => mo_(  "Hello {username}, Your order number {order-number} with {site-name} ordered on {order-date} has been refunded."),

						self::ORDER_CANCELLED_NOTIF_HEADER 	    => mo_(  'ORDER CANCELLED NOTIFICATION'),
			self::ORDER_CANCELLED_NOTIF_BODY	    => mo_(  'Customer will be sent a new sms notification notifying ".
			                                                "that the order has been cancelled.'),
			self::ORDER_CANCELLED_SMS 			    => mo_(  'Hello {username}, Your order number {order-number} with {site-name} ordered on {order-date} has been cancelled.'),

						self::ORDER_FAILED_NOTIF_HEADER 	    => mo_(  'ORDER FAILED NOTIFICATION'),
			self::ORDER_FAILED_NOTIF_BODY		    => mo_(  "Customer will be sent a new sms notification notifying ".
			                                                "that the order processing has failed."),
			self::ORDER_FAILED_SMS 				    => mo_(  "Hello {username}, Your order number {order-number} with {site-name} ordered on {order-date} has failed. We will contact you shortly."),

						self::ORDER_PENDING_NOTIF_HEADER 	    => mo_(  'ORDER PENDING PAYMENT NOTIFICATION'),
			self::ORDER_PENDING_NOTIF_BODY		    => mo_(  "Customer will be sent a new sms notification notifying".
			                                                " that the order is pending payment."),
			self::ORDER_PENDING_SMS 			    => mo_(  "Hello {username}, Your order number {order-number} with {site-name} ordered on {order-date} is pending payment."),
		)));
	}



	
	public static function showMessage($messageKeys , $data=array())
	{
		$displayMessage = "";
		$messageKeys = explode(" ",$messageKeys);
		$messages = unserialize(MO_WC_ADDON_MESSAGES);
		$commonMessages = unserialize(MO_MESSAGES);
		$messages = array_merge($messages,$commonMessages);
		foreach ($messageKeys as $messageKey)
		{
			if(MoUtility::isBlank($messageKey)) return $displayMessage;
			$formatMessage = $messages[$messageKey];
		    foreach($data as $key => $value)
		    {
		        $formatMessage = str_replace("{{" . $key . "}}", $value ,$formatMessage);
		    }
		    $displayMessage.=$formatMessage;
		}
	    return $displayMessage;
	}
}