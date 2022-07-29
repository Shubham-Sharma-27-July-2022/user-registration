<?php

namespace OTP;

use OTP\Handler\EmailVerificationLogic;
use OTP\Handler\FormActionHandler;
use OTP\Handler\MoOTPActionHandlerHandler;
use OTP\Handler\MoRegistrationHandler;
use OTP\Handler\PhoneVerificationLogic;
use OTP\Helper\CountryList;
use OTP\Helper\GatewayFunctions;
use OTP\Helper\MenuItems;
use OTP\Helper\MoConstants;
use OTP\Helper\MoDisplayMessages;
use OTP\Helper\MoMessages;
use OTP\Helper\MoUtility;
use OTP\Helper\MOVisualTour;
use OTP\Helper\PolyLangStrings;
use OTP\Helper\Templates\DefaultPopup;
use OTP\Helper\Templates\ErrorPopup;
use OTP\Helper\Templates\ExternalPopup;
use OTP\Helper\Templates\UserChoicePopup;
use OTP\Objects\PluginPageDetails;
use OTP\Objects\TabDetails;
use OTP\Objects\Tabs;
use OTP\Traits\Instance;
use OTP\Helper\MoAddonListContent;
use OTP\Helper\MoOffer;
use OTP\Handler\CustomForm;
use OTP\Helper\MocURLOTP;
use OTP\Objects\BaseMessages;
use OTP\Helper\MoVersionUpdate;
use OTP\Helper\MoOTPAlphaNumeric;
use OTP\Helper\MoSMSBackupGateway;
use OTP\Helper\MoGloballyBannedPhone;
use OTP\Helper\MoMasterOTP;
if(! defined( 'ABSPATH' )) exit;

final class MoOTP
{
	use Instance;

