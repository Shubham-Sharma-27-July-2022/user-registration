<?php

namespace OTP\Helper;

if(! defined( 'ABSPATH' )) exit;

use OTP\Addons\CustomMessage\MiniOrangeCustomMessage;
use OTP\Addons\PasswordReset\UltimateMemberPasswordReset;
use OTP\Addons\PasswordResetwc\WooCommercePasswordReset;
use OTP\Addons\UmSMSNotification\UltimateMemberSmsNotification;
use OTP\Addons\WcSMSNotification\WooCommerceSmsNotification;
use OTP\Addons\WpSMSNotification\WordPressSmsNotification;
use OTP\Addons\CountryCode\SelectedCountryCode;
use OTP\Addons\regwithphone\RegisterWithPhoneOnly;
use OTP\Addons\PasscodeOverCall\OTPOverCallAddon;
use OTP\Addons\passwordresetwp\WordPressPasswordReset;
use OTP\Addons\ipbasedropdown\EnableIpBaseCountryCode;
use OTP\Addons\APIVerification\APIAddon;
use OTP\Addons\ResendControl\MiniOrangeOTPControl;
use OTP\Addons\MoBulkSMS\MoBulkSMSInit;
use OTP\Addons\CountryCodeDropdown\CountryCodeDropdownInit;
use OTP\Objects\BaseAddOnHandler;
use OTP\Objects\IGatewayFunctions;
use OTP\Objects\NotificationSettings;
use OTP\Traits\Instance;


class MiniOrangeGateway implements IGatewayFunctions
{
    use Instance;

    
    private $applicationName = 'wp_otp_verification';

    

    public function registerAddOns()
    {
        MiniOrangeCustomMessage::instance();
        UltimateMemberPasswordReset::instance();
        UltimateMemberSmsNotification::instance();
        WooCommerceSmsNotification::instance();
        if(file_exists(MOV_DIR.'addons/passwordresetwc'))
            WooCommercePasswordReset::instance();
        if(file_exists(MOV_DIR.'addons/regwithphone'))
            RegisterWithPhoneOnly::instance();
        if(file_exists(MOV_DIR.'addons/wpsmsnotification'))
            WordPressSmsNotification::instance();
        if(file_exists(MOV_DIR.'addons/passcodeovercall'))
            OTPOverCallAddon::instance();
        if(file_exists(MOV_DIR.'addons/passwordresetwp'))
            WordPressPasswordReset::instance();   
        if(file_exists(MOV_DIR.'addons/apiverification'))
            APIAddon::instance();   
        if(file_exists(MOV_DIR.'addons/resendcontrol'))
            MiniOrangeOTPControl::instance();
        if(file_exists(MOV_DIR.'addons/countrycode'))
            SelectedCountryCode::instance();   
        if(file_exists(MOV_DIR.'addons/mobulksms'))
            MoBulkSMSInit::instance();    
        if(file_exists(MOV_DIR.'addons/countrycodedropdown'))
            CountryCodeDropdownInit::instance();   
        if(file_exists(MOV_DIR.'addons/ipbasedropdown'))
             EnableIpBaseCountryCode::instance(); 
    }

    public function showAddOnList()
    {
        
        $addonList = AddOnList::instance();
        $addonList = $addonList->getList();

        $premiumAddonList = PremiumAddOnList::instance();
        $premiumAddonList = $premiumAddonList->getPremiumAddOnList();
        $premiumAddonPageUrl = admin_url().'admin.php?page=pricing&subpage=premaddons';

        
        foreach ($addonList as $addon) {
            echo    '<tr>
                    <td class="addon-table-list-status">
                        '.$addon->getAddOnName().'
                    </td>
                    <td class="addon-table-list-name">
                        <i>
                            '.$addon->getAddOnDesc().'
                        </i>
                    </td>';

            echo'
                    <td class="addon-table-list-actions">
                        <a  class="button-primary button tips" style="background: #349cd9;"
                            href="'.$addon->getSettingsUrl().'">
                            '.mo_("Settings").'
                        </a>
                    </td>';
                 
            echo '
                    </tr>';
         }

            foreach ($premiumAddonList as $key => $value) {
                    if(!array_key_exists($key,$addonList)){
                echo    '<tr>
                                <td class="addon-table-list-status">
                                    '.$value["name"].'
                                </td>
                                <td class="addon-table-list-name">
                                    <i>
                                        '.$value["description"].'
                                    </i>
                                </td>';
                echo'
                        <td class="addon-table-list-actions">
                            <a  class="button-primary button tips" style="background: rgb(250 204 21); color:#000; border:none; display:flex; align-items:center; justify-content:center; gap: 4px; font-weight:bold; padding: 2px 8px;" href="'.$premiumAddonPageUrl.'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <g id="d4a43e0162b45f718f49244b403ea8f4">
                                        <g id="4ea4c3dca364b4cff4fba75ac98abb38">
                                            <g id="2413972edc07f152c2356073861cb269">
                                                <path id="2deabe5f8681ff270d3f37797985a977" d="M20.8007 20.5644H3.19925C2.94954 20.5644 2.73449 20.3887 2.68487 20.144L0.194867 7.94109C0.153118 7.73681 0.236091 7.52728 0.406503 7.40702C0.576651 7.28649 0.801941 7.27862 0.980492 7.38627L7.69847 11.4354L11.5297 3.72677C11.6177 3.54979 11.7978 3.43688 11.9955 3.43531C12.1817 3.43452 12.3749 3.54323 12.466 3.71889L16.4244 11.3598L23.0197 7.38654C23.1985 7.27888 23.4233 7.28702 23.5937 7.40728C23.7641 7.52754 23.8471 7.73707 23.8056 7.94136L21.3156 20.1443C21.2652 20.3887 21.0501 20.5644 20.8007 20.5644Z" fill="black"></path>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                '.mo_("Premium").'
                            </a>
                            </td>';
                        echo '
                            </tr>';
                    }
                }
    }

    

