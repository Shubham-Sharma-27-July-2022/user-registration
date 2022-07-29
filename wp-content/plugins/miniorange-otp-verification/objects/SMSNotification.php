<?php

namespace OTP\Objects;

use OTP\Helper\MoPHPSessions;

abstract class SMSNotification
{
    
    public $page;

    
    public $isEnabled;

    
    public $tooltipHeader;

    
    public $tooltipBody;

    
    public $recipient;

    
    public $smsBody;

    
    public $defaultSmsBody;

    
    public $title;

    
    public $availableTags;

    
    public $pageHeader;

    
    public $pageDescription;

    
    public $notificationType;

    
    function __construct(){}

    
    abstract public function sendSMS(array $args);


    

    public function setNotifInSession($notificationType)
    {
         MoPHPSessions::addSessionVar('mo_addon_notif_type' , $this->page);
    }

    
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }


    
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
        return $this;
    }


    
    public function setSmsBody($smsBody)
    {
        $this->smsBody = $smsBody;
        return $this;
    }
}