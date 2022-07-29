<?php

namespace OTP\Objects;

interface IGatewayFunctions
{
        public function registerAddOns();
    public function showAddOnList();

        public function flush_cache();
    public function _vlk($post);
    public function hourlySync();
    public function mclv();
    public function isGatewayConfig();
    public function isMG();
    public function getApplicationName();

        public function custom_wp_mail_from_name($original_email_from);
    public function _mo_configure_sms_template($posted);
    public function _mo_configure_email_template($posted);
    public function showConfigurationPage($disabled);

        public function mo_send_otp_token($authType,$email,$phone);
    public function mo_send_notif(NotificationSettings $settings);

        public function mo_validate_otp_token($txId,$otp_token);

        public function getConfigPagePointers();
}