<?php

namespace OTP\Handler;
if(! defined( 'ABSPATH' )) exit;
use OTP\Helper\FormSessionVars;
use OTP\Helper\GatewayFunctions;
use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormSessionData;
use OTP\Objects\VerificationLogic;
use OTP\Traits\Instance;


final class PhoneVerificationLogic extends VerificationLogic
{
    use Instance;

	
	public function _handle_logic($user_login,$user_email,$phone_number,$otp_type,$from_both)
	{
		$this->checkIfUserRegistered($otp_type,$from_both);
		$match = MoUtility::validatePhoneNumber($phone_number);
		switch ($match)
		{
			case 0:
				$this->_handle_not_matched($phone_number,$otp_type,$from_both);						break;
			case 1:
				$this->_handle_matched($user_login,$user_email,$phone_number,$otp_type,$from_both);	break;
		}
	}

	

 function checkIfUserRegistered($otp_type,$from_both){
		if (!MoUtility::micr()) {
			$message = MoMessages::showMessage(MoMessages::NEED_TO_REGISTER);
			if($this->_is_ajax_form()){
				
				wp_send_json(MoUtility::createJson($message,MoConstants::ERROR_JSON_TYPE));
			}
			else{
				miniorange_site_otp_validation_form(null,null,null,$message,$otp_type,$from_both);
			}
		}
	}


	
	public function _handle_matched($user_login,$user_email,$phone_number,$otp_type,$from_both)
	{
		$message = str_replace("##phone##",$phone_number,$this->_get_is_blocked_message());
		if($this->_is_blocked($user_email,$phone_number))
			if($this->_is_ajax_form())
				wp_send_json(MoUtility::createJson($message,MoConstants::ERROR_JSON_TYPE));
			else
				miniorange_site_otp_validation_form(null,null,null,$message,$otp_type,$from_both);
		else{
			do_action("mo_globally_banned_phone_check",$phone_number,$this->_is_ajax_form());
			$this->_start_otp_verification($user_login,$user_email,$phone_number,$otp_type,$from_both);
		}
	}


	
	public function _start_otp_verification($user_login,$user_email,$phone_number,$otp_type,$from_both)
	{
        
        $gateway = GatewayFunctions::instance();
        $verificationType = 'SMS';
        $verificationType = apply_filters("otp_over_call_activation",$verificationType);
        $content = $gateway->mo_send_otp_token($verificationType,'',$phone_number);
		switch ($content['status'])
		{
			case 'SUCCESS':
				$this->_handle_otp_sent($user_login,$user_email,$phone_number,$otp_type,$from_both,$content);
				break;
			default:
				$this->_handle_otp_sent_failed($user_login,$user_email,$phone_number,$otp_type,$from_both,$content);
				break;
		}
	}


	
	public function _handle_not_matched($phone_number,$otp_type,$from_both)
	{
		$message = str_replace("##phone##",$phone_number,$this->_get_otp_invalid_format_message());
		if($this->_is_ajax_form())
			wp_send_json(MoUtility::createJson($message,MoConstants::ERROR_JSON_TYPE));
		else
			miniorange_site_otp_validation_form(null,null,null,$message,$otp_type,$from_both);
	}


	
	public function _handle_otp_sent_failed($user_login,$user_email,$phone_number,$otp_type,$from_both,$content)
	{
		$message = str_replace("##phone##",$phone_number,$this->_get_otp_sent_failed_message());

		if($this->_is_ajax_form())
			wp_send_json(MoUtility::createJson($message,MoConstants::ERROR_JSON_TYPE));
		else
			miniorange_site_otp_validation_form(null,null,null,$message,$otp_type,$from_both);
	}


	
	public function _handle_otp_sent($user_login,$user_email,$phone_number,$otp_type,$from_both,$content)
	{
        SessionUtils::setPhoneTransactionID($content['txId']);
	 
		if(MoUtility::micr() && MoUtility::isMG()) {
            $availSMS = get_mo_option('phone_transactions_remaining');
            if(($availSMS > 0) && (MO_TEST_MODE==false))
            update_mo_option('phone_transactions_remaining',$availSMS - 1);
        }

        $message = str_replace("##phone##",$phone_number,$this->_get_otp_sent_message());
		if($this->_is_ajax_form())
			wp_send_json(MoUtility::createJson($message,MoConstants::SUCCESS_JSON_TYPE));
		else
			miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,$message,$otp_type,$from_both);
	}


	
	public function _get_otp_sent_message()
	{
		$sendMsg = get_mo_option("success_phone_message","mo_otp_");
		return $sendMsg ? mo_($sendMsg) : MoMessages::showMessage(MoMessages::OTP_SENT_PHONE);
	}


	
	public function _get_otp_sent_failed_message()
	{
		$failedMsg = get_mo_option("error_phone_message","mo_otp_");
		$failedMsg = $failedMsg ? mo_($failedMsg) : MoMessages::showMessage(MoMessages::ERROR_OTP_PHONE);

		$failedMsg = apply_filters("mo_get_otp_sent_failed_message",$failedMsg);
		
		return $failedMsg;
	}


	
	public function _get_otp_invalid_format_message()
	{
		$invalidMsg = get_mo_option("invalid_phone_message","mo_otp_");
		return $invalidMsg ? mo_($invalidMsg) : MoMessages::showMessage(MoMessages::ERROR_PHONE_FORMAT);
	}


    
	public function _is_blocked($user_email,$phone_number)
	{
		$blocked_phone_numbers = explode(";",get_mo_option('blocked_phone_numbers'));
		$blocked_phone_numbers = apply_filters("mo_blocked_phones",$blocked_phone_numbers,$phone_number);
		return in_array($phone_number,$blocked_phone_numbers);
	}


	
	public function _get_is_blocked_message()
	{
		$blockedMsg = get_mo_option("blocked_phone_message","mo_otp_");
		return $blockedMsg ? mo_($blockedMsg) : MoMessages::showMessage(MoMessages::ERROR_PHONE_BLOCKED);
	}
}