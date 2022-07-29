<?php

namespace OTP\Helper;

use OTP\Objects\BaseMessages;
use OTP\Traits\Instance;

if(! defined( 'ABSPATH' )) exit;


class PolyLangStrings
{
	use Instance;

	private function __construct()
	{
		define("MO_POLY_STRINGS", serialize( array(

			BaseMessages::OTP_SENT_PHONE 		=> MoMessages::showMessage(MoMessages::OTP_SENT_PHONE),
			BaseMessages::OTP_SENT_EMAIL 		=> MoMessages::showMessage(MoMessages::OTP_SENT_EMAIL),
			BaseMessages::ERROR_OTP_EMAIL 		=> MoMessages::showMessage(MoMessages::ERROR_OTP_EMAIL),
			BaseMessages::ERROR_OTP_PHONE 		=> MoMessages::showMessage(MoMessages::ERROR_OTP_PHONE),
			BaseMessages::ERROR_PHONE_FORMAT 	=> MoMessages::showMessage(MoMessages::ERROR_PHONE_FORMAT),
			BaseMessages::CHOOSE_METHOD 		=> MoMessages::showMessage(MoMessages::CHOOSE_METHOD),
			BaseMessages::PLEASE_VALIDATE 		=> MoMessages::showMessage(MoMessages::PLEASE_VALIDATE),
			BaseMessages::ERROR_PHONE_BLOCKED 	=> MoMessages::showMessage(MoMessages::ERROR_PHONE_BLOCKED),
			BaseMessages::ERROR_EMAIL_BLOCKED 	=> MoMessages::showMessage(MoMessages::ERROR_EMAIL_BLOCKED),
			BaseMessages::INVALID_OTP 			=> MoMessages::showMessage(MoMessages::INVALID_OTP),
			BaseMessages::EMAIL_MISMATCH 		=> MoMessages::showMessage(MoMessages::EMAIL_MISMATCH),
			BaseMessages::PHONE_MISMATCH 		=> MoMessages::showMessage(MoMessages::PHONE_MISMATCH),
			BaseMessages::ENTER_PHONE 			=> MoMessages::showMessage(MoMessages::ENTER_PHONE),
			BaseMessages::ENTER_EMAIL 			=> MoMessages::showMessage(MoMessages::ENTER_EMAIL),
			BaseMessages::ENTER_PHONE_CODE 		=> MoMessages::showMessage(MoMessages::ENTER_PHONE_CODE),
			BaseMessages::ENTER_EMAIL_CODE 		=> MoMessages::showMessage(MoMessages::ENTER_EMAIL_CODE),
			BaseMessages::ENTER_VERIFY_CODE 	=> MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE),
			BaseMessages::PHONE_VALIDATION_MSG 	=> MoMessages::showMessage(MoMessages::PHONE_VALIDATION_MSG),
			BaseMessages::MO_REG_ENTER_PHONE 	=> MoMessages::showMessage(MoMessages::MO_REG_ENTER_PHONE),
			BaseMessages::UNKNOWN_ERROR 		=> MoMessages::showMessage(MoMessages::UNKNOWN_ERROR),
			BaseMessages::PHONE_NOT_FOUND 		=> MoMessages::showMessage(MoMessages::PHONE_NOT_FOUND),
			BaseMessages::REGISTER_PHONE_LOGIN 	=> MoMessages::showMessage(MoMessages::REGISTER_PHONE_LOGIN),
			BaseMessages::DEFAULT_SMS_TEMPLATE	=> MoMessages::showMessage(MoMessages::DEFAULT_SMS_TEMPLATE),
			BaseMessages::EMAIL_SUBJECT			=> MoMessages::showMessage(MoMessages::EMAIL_SUBJECT),
			BaseMessages::DEFAULT_EMAIL_TEMPLATE=> MoMessages::showMessage(MoMessages::DEFAULT_EMAIL_TEMPLATE),
			BaseMessages::DEFAULT_BOX_HEADER 	=> 'Validate OTP (One Time Passcode)',
			BaseMessages::GO_BACK 				=> '&larr; Go Back',
			BaseMessages::RESEND_OTP 			=> 'Resend OTP',
			BaseMessages::VALIDATE_OTP 			=> 'Validate OTP',
			BaseMessages::VERIFY_CODE 			=> 'Verify Code',
			BaseMessages::SEND_OTP 				=> 'Send OTP',
			BaseMessages::VALIDATE_PHONE_NUMBER  => 'Validate your Phone Number',
			BaseMessages::VERIFY_CODE_DESC 		=> 'Enter Verification Code',
			BaseMessages::WC_BUTTON_TEXT		=> "Verify Your Purchase",
			BaseMessages::WC_POPUP_BUTTON_TEXT 	=> "Place Order",
			BaseMessages::WC_LINK_TEXT 			=> "[ Click here to verify your Purchase ]",
			BaseMessages::WC_EMAIL_TTLE 		=> "Please Enter an Email Address to enable this.",
			BaseMessages::WC_PHONE_TTLE 		=> "Please Enter a Phone Number to enable this.",
		)));
	}
}