    public function hourlySync()
    {
        $customerKey = get_mo_option('admin_customer_key');
        $apiKey = get_mo_option('admin_api_key');
        if(isset($customerKey) && isset($apiKey)) {
            MoUtility::_handle_mo_check_ln(FALSE, $customerKey, $apiKey);
        }
    }

    public function flush_cache()
    {
        return;
    }

    public function _vlk($post)
    {
        return;
    }

    
    public function mclv()
    {
        return TRUE;
    }



    
     public function isGatewayConfig()
    {
        return TRUE;
    }

    
    
    public function isMG()
    {
        return $this->mclv();
    }

    
    public function getApplicationName()
    {
        return $this->applicationName;
    }

    

    public function custom_wp_mail_from_name($original_email_from)
    {
                return $original_email_from;
    }

    function _mo_configure_sms_template($posted)
    {
        return;     }

    function _mo_configure_email_template($posted)
    {
        return;     }

    public function showConfigurationPage($disabled)
    {
        include MOV_DIR . 'views/mconfiguration.php';
    }

    

    
    public function mo_send_otp_token($authType, $email, $phone)
    {
        if(MO_TEST_MODE) {
            return ['status'=>'SUCCESS','txId'=> MoUtility::rand()];
        } else {
            $content = MocURLOTP::mo_send_otp_token($authType,$email,$phone);
            return json_decode($content,TRUE);
        }
    }

    
    public function mo_send_notif(NotificationSettings $settings)
    {
        $url 		 = MoConstants::HOSTNAME . '/moas/api/notify/send';
        $customerKey = get_mo_option('admin_customer_key');
        $apiKey 	 = get_mo_option('admin_api_key');

        $fields 	 = [
            'customerKey' => $customerKey,
            'sendEmail' => $settings->sendEmail,
            'sendSMS' => $settings->sendSMS,
            'email' => [
                'customerKey' => $customerKey,
                'fromEmail' => $settings->fromEmail,
                'bccEmail' => $settings->bccEmail,
                'fromName' => $settings->fromName,
                'toEmail' => $settings->toEmail,
                'toName' => $settings->toEmail,
                'subject' => $settings->subject,
                'content' => $settings->message
            ],
            'sms' => [
                'customerKey' => $customerKey,
                'phoneNumber' => $settings->phoneNumber,
                'message' => $settings->message
            ]
        ];

        $json 		 = json_encode ( $fields );
        $authHeader  = MocURLOTP::createAuthHeader($customerKey,$apiKey);
        $response 	 = MocURLOTP::callAPI($url, $json, $authHeader);
        return $response;
    }

    

    
    public function mo_validate_otp_token($txId, $otp_token)
    {
        if(MO_TEST_MODE) {
            return MO_FAIL_MODE ? ['status' => ''] : ['status' => 'SUCCESS'];
        } else {
            $content = MocURLOTP::validate_otp_token($txId, $otp_token);
            return json_decode($content,TRUE);
        }
    }

    

    
    public function getConfigPagePointers()
    {
        
        $visualTour = MOVisualTour::instance();
        return [
            $visualTour->tourTemplate(
                'configuration_instructions',
                'right',
                '',
                '<br>Check the links here to see how to change email/sms template, custom gateway, senderID, etc.',
                'Next',
                'emailSms.svg',
                1
            )
        ];
    }
}