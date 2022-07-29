<?php

namespace OTP\Addons\WcSMSNotification\Helper;

use OTP\Helper\MoUtility;
use WC_Order;
use \WP_User_Query;


class MoWcAddOnUtility
{

    
    public static function getAdminPhoneNumber()
    {
        $user = new WP_User_Query( array(
            'role' => 'Administrator',
            'search_columns' => array( 'ID', 'user_login' )
        ) );
        return ! empty( $user->results[0] ) ? get_user_meta( $user->results[0]->ID,
            'billing_phone', true) : "";
    }

    
    public static function getCustomerNumberFromOrder($order){
        $user_id 	= $order->get_user_id();
        $phone 		= $order->get_billing_phone();
        return !empty($phone) ? $phone : get_user_meta($user_id,'billing_phone',true);
    }


    
    public static function is_addon_activated()
    {
        MoUtility::is_addon_activated();
    }
}