<?php

namespace OTP\Helper;

use OTP\Traits\Instance;
if(! defined( 'ABSPATH' )) exit;

class MoAddonListContent
{

use Instance;
    function __construct()
    {
    
    define("MO_ADDONS_CONTENT",serialize( array(

    "WORDPRESS_SMS_NOTIFICATION" =>      [
        'id'        =>'WORDPRESS_SMS_NOTIFICATION',
        'addonName'  => 'WordPress SMS Notification to Admin & User on Registration',
        'addonDescription'  => 'Allows your site to send out custom SMS notifications to Customers and Administrators when a new user registers on your Wordpress site.<br> Click on the button below for further details.', 
        'addonPrice' => '$19*',
        'addonDetails' => 'This Addon allows the Admin and User to receive New Account SMS Notifications when a new user is created on the Wordpress Site. It is an easy addon to configure and does not require any coding skills.<br><br><li>Customizable SMS Template.</li><li>Notifications can be sent to multiple Admins.</li><br><br><br>',
    ],
    "WOOCOMMERCE_PASSWORD_RESET" =>      [
        'id'=>'WOOCOMMERCE_PASSWORD_RESET',
        'addonName'  => 'WooCommerce Password Reset Over OTP',
        'addonDescription'  => 'Allows your users to reset their password using OTP instead of email links.<br> Click on the button below for further details.<br><br>',
        'addonPrice' => '$19*',
        'addonDetails' => 'This Addon enables OTP Verification before a user resets his/her WooCommerce password on your WooCommerce site hence enhancing the security of the site and restricting unauthorized users from changing their password without verifying themselves.<br><br><li>OTP over Phone supported.</li><li>OTP over Email supported.</li><br><br>'
    ],
    "WORDPRESS_PASSWORD_RESET" =>      [
       'id'=>'WORDPRESS_PASSWORD_RESET',
       'addonName'  => 'WordPress Password Reset Over OTP',
       'addonDescription'  => 'Allows your users to reset their password using OTP instead of email links.<br> Click on the button below for further details.<br><br>',
       'addonPrice' => '$19*',
       'addonDetails' => 'This Addon enables OTP Verification before a user resets his/her WordPress password on your WordPress site hence enhancing the security of the site and restricting unauthorized users from changing their password without verifying themselves.<br><br><li>OTP over Phone supported.</li><li>OTP over Email supported.</li><br><br>'
   ],
    "COUNTRY_DROPDOWN" =>      [
       'id'=>'COUNTRY_DROPDOWN',
       'addonName'  => 'Country Code Dropdown',
       'addonDescription'  => 'Allows you to enable the country code dropdown on any field of your choice.<br>Includes the country code and the country flag for selection.<br><br>',
       'addonPrice' => '$19*',
       'addonDetails' => 'This Addon allows you to enable the country code and flag dropdown selection on any input field that you wish.<br><br>All you need to do is save the ID selector of the input field and the addon is enabled.<br><br>'
   ],
      "GEOLOCATION_COUNTRY_DROPDOWN" =>      [
        'id'=>'GEOLOCATION_COUNTRY_DROPDOWN',
        'addonName'  => 'Geolocation Based Country Code Dropdown',
        'addonDescription'  => 'Allows you to enable the geolocation based country code dropdown on your forms with country code dropdown enabled.<br>Click on the button below for further details.<br><br>',
        'addonPrice' => '$49*',
        'addonDetails' => 'This Addon allows you to enable the geolocation based country code and flag dropdown selection on your forms with OTP Verification enabled.<br><br>All you need to do is enable the addon.<br><br>'
    ],
     
     "SELECTED_COUNTRY" =>      [
       'id'=>'SELECTED_COUNTRY',
       'addonName'  => 'OTP Verification for Selected Countries Only',
       'addonDescription'  => 'Allows OTP Verification to be enabled for selected list of countries only.<br><br><br>',
       'addonPrice' => '$39*',
       'addonDetails' => 'In this Addon you can add a list of countries for which you wish to enable OTP Verification. <br>The country code dropdown will be altered accordingly.<br><br>OTP Verification for any other country out of the selected list will be blocked by the addon.<br><br>'
   ],
       "RESEND_OTP_CONTROL" =>      [
        'id'=>'RESEND_OTP_CONTROL',
        'addonName'  => 'Limit OTP Request',
        'addonDescription'  => 'Allows you to block OTP from being sent out before the set timer is up.<br>Click on the button below for further details.<br><br><br>',
        'addonPrice' => '$49*',
        'addonDetails' => 'This Addon works on limiting malicious users or unwanted OTP requests to be made by blocking the user for the time limit set in the premium addon. This Addon will prevent external attacks by bots. Very easy to set-up and use.<br><br>'

    ],
     "OTP_OVER_CALL" =>      [
        'id'=>'OTP_OVER_CALL',
        'addonName'  => 'OTP Over Phone Call',
        'addonDescription'  => 'Allows you to send OTPs over Phone Call instead of SMSs throughout the plugin.<br>Click on the button below for further details.',
        'addonPrice' => '$49*',
        'addonDetails' => 'This addon allows OTP Verification to take place over the call for your enabled forms. <br>Enabling just a checkbox will turn your OTP over SMSs to OTP over Phone Call throughout the plugin.<br><br>'

    ],
    "REGISTER_USING_ONLY_PHONE" =>      [
        'id'=>'REGISTER_USING_ONLY_PHONE',
        'addonName'  => 'Register Using Only Phone Number',
        'addonDescription'  => 'Allows your users to register on your Wordpress site using only their Phone Number instead of Email address or Username.<br>Click on the button below for further details.',
        'addonPrice' => '$49*',
          'addonDetails' => 'This Addon allows the user to register on the Wordpress site using his/her Phone number hence bypassing the registration through Email Id. This enhances the security of the site by restricting unauthorized users from accessing the site without Phone Verification.<br><br><b>Supported forms for login:</b><li>Default Wordpress Login Form.</li><li>Woocommerce Login Form.</li><li>Ultimate Member Login Form.</li><li>For other Login Forms use <b>Login using only Phone Number addon</b> shown below.</li><br><br>'
    ],

       "LOGIN_USING_ONLY_PHONE" =>      [
        'id'=>'LOGIN_USING_ONLY_PHONE',
        'addonName'  => 'Login Using Only Phone Number',
        'addonDescription'  => 'Allows your users to Login into your Wordpress site using Phone verification via OTP.<br>Click on the button below for further details.',
        'addonPrice' => '$49*',
        'addonDetails' => 'This will allow your users to log into your site using only their registered Phone number after OTP Verification.<br><br><li>This Addon can be customized as per your login form.</li><br>'

    ],

    "OTP_VERIFICATION_API" =>      [
       'id'=>'OTP_VERIFICATION_API',
       'addonName'  => 'OTP Verification for Android/IOS Application',
       'addonDescription'  => 'Provides APIs to connect your Wordpress site and mobile application for OTP Verification. OTP sending and validating APIs are included. ',
       'addonPrice' => '$89*',
       'addonDetails' => 'This Addon provides APIs to connect your Wordpress site with your mobile application to enable OTP Verification. The addon consists of two APIs, one each for sending OTP and validating OTP. The addon supports both miniOrange gateway as well as Your own SMS(custom) gateway. <br><br><li>Registers Users from mobile application to Wordpress site.</li><li>OTP over Phone supported.</li><li>OTP over Email supported.</li><br><br>'
   ],

    )));


    }
    public static function showAddonsContent(){
        $displayMessage = "";
        $messages = unserialize(MO_ADDONS_CONTENT);
        echo '<div class="mo_otp_wrapper">';
        $queryBody = "Hi! I am interested in the {{addonName}} addon, could you please tell me more about this addon?";
        foreach ($messages as $messageKey)
        {
            echo'<div id="'.$messageKey["addonName"].'">
                                <center><h3 style="color:white;">'.$messageKey["addonName"].'</h3></center><br>
                                <div class="details-front '.$messageKey['id'].'"> <center><h3 style="color:white;font-size:30px">'.$messageKey["addonPrice"].'<br></h3>'.$messageKey["addonDescription"].'</center><br>
                                </div>
                                 <div class="details-back'.''.$messageKey['id'].'" style="display:none;">'.$messageKey["addonDetails"].'</div>
                                <footer>
                                    <center><input type="button" class="button button-primary button-large" onclick="otpSupportOnClick(\''.str_replace("{{addonName}}",$messageKey["addonName"], $queryBody).'\');" value="Interested"/>

                                    <input id="otpAddonDetails" type="button" class="button button-primary button-large" onclick="otpAddonDetailsOnClick(\''.$messageKey["id"].'\');" value="More Details"/>
                                    </center>
                                    </footer>
                                </div>
                            ';

                                }
        echo '</div><br>';
        return $displayMessage;
    }

}