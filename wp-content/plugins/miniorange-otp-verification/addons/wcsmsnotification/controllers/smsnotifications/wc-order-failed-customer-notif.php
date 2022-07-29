<?php

use OTP\Helper\MoUtility;

$goBackURL 			= remove_query_arg( array('sms'), $_SERVER['REQUEST_URI'] );
$smsSettings		= $notification_settings->getWcOrderFailedNotif();
$enableDisableTag 	= $smsSettings->page.'_enable';
$textareaTag		= $smsSettings->page.'_smsbody';
$recipientTag		= $smsSettings->page.'_recipient';
$formOptions 		= $smsSettings->page.'_settings';

if(MoUtility::areFormOptionsBeingSaved($formOptions))
{
    $isEnabled = array_key_exists($enableDisableTag, $_POST) ? TRUE : FALSE;
        $sms = MoUtility::isBlank($_POST[$textareaTag]) ? $smsSettings->defaultSmsBody : MoUtility::sanitizeCheck($textareaTag,$_POST);

    $notification_settings->getWcOrderFailedNotif()->setIsEnabled($isEnabled);
        $notification_settings->getWcOrderFailedNotif()->setSmsBody($sms);

    update_wc_option('notification_settings',$notification_settings);
    $smsSettings	= $notification_settings->getWcOrderFailedNotif();
}

$recipientValue		= $smsSettings->recipient;
$enableDisable 		= $smsSettings->isEnabled ? "checked" : "";

include MSN_DIR . '/views/smsnotifications/wc-customer-sms-template.php';