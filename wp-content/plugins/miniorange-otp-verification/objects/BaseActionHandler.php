<?php

namespace OTP\Objects;

use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoUtility;

class BaseActionHandler
{
    
    protected $_nonce;

    protected function __construct() {}


    
    protected function isValidRequest()
    {
        if (!current_user_can('manage_options') || !check_admin_referer($this->_nonce)) {
            wp_die(MoMessages::showMessage(MoMessages::INVALID_OP));
        }
        return true;
    }


    
    protected function isValidAjaxRequest($key)
    {
        if(!check_ajax_referer($this->_nonce,$key)){
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(BaseMessages::INVALID_OP),
                MoConstants::ERROR_JSON_TYPE
            ));
        }
    }


    
    public function getNonceValue(){ return $this->_nonce; }
}