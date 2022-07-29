<?php

namespace OTP\Objects;

interface IFormHandler
{
        public function unsetOTPSessionVariables();
    public function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType);
    public function handle_failed_verification($user_login,$user_email,$phone_number, $otpType);
    public function handleForm();
    public function handleFormOptions();
    public function getPhoneNumberSelector($selector);
    public function isLoginOrSocialForm($isLoginOrSocialForm);

    
    public function is_ajax_form_in_play($isAjax);
    public function getPhoneHTMLTag();
    public function getEmailHTMLTag();
    public function getBothHTMLTag();
    public function getFormKey();
    public function getFormName();
    public function getOtpTypeEnabled();
    public function disableAutoActivation();
    public function getPhoneKeyDetails();
    public function isFormEnabled();
    public function getEmailKeyDetails();
    public function getButtonText();
    public function getFormDetails();
    public function getVerificationType();
    public function getFormDocuments();
}