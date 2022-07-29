<?php

namespace OTP\Addons\PasswordReset\Handler;

use OTP\Objects\BaseAddOnHandler;
use OTP\Traits\Instance;
use OTP\Helper\MoOTPDocs;


class UMPasswordResetAddOnHandler extends BaseAddOnHandler
{
    use Instance;

    
    function __construct()
    {
        parent::__construct();
        if (!$this->moAddOnV()) return;
        UMPasswordResetHandler::instance();
    }

    
    function setAddonKey()
    {
        $this->_addOnKey = 'um_pass_reset_addon';
    }

    
    function setAddOnDesc()
    {
        $this->_addOnDesc = mo_("Allows your users to reset their password using OTP instead of email links."
            ."Click on the settings button to the right to configure settings for the same.");
    }

    
    function setAddOnName()
    {
        $this->_addOnName = mo_("Ultimate Member Password Reset Over OTP");
    }

    
    function setAddOnDocs()
    {
        $this->_addOnDocs = MoOTPDocs::ULTIMATEMEMBER_PASSWORD_RESET_ADDON_LINK['guideLink'];
    }

     
    function setAddOnVideo()
    {
        $this->_addOnVideo = MoOTPDocs::ULTIMATEMEMBER_PASSWORD_RESET_ADDON_LINK['videoLink'];
    }
    
    function setSettingsUrl()
    {
        $this->_settingsUrl = add_query_arg( array('addon'=> 'umpr_notif'), $_SERVER['REQUEST_URI']);
    }
}