<?php

namespace OTP\Helper;

if(! defined( 'ABSPATH' )) exit;

use OTP\Objects\FormSessionData;
use OTP\Objects\TransactionSessionData;
use OTP\Objects\VerificationType;

final class SessionUtils
{

    
    static function isOTPInitialized($key)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            return $formData->getIsInitialized();
        }
        return FALSE;
    }

    
    static function addEmailOrPhoneVerified($key, $val, $otpType)
    {
        switch ($otpType)
        {
            case VerificationType::PHONE:
                self::addPhoneVerified($key, $val);        break;
            case VerificationType::EMAIL:
                self::addEmailVerified($key, $val);        break;
        }
    }

    static function addEmailSubmitted($key,$val)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            $formData->setEmailSubmitted($val);
            MoPHPSessions::addSessionVar($key, $formData);
        }
    }

    static function addPhoneSubmitted($key,$val)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            $formData->setPhoneSubmitted($val);
            MoPHPSessions::addSessionVar($key, $formData);
        }
    }

    static function addEmailVerified($key,$val)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            $formData->setEmailVerified($val);
            MoPHPSessions::addSessionVar($key, $formData);
        }
    }

    static function addPhoneVerified($key,$val)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            $formData->setPhoneVerified($val);
            MoPHPSessions::addSessionVar($key, $formData);
        }
    }

    
    static function addStatus($key,$val,$type)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            if(!$formData->getIsInitialized())  return;
            if($type === VerificationType::EMAIL)  $formData->setEmailVerificationStatus($val);
            if($type === VerificationType::PHONE)  $formData->setPhoneVerificationStatus($val);
            MoPHPSessions::addSessionVar($key, $formData);
        }
    }

    
    static function isStatusMatch($key, $status, $type)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            switch ($type) {
                case VerificationType::EMAIL:
                    return $status === $formData->getEmailVerificationStatus();
                case VerificationType::PHONE:
                    return $status === $formData->getPhoneVerificationStatus();
                case VerificationType::BOTH:
                    return $status === $formData->getEmailVerificationStatus()
                        || $status === $formData->getPhoneVerificationStatus();
            }
        }
        return FALSE;
    }

    static function isEmailVerifiedMatch($key, $string)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            return $string === $formData->getEmailVerified();
        }
        return FALSE;
    }

    static function isPhoneVerifiedMatch($key, $string)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            return $string === $formData->getPhoneVerified();
        }
        return FALSE;
    }

    static function setEmailTransactionID($txId)
    {
        
        $transactionData = MoPHPSessions::getSessionVar(FormSessionVars::TX_SESSION_ID);
        if(!$transactionData instanceof TransactionSessionData) {
            $transactionData = new TransactionSessionData();
        }
        $transactionData->setEmailTransactionId($txId);
        MoPHPSessions::addSessionVar(FormSessionVars::TX_SESSION_ID,$transactionData);
    }

    static function setPhoneTransactionID($txId)
    {
        
        $transactionData = MoPHPSessions::getSessionVar(FormSessionVars::TX_SESSION_ID);
        if(!$transactionData instanceof TransactionSessionData) {
            $transactionData = new TransactionSessionData();
        }
        $transactionData->setPhoneTransactionId($txId);
        MoPHPSessions::addSessionVar(FormSessionVars::TX_SESSION_ID,$transactionData);
    }

    
    static function getTransactionId($otpType)
    {
        
        $transactionData = MoPHPSessions::getSessionVar(FormSessionVars::TX_SESSION_ID);
        if($transactionData instanceof TransactionSessionData) {
            switch ($otpType) {
                case VerificationType::EMAIL:
                    return $transactionData->getEmailTransactionId();
                case VerificationType::PHONE:
                    return $transactionData->getPhoneTransactionId();
                case VerificationType::BOTH:
                    return MoUtility::isBlank($transactionData->getPhoneTransactionId())
                        ? $transactionData->getEmailTransactionId() : $transactionData->getPhoneTransactionId();
            }
        }
        return '';
    }

    
    static function unsetSession($keys)
    {
        foreach ($keys as $key) {
            MoPHPSessions::unsetSession($key);
        }
    }

    static function isPhoneSubmittedAndVerifiedMatch($key)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            return $formData->getPhoneVerified() === $formData->getPhoneSubmitted();
        }
        return FALSE;
    }

    static function isEmailSubmittedAndVerifiedMatch($key)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            return $formData->getEmailVerified() === $formData->getEmailSubmitted();
        }
        return FALSE;
    }

    static function setFormOrFieldId($key,$val)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            $formData->setFieldOrFormId($val);
            MoPHPSessions::addSessionVar($key, $formData);
        }
    }

    static function getFormOrFieldId($key)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            return $formData->getFieldOrFormId();
        }
        return '';
    }

    static function initializeForm($form)
    {
        
        $formData = new FormSessionData();
        MoPHPSessions::addSessionVar($form,$formData->init());
    }

    static function addUserInSession($key,$val)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            $formData->setUserSubmitted($val);
            MoPHPSessions::addSessionVar($key, $formData);
        }
    }

    static function getUserSubmitted($key)
    {
        
        $formData = MoPHPSessions::getSessionVar($key);
        if($formData instanceof FormSessionData) {
            return $formData->getUserSubmitted();
        }
        return '';
    }
}