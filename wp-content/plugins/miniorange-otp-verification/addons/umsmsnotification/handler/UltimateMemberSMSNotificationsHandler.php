<?php

namespace OTP\Addons\UmSMSNotification\Handler;

	use OTP\Addons\UmSMSNotification\Helper\UltimateMemberNotificationsList;
    use OTP\Objects\BaseAddOnHandler;
    use OTP\Traits\Instance;
    use OTP\Helper\MoOTPDocs;

    
	class UltimateMemberSMSNotificationsHandler extends BaseAddOnHandler
	{
        use Instance;

        
        private $notificationSettings;

		
		function __construct()
		{
			parent::__construct();
			if(!$this->moAddOnV()) return;
			$this->notificationSettings = get_umsn_option('notification_settings')
                ? get_umsn_option('notification_settings') : UltimateMemberNotificationsList::instance();
			add_action('um_registration_complete',array($this,'mo_send_new_customer_sms_notif') , 1 ,2);
		}


		
		function mo_send_new_customer_sms_notif($user_id, array $args)
		{
			$this->notificationSettings->getUmNewCustomerNotif()->sendSMS(array_merge(['customer_id'=>$user_id],$args));
			$this->notificationSettings->getUmNewUserAdminNotif()->sendSMS(array_merge(['customer_id'=>$user_id],$args));
		}


		
		function unhook()
		{
			remove_action( 'um_registration_complete', 'um_send_registration_notification' );
		}


		
        function setAddonKey()
        {
            $this->_addOnKey = 'um_sms_notification_addon';
        }

        
        function setAddOnDesc()
        {
            $this->_addOnDesc = mo_("Allows your site to send custom SMS notifications to your customers."
                ."Click on the settings button to the right to see the list of notifications that go out.");
        }

        
        function setAddOnName()
        {
            $this->_addOnName = mo_("Ultimate Member SMS Notification");
        }

         
   		 function setAddOnDocs()
    	{
        $this->_addOnDocs = MoOTPDocs::ULTIMATEMEMBER_SMS_NOTIFICATION_LINK['guideLink'];
    	}

     	
    	function setAddOnVideo()
    	{
        $this->_addOnVideo = MoOTPDocs::ULTIMATEMEMBER_SMS_NOTIFICATION_LINK['videoLink'];
    	}
        
        function setSettingsUrl()
        {
            $this->_settingsUrl = add_query_arg( array('addon'=> 'um_notif'), $_SERVER['REQUEST_URI']);
        }
    }