<?php

use OTP\Helper\MoUtility;

$goBackURL 			= remove_query_arg( array('sms'), $_SERVER['REQUEST_URI'] );
$smsSettings		= $notification_settings->getWcAdminOrderStatusNotif();
$enableDisableTag 	= $smsSettings->page.'_enable';
$textareaTag		= $smsSettings->page.'_smsbody';
$recipientTag		= $smsSettings->page.'_recipient';
$formOptions 		= $smsSettings->page.'_settings';

if(MoUtility::areFormOptionsBeingSaved($formOptions))
{
    $isEnabled = array_key_exists($enableDisableTag, $_POST) ? TRUE : FALSE;
    $recipientTag = serialize(explode(";",MoUtility::sanitizeCheck($recipientTag,$_POST)));
    $sms = MoUtility::isBlank($_POST[$textareaTag]) ? $smsSettings->defaultSmsBody : MoUtility::sanitizeCheck($textareaTag,$_POST);

    $notification_settings->getWcAdminOrderStatusNotif()->setIsEnabled($isEnabled);
    $notification_settings->getWcAdminOrderStatusNotif()->setRecipient($recipientTag);
    $notification_settings->getWcAdminOrderStatusNotif()->setSmsBody($sms);

    update_wc_option('notification_settings',$notification_settings);
    $smsSettings	= $notification_settings->getWcAdminOrderStatusNotif();
}

$recipientValue     = maybe_unserialize($smsSettings->recipient);
$recipientValue     = is_array($recipientValue) ? implode(";",$recipientValue) : $recipientValue;
$enableDisable 		= $smsSettings->isEnabled ? "checked" : "";

include MSN_DIR . '/views/smsnotifications/wc-admin-sms-template.php';