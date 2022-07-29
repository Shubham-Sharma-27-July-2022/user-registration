<?php

use OTP\Handler\MoOTPActionHandlerHandler;
use OTP\Helper\MoConstants;

$message = mo_('We are sad to see you go :( Have you found a bug? Did you feel something was missing? 
                Whatever it is, we\'d love to hear from you and get better.');

$submit_message = mo_("Submit & Deactivate");
$submit_message2= mo_( "Submit");

$adminHandler 	= MoOTPActionHandlerHandler::instance();
$nonce          = $adminHandler->getNonceValue();

include MOV_DIR . 'views/feedback.php';



