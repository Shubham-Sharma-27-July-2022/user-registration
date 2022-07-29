<?php

namespace OTP\Addons\CustomMessage\Handler;

use OTP\Traits\Instance;


class CustomMessagesShortcode
{
    use Instance;

    
    private $_adminActions;

    
    private $_nonce;

    public function __construct()
    {
        
        $customMessageHandler = CustomMessages::instance();
        $this->_nonce = $customMessageHandler->getNonceValue();
        $this->_adminActions = $customMessageHandler->_adminActions;
        add_shortcode('mo_custom_sms',array($this,'_custom_sms_shortcode') );
        add_shortcode('mo_custom_email',array($this,'_custom_email_shortcode') );
    }

    
    function _custom_sms_shortcode()
    {
        if(!is_user_logged_in()) return;
        $actions = array_keys($this->_adminActions);
        include MCM_DIR . 'views/customSMSBox.php';
        wp_register_script('custom_sms_msg_script',  MCM_SHORTCODE_SMS_JS, ['jquery'], MOV_VERSION);
        wp_localize_script('custom_sms_msg_script', 'movcustomsms', [
            'alt'=> mo_("Sending..."),
            'img'=> MOV_LOADER_URL,
            'nonce'=>wp_create_nonce($this->_nonce),
            'url'=> wp_ajax_url(),
            'action'=> $actions[0],
            'buttonText'=>mo_("Send SMS"),
        ]);
        wp_enqueue_script('custom_sms_msg_script');
    }


    
    function _custom_email_shortcode()
    {
        if(!is_user_logged_in()) return;
        $actions = array_keys($this->_adminActions);
        include MCM_DIR . 'views/customEmailBox.php';
        wp_register_script('custom_email_msg_script',  MCM_SHORTCODE_EMAIL_JS, ['jquery'], MOV_VERSION);
        wp_localize_script('custom_email_msg_script', 'movcustomemail', [
            'alt'=> mo_("Sending..."),
            'img'=> MOV_LOADER_URL,
            'nonce'=>wp_create_nonce($this->_nonce),
            'url'=> wp_ajax_url(),
            'action'=> $actions[1],
            'buttonText'=>mo_("Send Email"),
        ]);
        wp_enqueue_script('custom_email_msg_script');
    }
}