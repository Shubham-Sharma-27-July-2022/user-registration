<?php

namespace OTP\Objects;

if(! defined( 'ABSPATH' )) exit;


class NotificationSettings
{

	public $sendSMS;
	public $sendEmail;
	public $phoneNumber;
	public $fromEmail;
	public $fromName;
	public $toEmail;
	public $toName;
	public $subject;
	public $bccEmail;
	public $message;

	public function __construct()
	{
		if(func_num_args() < 4) {
			$this->createSMSNotificationSettings(func_get_arg(0),func_get_arg(1));
		} else {
			$this->createEmailNotificationSettings(func_get_arg(0),func_get_arg(1),
				func_get_arg(2),func_get_arg(3),func_get_arg(4));
		}
	}

	
	public function createSMSNotificationSettings($phoneNumber,$message)
	{
		$this->sendSMS = TRUE;
		$this->phoneNumber = $phoneNumber;
		$this->message = $message;
	}


	
	public function createEmailNotificationSettings($fromEmail,$fromName,$toEmail,$subject,$message)
	{
		$this->sendEmail = TRUE;
		$this->fromEmail = $fromEmail;
		$this->fromName = $fromName;
		$this->toEmail = $toEmail;
		$this->toName = $toEmail;
		$this->subject = $subject;
		$this->bccEmail = '';
		$this->message = $message;
	}
}