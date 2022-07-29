<?php

use OTP\Handler\Forms\WooCommerceSocialLoginForm;

$handler 			= WooCommerceSocialLoginForm::instance();
$wc_social_login	= (Boolean) $handler->isFormEnabled() ? "checked" : "";
$form_name          = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/WooCommerceSocialLoginForm.php';
