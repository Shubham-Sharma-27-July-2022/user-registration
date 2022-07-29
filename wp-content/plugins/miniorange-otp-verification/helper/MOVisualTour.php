<?php

namespace OTP\Helper;

if(! defined( 'ABSPATH' )) exit;

use OTP\Traits\Instance;


class MOVisualTour
{
    use Instance;

    protected $_nonce;
    protected $_nonceKey;
    protected $_tourAjaxAction;

    protected function __construct()
    {
        $this->_nonce             =   "mo_admin_actions";
        $this->_nonceKey          =   "security";
        $this->_tourAjaxAction    =   "miniorange-tour-taken";

        add_action('admin_enqueue_scripts', [$this, 'enqueue_visualTour_script']);
        add_action("wp_ajax_{$this->_tourAjaxAction}", [$this,'_update_tour_taken']);
        add_action("wp_ajax_nopriv_{$this->_tourAjaxAction}", [$this,'_update_tour_taken']);
    }

    
    function _update_tour_taken()
    {
        $this->validateAjaxRequest();
        update_mo_option('tourTaken_'.$_POST['pageID'],$_POST['doneTour']);
        die();
    }

    
    protected function validateAjaxRequest()
    {
        if(!check_ajax_referer($this->_nonce,$this->_nonceKey)) {
            wp_send_json(
                MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::INVALID_OP),
                    MoConstants::ERROR_JSON_TYPE
                )
            );
            exit;
        }
    }

    
    function enqueue_visualTour_script()
    {
        wp_register_script( 'tourScript', MOV_URL . 'includes/js/visualTour.min.js?version='.MOV_VERSION , array('jquery') );
        $page = MoUtility::sanitizeCheck('page',$_GET);
        wp_localize_script( 'tourScript', 'moTour', array(
            'siteURL' 		=> 	    wp_ajax_url(),
                        'currentPage'   =>      $_GET,
            'tnonce'        =>      wp_create_nonce($this->_nonce),
            'pageID'        =>      $page,
            'tourData'      =>      $this->getTourData($page),
            'tourTaken'     =>      get_mo_option('tourTaken_'.$page),
            'ajaxAction'    =>      $this->_tourAjaxAction,
            'nonceKey'      =>      wp_create_nonce($this->_nonceKey),
        ));
        wp_enqueue_script('tourScript');
        wp_enqueue_style( 'mo_visual_tour_style',  MOV_URL . 'includes/css/mo-card.min.css?version='.MOV_VERSION);
    }

    
    public function tourTemplate($targetE,$pointToSide,$titleHTML,$contentHTML,$buttonText,$img,$size)
    {
        $cardSize=['small','medium','big'];
        return  [
            'targetE'       =>  $targetE,
            'pointToSide'   =>  $pointToSide,
            'titleHTML'     =>  $titleHTML,
            'contentHTML'   =>  $contentHTML,
            'buttonText'    =>  $buttonText,
            'img'           =>  $img? MOV_URL."includes\\images\\tourIcons\\".$img : $img,
            'cardSize'      =>  $cardSize[$size],
        ];
    }


    
    function getTourData($pageID)
    {
        $tourData = [
            'mosettings'    =>  $this->getMainPagePointers(),
            'otpsettings'   =>  $this->getOtpSettingPointers(),
            'config'        =>  $this->getConfigPagePointers(),
            'messages'      =>  $this->getMessagePointers(),
            'design'        =>  $this->getDesignPagePointers(),
            'addon'         =>  $this->getAddOnPagePointers()
        ];

        $tabs = $this->getTabsPointers();
        if(MoUtility::micr() && MoUtility::mclv()) {
            $tourData['otpaccount'] =  $this->getAccountPagePointers();
        }
        if(!get_mo_option('tourTaken_mosettings')) {
            $tourData['mosettings'] = array_merge($tourData['mosettings'], $tabs);
        }
        return MoUtility::sanitizeCheck($pageID,$tourData);
    }

    private function getTabsPointers()
    {
        return [
            $this->tourTemplate(
                'otpSettingsTab',
                'up',
                '<h1>OTP Settings</h1>',
                'Click here to goto OTP settings.',
                'Next',
                'settingsTab.svg',
                1
            ),

            $this->tourTemplate(
                'emailSmsTemplate',
                'up',
                '<h1>Message / SMS Templates</h1>',
                'Click here to goto template settings.',
                'Next',
                'emailSmsTemplate.svg',
                1
            ),

            $this->tourTemplate(
                'messagesTab',
                'up',
                '<h1>Configure Messages</h1>',
                'Click here to configure the messages shown.',
                'Next',
                'messages.svg',
                1
            ),

            $this->tourTemplate(
                'popDesignTab',
                'up',
                '<h1>Popup Design</h1>',
                'Modify your Pop-Up as you like.',
                'Next',
                'design.svg',
                1
            ),

            $this->tourTemplate(
                'addOnsTab',
                'up',
                '<h1>AddOns</h1>',
                'Check our cool AddOns here.',
                'Next',
                'addOnSetting.svg',
                1
            ),

            $this->tourTemplate(
                'accountButton',
                'up',
                '<h1>Start Using the Plugin</h1>',
                'Register with us to get started.',
                'Next',
                'profile.svg',
                1
            ),

            $this->tourTemplate(
                'faqButton',
                'up',
                '<h1>Any Questions?</h1>',
                'Check our FAQ page for more information.',
                'Next',
                'faq.svg',
                1
            ),

            $this->tourTemplate(
                'upgradeTab',
                'up',
                '<h1>Upgrade or Recharge</h1>',
                'Check our cool Plans for everyone here.',
                'Next',
                'upgrade.svg',
                1
            ),

            $this->tourTemplate(
                'feedbackButton',
                'right',
                '<h1>Any Feedback for Us?</h1>',
                'Any issues or missing features ? Let Us Know.',
                'Next',
                'help.svg',
                1
            ),

            $this->tourTemplate(
                'helpButton',
                'right',
                '<h1>Any Queries?</h1>',
                'Click here to leave us an email.',
                'Next',
                'help.svg',
                1
            ),

            $this->tourTemplate(
                'restart_tour_button',
                'right',
                '<h1>Thank You!</h1>',
                'Click here to Restart the Tour for current tab.',
                'Next',
                'replay.svg',
                1
            ),
        ];
    }


    private function getMainPagePointers()
    {
        return [
            $this->tourTemplate(
                '',
                '',
                '<h1>WELCOME!</h1>',
                'Fasten your seat belts for a quick ride.',
                'Let\'s Go!',
                'startTour.svg',
                2
            ),

            $this->tourTemplate(
                'tabID',
                'up',
                '<br>',
                'This is Form settings page. Enable/Disable OTP verification for your forms here.',
                'Next',
                'formSettings.svg',
                1
            ),

            $this->tourTemplate(
                'searchForm',
                'left',
                '<br>',
                'Type here to find your Form.<br><br>',
                'Next',
                'searchForm.svg',
                1
            ),

            $this->tourTemplate(
                'formList',
                'left',
                '<br>',
                "Select your form from the list <br><br>",
                'Next',
                'choose.svg',
                1
            ),
        ];
    }

    private function getOtpSettingPointers()
    {
        return [
            $this->tourTemplate(
                'country_code_settings',
                'left',
                '<h1>Country Code</h1>',
                'Set your default Country Code here.',
                'Next',
                'maps-and-flags.svg',
                1
            ),

            $this->tourTemplate(
                'dropdownEnable',
                'up',
                '<br>',
                'Enable this to show country code drop down in your Form.',
                'Next',
                'drop-down-list.svg',
                1
            ),

            $this->tourTemplate(
                'otpLengthValidity',
                'right',
                '<br>',
                'Check the links to see how to change OTP Length and Validity.',
                'Next',
                '',
                0
            ),

            $this->tourTemplate(
                'blockedEmailList',
                'left',
                '<h1>Blocked Emails</h1>',
                'Add the email ids here to block them.',
                'Next',
                'blockedEmail.svg',
                1
            ),

            $this->tourTemplate(
                'blockedPhoneList',
                'right',
                '<h1>Blocked Phone Numbers</h1>',
                'Add the phone numbers here to block them.',
                'Next',
                'blockPhone.svg',
                1
            ),
        ];
    }

    private function getConfigPagePointers()
    {
        
        $gatewayFn = GatewayFunctions::instance();
        return $gatewayFn->getConfigPagePointers();
    }

    private function getMessagePointers()
    {
        return [
            $this->tourTemplate(
                '',
                '',
                '<h1>Configure your Messages</h1>',
                'These messages are displayed to your users. Customize it to your liking.'.
                '<style>.mo-tour-content-area>img {padding:0;} .mo-tour-content{padding: 10px 25px 10px 25px;}</style>',
                'Next',
                'allMessages.svg',
                2
            ),
        ];
    }

    private function getDesignPagePointers()
    {
        return [
            $this->tourTemplate(
                'wp-customEmailMsgEditor-editor-container',
                'left',
                '<h1>Desgin Pop-Up</h1>',
                'Design your pop-up to suit your theme, add css, js and more.',
                'Next',
                'popUp.svg',
                1
            ),
            $this->tourTemplate(
                'defaultPreview',
                'right',
                '<h1>Preview Design</h1>',
                'Preview your Design here. Click on Preview Button to see.',
                'Next',
                'preview.svg',
                1
            ),
        ];
    }

    private function getAddOnPagePointers()
    {
        return [
            $this->tourTemplate(
                'addOnsTable',
                'right',
                '<h1>AddOns</h1>',
                'Check out our cool AddOns for WooCommerce and Ultimate Member.',
                'Next',
                'addOns.svg',
                1
            ),
        ];
    }

    private function getAccountPagePointers()
    {
        return [
            $this->tourTemplate(
                'check_btn',
                'right',
                '<h1>Check Licence</h1>',
                "Don't forget to check your Licence here After Upgrade.",
                'Next',
                'account.svg',
                2
            ),
        ];
    }
}