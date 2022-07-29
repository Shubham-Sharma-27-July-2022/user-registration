<?php

namespace OTP\Objects;


abstract class BaseAddOn implements AddOnInterface
{
    function __construct()
    {
        $this->initializeHelpers();
        $this->initializeHandlers();
        add_action( 'mo_otp_verification_add_on_controller' , array( $this, 'show_addon_settings_page'   ), 1,1);
    }
}