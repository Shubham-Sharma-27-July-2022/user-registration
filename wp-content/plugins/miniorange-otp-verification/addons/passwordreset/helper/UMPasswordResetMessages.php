<?php

namespace OTP\Addons\PasswordReset\Helper;

use OTP\Helper\MoUtility;
use OTP\Objects\BaseMessages;
use OTP\Traits\Instance;


final class UMPasswordResetMessages extends BaseMessages
{
    use Instance;

    private function __construct()
    {
        
        define("MO_UMPR_ADDON_MESSAGES", serialize( array(
            self::USERNAME_MISMATCH => mo_(  'Username that the OTP was sent to and the username submitted do not match'),
            self::USERNAME_NOT_EXIST=> mo_(  "We can't find an account registered with that address or ".
                                             "username or phone number"),
            self::RESET_LABEL       => mo_( "To reset your password, please enter your email address, username or phone number below"),
            self::RESET_LABEL_OP    => mo_( "To reset your password, please enter your registered phone number below"),
        )));
    }


    
    public static function showMessage($messageKeys , $data=array())
    {
        $displayMessage = "";
        $messageKeys = explode(" ",$messageKeys);
        $messages = unserialize(MO_UMPR_ADDON_MESSAGES);
        $commonMessages = unserialize(MO_MESSAGES);
        $messages = array_merge($messages,$commonMessages);
        foreach ($messageKeys as $messageKey)
        {
            if(MoUtility::isBlank($messageKey)) return $displayMessage;
            $formatMessage = $messages[$messageKey];
            foreach($data as $key => $value)
            {
                $formatMessage = str_replace("{{" . $key . "}}", $value ,$formatMessage);
            }
            $displayMessage.=$formatMessage;
        }
        return $displayMessage;
    }
}