<?php

namespace OTP\Objects;


abstract class VerificationLogic
{
        abstract public function _handle_logic($user_login,$user_email,$phone_number,$otp_type,$from_both);
    abstract public function _handle_otp_sent($user_login,$user_email,$phone_number,$otp_type,$from_both,$content);
    abstract public function _handle_otp_sent_failed($user_login,$user_email,$phone_number,$otp_type,$from_both,$content);
    abstract public function _get_otp_sent_message();
    abstract public function _get_otp_sent_failed_message();
    abstract public function _get_otp_invalid_format_message();
    abstract public function _get_is_blocked_message();
    abstract public function _handle_matched($user_login,$user_email,$phone_number,$otp_type,$from_both);
    abstract public function _handle_not_matched($phone_number,$otp_type,$from_both);
    abstract public function _start_otp_verification($user_login,$user_email,$phone_number,$otp_type,$from_both);
    abstract public function _is_blocked($user_email,$phone_number);

    
    public static function _is_ajax_form()
    {
        return (Bool) apply_filters('is_ajax_form',FALSE);
    }
}