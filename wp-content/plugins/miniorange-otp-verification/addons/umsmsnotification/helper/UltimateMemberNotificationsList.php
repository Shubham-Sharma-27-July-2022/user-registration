<?php

namespace OTP\Addons\UmSMSNotification\Helper;

use OTP\Addons\UmSMSNotification\Helper\Notifications\UltimateMemberNewCustomerNotification;
use OTP\Addons\UmSMSNotification\Helper\Notifications\UltimateMemberNewUserAdminNotification;
use OTP\Traits\Instance;


class UltimateMemberNotificationsList
{
    
	public $um_new_customer_notif;

    
    public $um_new_user_admin_notif;

    use Instance;

	function __construct()
	{
		$this->um_new_customer_notif  	= UltimateMemberNewCustomerNotification::getInstance();
		$this->um_new_user_admin_notif  = UltimateMemberNewUserAdminNotification::getInstance();
	}


	
	public function getUmNewCustomerNotif()
	{
		return $this->um_new_customer_notif;
	}


	
	public function getUmNewUserAdminNotif()
	{
		return $this->um_new_user_admin_notif;
	}

}