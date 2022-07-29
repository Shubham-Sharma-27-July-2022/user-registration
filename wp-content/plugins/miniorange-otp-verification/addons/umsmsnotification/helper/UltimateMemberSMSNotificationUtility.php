<?php

namespace OTP\Addons\UmSMSNotification\Helper;

use OTP\Helper\MoUtility;
use \WP_User_Query;


class UltimateMemberSMSNotificationUtility {

	
	public static function getAdminPhoneNumber() {
		$user = new WP_User_Query( array(
			'role' => 'Administrator',
			'search_columns' => array( 'ID', 'user_login' )
		) );
		return ! empty( $user->results[0] ) ? array(get_user_meta( $user->results[0]->ID, 'mobile_number', true)) : "";
	}


	
	public static function is_addon_activated()
	{
	    MoUtility::is_addon_activated();
	}
}