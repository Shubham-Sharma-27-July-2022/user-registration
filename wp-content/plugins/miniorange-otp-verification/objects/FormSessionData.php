<?php

namespace OTP\Objects;

class FormSessionData
{
    private $isInitialized = false;
    private $emailSubmitted;
    private $phoneSubmitted;
    private $emailVerified;
    private $phoneVerified;
    private $emailVerificationStatus;
    private $phoneVerificationStatus;
    private $fieldOrFormId;
    private $userSubmitted;

    function __construct() {}

    
    function init()
    {
        $this->isInitialized = true;
        return $this;
    }

    
    public function getIsInitialized()
    {
        return $this->isInitialized;
    }

    
    public function getEmailSubmitted()
    {
        return $this->emailSubmitted;
    }

    
    public function setEmailSubmitted($emailSubmitted)
    {
        $this->emailSubmitted = $emailSubmitted;
    }

    
    public function getPhoneSubmitted()
    {
        return $this->phoneSubmitted;
    }

    
    public function setPhoneSubmitted($phoneSubmitted)
    {
        $this->phoneSubmitted = $phoneSubmitted;
    }

    
    public function getEmailVerified()
    {
        return $this->emailVerified;
    }

    
    public function setEmailVerified($emailVerified)
    {
        $this->emailVerified = $emailVerified;
    }

    
    public function getPhoneVerified()
    {
        return $this->phoneVerified;
    }

    
    public function setPhoneVerified($phoneVerified)
    {
        $this->phoneVerified = $phoneVerified;
    }

    
    public function getEmailVerificationStatus()
    {
        return $this->emailVerificationStatus;
    }

    
    public function setEmailVerificationStatus($emailVerificationStatus)
    {
        $this->emailVerificationStatus = $emailVerificationStatus;
    }

    
    public function getPhoneVerificationStatus()
    {
        return $this->phoneVerificationStatus;
    }

    
    public function setPhoneVerificationStatus($phoneVerificationStatus)
    {
        $this->phoneVerificationStatus = $phoneVerificationStatus;
    }

    
    public function getFieldOrFormId()
    {
        return $this->fieldOrFormId;
    }

    
    public function setFieldOrFormId($fieldOrFormId)
    {
        $this->fieldOrFormId = $fieldOrFormId;
    }

    
    public function getUserSubmitted()
    {
        return $this->userSubmitted;
    }

    
    public function setUserSubmitted($userSubmitted)
    {
        $this->userSubmitted = $userSubmitted;
    }
}