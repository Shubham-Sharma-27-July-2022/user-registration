<?php

use OTP\Helper\MoConstants;
use OTP\Helper\MoUtility;
use OTP\Objects\PluginPageDetails;
use OTP\Objects\Tabs;

$page_list = admin_url().'edit.php?post_type=page';
$plan_type = MoUtility::micv() ? 'wp_otp_verification_upgrade_plan' : 'wp_otp_verification_basic_plan';

$nonce = $adminHandler->getNonceValue();
$action = add_query_arg([
    "page"  =>  $tabDetails->_tabDetails[Tabs::FORMS]->_menuSlug,
    "form"  =>  "configured_forms#configured_forms"
]);
$formsListPage = add_query_arg(
    "page",
    $tabDetails->_tabDetails[Tabs::FORMS]->_menuSlug."#form_search",
    remove_query_arg(["form"])
);

$formName = isset( $_GET[ 'form' ]) ? sanitize_text_field($_GET['form']) : false;
$showConfiguredForms = $formName == "configured_forms";


$otpSettingsTab = $tabDetails->_tabDetails[Tabs::OTP_SETTINGS];
$otpSettings = $otpSettingsTab->_url;

$configTab = $tabDetails->_tabDetails[Tabs::SMS_EMAIL_CONFIG];
$config= $configTab->_url;

$designTab = $tabDetails->_tabDetails[Tabs::DESIGN];
$design = $designTab->_url;

$addOnTab = $tabDetails->_tabDetails[Tabs::ADD_ONS];
$addon = $addOnTab->_url;

$contactusTab = $tabDetails->_tabDetails[Tabs::CONTACT_US];
$contactus = $contactusTab->_url;

$support = MoConstants::FEEDBACK_EMAIL;

include MOV_DIR . 'views/settings.php';
include MOV_DIR . 'views/instructions.php';