	private function __construct()
	{
		$this->initializeHooks();
		$this->initializeGlobals();
		$this->initializeHelpers();
		$this->initializeHandlers();
		$this->registerPolyLangStrings();
        $this->registerAddOns();
	}

    
	private function initializeHooks()
	{
		add_action( 'plugins_loaded'				, array( $this, 'otp_load_textdomain'						 )		  );
		add_action( 'admin_menu'					, array( $this, 'miniorange_customer_validation_menu' 		 ) 		  );
		add_action( 'admin_enqueue_scripts'			, array( $this, 'mo_registration_plugin_settings_style'      ) 		  );
		add_action( 'admin_enqueue_scripts'			, array( $this, 'mo_registration_plugin_settings_script' 	 ) 		  );
		add_action( 'wp_enqueue_scripts'		  	, array( $this, 'mo_registration_plugin_frontend_scripts' 	 ),99	  );
		add_action( 'login_enqueue_scripts'		  	, array( $this, 'mo_registration_plugin_frontend_scripts' 	 ),99	  );
		add_action( 'mo_registration_show_message'	, array( $this, 'mo_show_otp_message'    		 			 ),1   , 2);
		add_action( 'hourlySync'					, array( $this, 'hourlySync'								 ) 		  );
		add_action( 'admin_footer'                  , array( $this,	'feedback_request'  						 )        );
        add_filter( 'wp_mail_from_name' 			, array( $this,	'custom_wp_mail_from_name'					 )        );
        add_filter( 'plugin_row_meta'               , array( $this, 'mo_meta_links'                              ),10  , 2);
        add_action( 'wp_enqueue_scripts'			, array( $this, 'load_jquery_on_forms'                       )        );

        add_action( 'plugin_action_links_'.MOV_PLUGIN_NAME, array( $this, 'plugin_action_links'                  ),10  , 1);
    
	}

    
    function load_jquery_on_forms()
    {
        if ( ! wp_script_is( 'jquery', 'enqueued' )) {
            wp_enqueue_script( 'jquery' );
        }
    }

    
	private function initializeHelpers()
	{
		MoMessages::instance();
		MoAddonListContent::instance();
		MoOffer::instance();
		PolyLangStrings::instance();
		MOVisualTour::instance();
		if(file_exists(MOV_DIR.'helper/MoVersionUpdate.php'))
		MoVersionUpdate::instance();
        if(file_exists(MOV_DIR."helper/MoOTPAlphaNumeric.php"))
		MoOTPAlphaNumeric::instance();
		if(file_exists(MOV_DIR."helper/MoSMSBackupGateway.php"))
		MoSMSBackupGateway::instance();
        if(file_exists(MOV_DIR.'helper/MoGloballyBannedPhone.php'))
        MoGloballyBannedPhone::instance(); 
        if(file_exists(MOV_DIR."helper/MoMasterOTP.php"))
		MoMasterOTP::instance();    
	}

    
	private function initializeHandlers()
	{
	    FormActionHandler::instance();
		MoOTPActionHandlerHandler::instance();
		DefaultPopup::instance();
		ErrorPopup::instance();
		ExternalPopup::instance();
		UserChoicePopup::instance();
		MoRegistrationHandler::instance();
		CustomForm::instance();
	}

    
	private function initializeGlobals()
	{
		global $phoneLogic,$emailLogic;
		$phoneLogic = PhoneVerificationLogic::instance();
		$emailLogic = EmailVerificationLogic::instance();
	}

	
	function miniorange_customer_validation_menu()
	{
	    MenuItems::instance();
	}


	
	function  mo_customer_validation_options()
	{
		include MOV_DIR . 'controllers/main-controller.php';
	}


	
	function mo_registration_plugin_settings_style()
	{
		wp_enqueue_style( 'mo_customer_validation_admin_settings_style'	 , MOV_CSS_URL);
		wp_enqueue_style( 'mo_customer_validation_inttelinput_style', MO_INTTELINPUT_CSS);
	}


	
	function mo_registration_plugin_settings_script()
	{
		$countryVal = [];
		wp_enqueue_script( 'mo_customer_validation_admin_settings_script', MOV_JS_URL , array('jquery'));
		wp_enqueue_script( 'mo_customer_validation_form_validation_script', VALIDATION_JS_URL , array('jquery'));
		wp_register_script('mo_customer_validation_inttelinput_script', MO_INTTELINPUT_JS , array('jquery'));
		$countriesavail=CountryList::getCountryCodeList();
		$countriesavail = apply_filters('selected_countries',$countriesavail);

		foreach ($countriesavail as $key => $value) {
            array_push($countryVal, $value);
        }
		wp_localize_script('mo_customer_validation_inttelinput_script', 'moselecteddropdown', array(
			'selecteddropdown' =>  $countryVal,
		));
		wp_enqueue_script('mo_customer_validation_inttelinput_script');
	}


	
	function mo_registration_plugin_frontend_scripts()
	{ 
        $countryVal = [];
		if(!get_mo_option('show_dropdown_on_form')) return;
		$selector = apply_filters( 'mo_phone_dropdown_selector', array() );
		if (MoUtility::isBlank($selector)) return;
		$selector = array_unique($selector); 
		$countriesavail = CountryList::getCountryCodeList();
		$countriesavail = apply_filters('selected_countries',$countriesavail);
        foreach ($countriesavail as $key => $value) {
            array_push($countryVal, $value);
        }
        $defaultCountry = CountryList::getDefaultCountryIsoCode();
        $getIpcountry = apply_filters( 'mo_get_default_country',$defaultCountry);
		wp_register_script('mo_customer_validation_inttelinput_script', MO_INTTELINPUT_JS , array('jquery'));
		wp_localize_script('mo_customer_validation_inttelinput_script', 'moselecteddropdown', array(
			'selecteddropdown' =>  $countryVal,

		));
		wp_enqueue_script('mo_customer_validation_inttelinput_script');

		wp_enqueue_style( 'mo_customer_validation_inttelinput_style', MO_INTTELINPUT_CSS);
		wp_register_script('mo_customer_validation_dropdown_script', MO_DROPDOWN_JS , array('jquery'), MOV_VERSION, true);
		wp_localize_script('mo_customer_validation_dropdown_script', 'modropdownvars', array(
			'selector' =>  json_encode($selector),
			'defaultCountry' => $getIpcountry,
			'onlyCountries' => CountryList::getOnlyCountryList(),
		));
		wp_enqueue_script('mo_customer_validation_dropdown_script');
	}


	
	function mo_show_otp_message($content,$type)
	{
		new MoDisplayMessages($content,$type);
	}


	
	function otp_load_textdomain()
	{
		load_plugin_textdomain( 'miniorange-otp-verification', FALSE, dirname( plugin_basename(__FILE__) ) . '/lang/' );
		do_action('mo_otp_verification_add_on_lang_files');
	}


	
	private function registerPolylangStrings()
	{
		if(!MoUtility::_is_polylang_installed()) return;
		foreach (unserialize(MO_POLY_STRINGS) as $key => $value) {
			pll_register_string($key,$value,'miniorange-otp-verification');
		}
	}


    
    private function registerAddOns()
    {
        
        $gateway = GatewayFunctions::instance();
        $gateway->registerAddOns();
    }


	
	function feedback_request()
	{
		include MOV_DIR . 'controllers/feedback.php';
	}


    
	function mo_meta_links($meta_fields, $file)
    {
        if ( MOV_PLUGIN_NAME === $file ) {
            $meta_fields[] = "<span class='dashicons dashicons-sticky'></span>
            <a href='" . MoConstants::FAQ_URL . "' target='_blank'>" . mo_('FAQs') . "</a>";
        }
        return $meta_fields;
    }


    
    function plugin_action_links($links)
    {
        
        $tabDetails = TabDetails::instance();
        
        $formSettingsTab = $tabDetails->_tabDetails[Tabs::FORMS];
        if(is_plugin_active(MOV_PLUGIN_NAME)) {
            $links = array_merge([
                '<a href="' . esc_url(admin_url('admin.php?page='.$formSettingsTab->_menuSlug)) . '">' .
                    mo_('Settings')
                . '</a>'
            ], $links);
        }
        return $links;
    }

	
	function hourlySync()
	{
        
        $gateway = GatewayFunctions::instance();
        $gateway->hourlySync();
	}

    
    function custom_wp_mail_from_name($original_email_from )
    {
        
        $gateway = GatewayFunctions::instance();
        return $gateway->custom_wp_mail_from_name($original_email_from);
    }
}