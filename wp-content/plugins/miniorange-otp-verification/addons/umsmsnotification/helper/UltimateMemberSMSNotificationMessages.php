<?php

namespace OTP\Addons\UmSMSNotification\Helper;

use OTP\Helper\MoUtility;
use OTP\Objects\BaseMessages;
use OTP\Traits\Instance;


final class UltimateMemberSMSNotificationMessages extends BaseMessages
{
	use Instance;

	private function __construct()
	{
        
		define("MO_UM_ADDON_MESSAGES", serialize( array(
						self::NEW_UM_CUSTOMER_NOTIF_HEADER 	    =>	mo_('NEW ACCOUNT NOTIFICATION'),
			self::NEW_UM_CUSTOMER_NOTIF_BODY 	    => 	mo_("Customers are sent a new account SMS notification".
                                                            " when they sign up on the site."),
			self::NEW_UM_CUSTOMER_SMS			    => 	mo_("Thanks for creating an account on {site-name}. Your ".
			                                                "username is {username}. Login Here: {accountpage-url}"),
			self::NEW_UM_CUSTOMER_ADMIN_NOTIF_BODY 	=> 	mo_("Admins are sent a new account SMS notification when".
                                                            " a user signs up on the site."),
			self::NEW_UM_CUSTOMER_ADMIN_SMS 	    => 	mo_('New User Created on {site-name}. Username: '.
                                                            '{username}. Profile Page: {accountpage-url}'),
		)));
	}



	
	public static function showMessage($messageKeys , $data=array())
	{
		$displayMessage = "";
		$messageKeys = explode(" ",$messageKeys);
		$messages = unserialize(MO_UM_ADDON_MESSAGES);
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