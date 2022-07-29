<?php

use OTP\Addons\PasswordReset\Handler\UMPasswordResetHandler;
use OTP\Handler\MoOTPActionHandlerHandler;


$handler                    = UMPasswordResetHandler::instance();

$adminHandler               = MoOTPActionHandlerHandler::instance();
$umpr_enabled 			    = $handler->isFormEnabled() ? "checked" : "";
$umpr_hidden 			    = $umpr_enabled=="checked" ? "" : "hidden";
$umpr_enabled_type		    = $handler->getOtpTypeEnabled();
$umpr_type_phone	 	    = $handler->getPhoneHTMLTag();
$umpr_type_email	 		= $handler->getEmailHTMLTag();
$form_name                  = $handler->getFormName();
$umpr_button_text           = $handler->getButtonText();
$nonce                      = $adminHandler->getNonceValue();
$formOption                 = $handler->getFormOption();
$umpr_field_key             = $handler->getPhoneKeyDetails();
$umpr_only_phone            = $handler->getIsOnlyPhoneReset() ? "checked" : "";

include UMPR_DIR . 'views/UMPasswordReset.php';