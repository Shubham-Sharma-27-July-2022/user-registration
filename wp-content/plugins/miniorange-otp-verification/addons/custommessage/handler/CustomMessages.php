<?php

namespace OTP\Addons\CustomMessage\Handler;

use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoUtility;
use OTP\Objects\BaseAddOnHandler;
use OTP\Objects\BaseMessages;
use OTP\Traits\Instance;
use OTP\Helper\MoPHPSessions;
use OTP\Helper\MoOTPDocs;


class CustomMessages extends BaseAddOnHandler
{
    use Instance;

    
    public $_adminActions = [
        'mo_customer_validation_admin_custom_phone_notif'   =>  '_mo_validation_send_sms_notif_msg',
        'mo_customer_validation_admin_custom_email_notif'   =>  '_mo_validation_send_email_notif_msg',
    ];

    
    function __construct()
    {
        parent::__construct();
        $this->_nonce = 'mo_admin_actions';
        if(!$this->moAddOnV()) return;
        $this->_addonSessionVar = 'custom_message_addon';
        $this->send_admin_notification();
                foreach ($this->_adminActions as $action => $callback) {
            add_action("wp_ajax_{$action}",[$this,$callback]);
            add_action("admin_post_{$action}",[$this,$callback]);
        }
    }
     
    public function send_admin_notification(){

            if (MoPHPSessions::getSessionVar($this->_addonSessionVar)) {
                MoPHPSessions::getSessionVar($this->_addonSessionVar)['result'] == MoConstants::SUCCESS_JSON_TYPE ?    
                do_action('mo_registration_show_message',MoPHPSessions::getSessionVar( $this->_addonSessionVar)["message"], MoConstants::CUSTOM_MESSAGE_ADDON_SUCCESS) :
                do_action('mo_registration_show_message',MoPHPSessions::getSessionVar( $this->_addonSessionVar)["message"], MoConstants::CUSTOM_MESSAGE_ADDON_ERROR); 
                $this->unsetSessionVariables();
            }
        }
    public function unsetSessionVariables(){
            
            MoPHPSessions::unsetSession($this->_addonSessionVar);
    }
    
    public function _mo_validation_send_sms_notif_msg()
    {
        $_isAjax = MoUtility::sanitizeCheck('ajax_mode',$_POST);
        $_isAjax ? $this->isValidAjaxRequest('security') : $this->isValidRequest();         
        $phone_numbers = explode(";",MoUtility::sanitizeCheck('mo_phone_numbers',$_POST));
        $message = MoUtility::sanitizeCheck('mo_customer_validation_custom_sms_msg',$_POST);
        $content = null;

        foreach ($phone_numbers as $phone) {
            $content = MoUtility::send_phone_notif($phone,$message);
        }
                $_isAjax ? $this->checkStatusAndSendJSON($content) : $this->checkStatusAndShowMessage($content);
    }


    
    public function _mo_validation_send_email_notif_msg()
    {
        $_isAjax = MoUtility::sanitizeCheck('ajax_mode',$_POST);
        $_isAjax ? $this->isValidAjaxRequest('security') : $this->isValidRequest();         
        $email_addresses = explode(";",MoUtility::sanitizeCheck('toEmail',$_POST));
        $content = null;

        foreach ($email_addresses as $email) {
            $content = MoUtility::send_email_notif(
                sanitize_email($_POST['fromEmail']),
                sanitize_text_field($_POST['fromName']),
                sanitize_email($email),
                sanitize_text_field($_POST['subject']),
                stripslashes(sanitize_text_field($_POST['content']))
            );
        }
                $_isAjax ? $this->checkStatusAndSendJSON($content) : $this->checkStatusAndShowMessage($content);
    }


    
    private function checkStatusAndShowMessage($content)
    {
        if(is_null($content)) return;
        $msgType = $content ? MoConstants::SUCCESS : MoConstants::ERROR;
        if($msgType== MoConstants::SUCCESS){
             MoPHPSessions::addSessionVar( $this->_addonSessionVar ,MoUtility::createJson(
                    MoMessages::showMessage(BaseMessages::CUSTOM_MSG_SENT),
                   MoConstants::SUCCESS_JSON_TYPE
               ));
        }
        else{
             MoPHPSessions::addSessionVar( $this->_addonSessionVar ,MoUtility::createJson(
                   MoMessages::showMessage(BaseMessages::CUSTOM_MSG_SENT_FAIL),
                MoConstants::ERROR_JSON_TYPE
               ));
        }
      
        wp_safe_redirect(wp_get_referer());
    }

    
    private function checkStatusAndSendJSON($content)
    {
        if(is_null($content)) return;
        if($content) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(BaseMessages::CUSTOM_MSG_SENT),
                MoConstants::SUCCESS_JSON_TYPE
            ));
        }else{
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(BaseMessages::CUSTOM_MSG_SENT_FAIL),
                MoConstants::ERROR_JSON_TYPE
            ));
        }
    }


    

    
    function setAddonKey()
    {
        $this->_addOnKey = 'custom_messages_addon';
    }

    
    function setAddOnDesc()
    {
        $this->_addOnDesc = mo_("Send Customized message to any phone or email directly from the dashboard.");
    }

    
    function setAddOnName()
    {
        $this->_addOnName = mo_("Custom Messages");
    }

    
    function setAddOnDocs()
    {
        $this->_addOnDocs = MoOTPDocs::CUSTOM_MESSAGES_ADDON_LINK['guideLink'];
    }

     
    function setAddOnVideo()
    {
        $this->_addOnVideo = MoOTPDocs::CUSTOM_MESSAGES_ADDON_LINK['videoLink'];
    }
    
    
    function setSettingsUrl()
    {
        $this->_settingsUrl = add_query_arg( array('addon'=> 'custom'), $_SERVER['REQUEST_URI'] );
    }
}