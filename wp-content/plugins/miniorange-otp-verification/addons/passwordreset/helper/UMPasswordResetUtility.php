<?php

namespace OTP\Addons\PasswordReset\Helper;

use OTP\Helper\MoUtility;


class UMPasswordResetUtility
{
    
    public static function is_addon_activated()
    {
        MoUtility::is_addon_activated();
    }
}