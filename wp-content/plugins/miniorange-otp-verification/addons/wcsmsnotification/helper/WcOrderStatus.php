<?php

namespace OTP\Addons\WcSMSNotification\Helper;

use \ReflectionClass;


final class WcOrderStatus
{
    const PROCESSING = "processing";
    const ON_HOLD 	 = "on-hold";
    const CANCELLED  = "cancelled";
    const PENDING 	 = "pending";
    const FAILED 	 = "failed";
    const COMPLETED  = "completed";
    const REFUNDED 	 = "refunded";


    
    public static function getAllStatus()
    {
        $refl = new ReflectionClass(self::class);
        return array_values($refl->getConstants());
    }
}