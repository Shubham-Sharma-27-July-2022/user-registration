<?php

namespace OTP\Traits;

trait Instance {

    private static $_instance = null;

    public static function instance()
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

}