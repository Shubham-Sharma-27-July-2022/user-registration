<?php

use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Objects\Tabs;
use OTP\Helper\MoUtility;

$request_uri    = remove_query_arg(['addon','form','subpage'],$_SERVER['REQUEST_URI']);
$profile_url	= add_query_arg( array('page' => $tabDetails->_tabDetails[Tabs::ACCOUNT]->_menuSlug), $request_uri );
$help_url       = MoConstants::FAQ_URL;
$registerMsg    = MoMessages::showMessage(MoMessages::REGISTER_WITH_US,[ "url"=> $profile_url ]);
$activationMsg  = MoMessages::showMessage(MoMessages::ACTIVATE_PLUGIN,[ "url"=> $profile_url ]);
$gateway_url    = add_query_arg( array('page' => $tabDetails->_tabDetails[Tabs::SMS_EMAIL_CONFIG]->_menuSlug), $request_uri);
$gatewayMsg     = MoMessages::showMessage(MoMessages::CONFIG_GATEWAY,[ "url"=> $gateway_url ]);
$active_tab 	= sanitize_text_field($_GET['page']);
$license_url	= add_query_arg( array('page' => $tabDetails->_tabDetails[Tabs::PRICING]->_menuSlug), $request_uri );
$nonce          = $adminHandler->getNonceValue();
$isLoggedIn 	= MoUtility::micr();
$isFreePlugin	= strcmp(MOV_TYPE, "MiniOrangeGateway")===0;

include MOV_DIR . 'views/navbar.php';