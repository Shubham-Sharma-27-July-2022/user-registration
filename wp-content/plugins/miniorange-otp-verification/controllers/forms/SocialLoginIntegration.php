<?php

use OTP\Handler\Forms\SocialLoginIntegration;

$handler                    = SocialLoginIntegration::instance();
$mo_social_login_enabled		  	= (Boolean) $handler->isFormEnabled() ? "checked" : "";
$mo_social_login_hidden 		  	= $mo_social_login_enabled== "checked" ? "" : "hidden";;
$form_name                  = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/SocialLoginIntegration.php';