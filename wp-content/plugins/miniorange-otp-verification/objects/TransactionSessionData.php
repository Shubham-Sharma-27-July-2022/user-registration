<?php

namespace OTP\Objects;


class TransactionSessionData
{
    private $emailTransactionId;
    private $phoneTransactionId;

    
    public function getEmailTransactionId()
    {
        return $this->emailTransactionId;
    }

    
    public function setEmailTransactionId($emailTransactionId)
    {
        $this->emailTransactionId = $emailTransactionId;
    }

    
    public function getPhoneTransactionId()
    {
        return $this->phoneTransactionId;
    }

    
    public function setPhoneTransactionId($phoneTransactionId)
    {
        $this->phoneTransactionId = $phoneTransactionId;
    }
}