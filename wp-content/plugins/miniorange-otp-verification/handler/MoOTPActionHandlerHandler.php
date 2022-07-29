<?php

namespace OTP\Handler;
if(! defined( 'ABSPATH' )) exit;
use OTP\Helper\CountryList;
use OTP\Helper\GatewayFunctions;
use OTP\Helper\MoConstants;
use OTP\Helper\MocURLOTP;
use OTP\Helper\MoMessages;
use OTP\Helper\MoUtility;
use OTP\Objects\BaseActionHandler;
use OTP\Objects\PluginPageDetails;
use OTP\Objects\TabDetails;
use OTP\Objects\Tabs;
use OTP\Traits\Instance;


class MoOTPActionHandlerHandler extends BaseActionHandler
{
    use Instance;
	function __construct()
	{
	    parent::__construct();
		$this->_nonce = 'mo_admin_actions';
		add_action( 'admin_init', array( $this,'_handle_admin_actions' ),1);
		add_action( 'admin_init', array( $this,'moScheduleTransactionSync'),1);
        add_action( 'admin_init', array( $this,'checkIfPopupTemplateAreSet'),1);
		add_filter( 'dashboard_glance_items', array( $this,'otp_transactions_glance_counter'),10,1);
		add_action( 'admin_post_miniorange_get_form_details', array($this,'showFormHTMLData'));
 		add_action( 'admin_post_miniorange_get_gateway_config', array($this,'showGatewayConfig'));
        add_action( 'admin_notices', array( $this, 'showNotice' ) );
		add_action( 'wp_ajax_mo_dismiss_notice', array( $this, 'dismiss_notice' ) );
	}


	  function showNotice(){
    $licensePageUrl = admin_url().'admin.php?page=pricing';
    $addonPageURL = admin_url().'admin.php?page=addon';
    $current_url    = admin_url().'admin.php?'.$_SERVER['QUERY_STRING'];
    $isNoticeClosed = get_mo_option("mo_hide_notice");
    if($isNoticeClosed !== "mo_hide_notice"){
      if((!strcmp(MOV_TYPE, "EnterpriseGatewayWithAddons")!==0) && ($current_url !== $licensePageUrl)){
        echo '<div class="mo_notice updated notice is-dismissible" style="padding-bottom: 7px;background-color:#e0eeee99;">
        <p style ="font-size:14px;"><img src="'.MOV_FEATURES_GRAPHIC.'" class="show_mo_icon_form" style="width: 3%;margin-bottom: -1%;">&ensp;<b>Thank you for choosing miniOrange OTP Verification.<br><br>With Support for PasswordlLess Login, AlphaNumeric OTP , Selected Country Verification, Password Reset via OTP and many more.<br> Are you looking for WooCommerce SMS Notifications for Admins, Vendors & Customers? <strong>Check it out here <a href='.$addonPageURL.'>WooCommerce Order SMS Notifications</a>.</strong><br><br>AWS SNS, Twilio Gateway & more gateways supported!
        <br>Want to know more? Check it out here : <a href='.$licensePageUrl.'>Plan Details</a>.</b></p>
         </div>';
       }
     }	

   }

  function dismiss_notice(){
    update_mo_option("mo_hide_notice","mo_hide_notice");
   }
	function _handle_admin_actions()
	{
		if(!isset($_POST['option'])) return;
		switch($_POST['option'])
		{
			case "mo_customer_validation_settings":
				$this->_save_settings($_POST);																	 break;
			case "mo_customer_validation_messages":
				$this->_handle_custom_messages_form_submit($_POST);												 break;
			case "mo_validation_contact_us_query_option":
				$this->_mo_validation_support_query($_POST);                                                     break;
			case "mo_otp_extra_settings":
				$this->_save_extra_settings($_POST); 															 break;
			case "mo_otp_feedback_option":
			    $this->_mo_validation_feedback_query();	                                                         break;
            case "check_mo_ln":
                $this->_mo_check_l();											                                 break;
            case "mo_check_transactions":
            	$this->_mo_check_transactions();																			 break;
            case "mo_customer_validation_sms_configuration":
                $this->_mo_configure_sms_template($_POST);														 break;
            case "mo_customer_validation_email_configuration":
                $this->_mo_configure_email_template($_POST);													 break;
            case "mo_customer_customization_form":
            	$this->_mo_configure_custom_form($_POST);														 break;
		}
	}


function _mo_configure_custom_form($post){

		$this->isValidRequest();

		update_mo_option('cf_submit_id' ,MoUtility::sanitizeCheck('cf_submit_id',$post)   ,"mo_otp_");
		update_mo_option('cf_field_id' ,MoUtility::sanitizeCheck('cf_field_id',$post)   ,"mo_otp_");
		update_mo_option('cf_enable_type',MoUtility::sanitizeCheck('cf_enable_type',$post),"mo_otp_");
		update_mo_option('cf_button_text',MoUtility::sanitizeCheck('cf_button_text',$post),"mo_otp_");

	}

	
	function _handle_custom_messages_form_submit($post)
	{
		$this->isValidRequest();
		update_mo_option('success_email_message' ,MoUtility::sanitizeCheck('otp_success_email',$post)   ,"mo_otp_");
		update_mo_option('success_phone_message' ,MoUtility::sanitizeCheck('otp_success_phone',$post)   ,"mo_otp_");
		update_mo_option('error_phone_message'   ,MoUtility::sanitizeCheck('otp_error_phone',$post)     ,"mo_otp_");
		update_mo_option('error_email_message'   ,MoUtility::sanitizeCheck('otp_error_email',$post)     ,"mo_otp_");
		update_mo_option('invalid_phone_message' ,MoUtility::sanitizeCheck('otp_invalid_phone',$post)   ,"mo_otp_");
        update_mo_option('invalid_email_message' ,MoUtility::sanitizeCheck('otp_invalid_email',$post)   ,"mo_otp_");
		update_mo_option('invalid_message'       ,MoUtility::sanitizeCheck('invalid_otp',$post)         ,"mo_otp_");
		update_mo_option('blocked_email_message' ,MoUtility::sanitizeCheck('otp_blocked_email',$post)   ,"mo_otp_");
		update_mo_option('blocked_phone_message' ,MoUtility::sanitizeCheck('otp_blocked_phone',$post)   ,"mo_otp_");

		do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::MSG_TEMPLATE_SAVED),'SUCCESS');
	}


	
	function _save_settings($posted)
	{
	    
	    $tabDetails = TabDetails::instance();
	    
	    $formSettingsTab = $tabDetails->_tabDetails[Tabs::FORMS];
        $this->isValidRequest();
        if (MoUtility::sanitizeCheck("page",$_GET) !== $formSettingsTab->_menuSlug
            && sanitize_text_field($posted['error_message'])) {
            do_action(
                'mo_registration_show_message',
                MoMessages::showMessage(sanitize_text_field($posted['error_message'])),
                'ERROR'
            );
        }
	}


	
	function _save_extra_settings($posted)
	{
		$this->isValidRequest();

		delete_site_option('default_country_code');
		$defaultCountry = isset($posted['default_country_code']) ? sanitize_text_field($posted['default_country_code']) : '';

		update_mo_option('default_country'           ,maybe_serialize(CountryList::$countries[$defaultCountry]));
		update_mo_option('blocked_domains'           ,MoUtility::sanitizeCheck('mo_otp_blocked_email_domains',$posted));
		update_mo_option('blocked_phone_numbers'     ,MoUtility::sanitizeCheck('mo_otp_blocked_phone_numbers',$posted));
		update_mo_option('show_remaining_trans'      ,MoUtility::sanitizeCheck('mo_show_remaining_trans',$posted));
		update_mo_option('show_dropdown_on_form'     ,MoUtility::sanitizeCheck('show_dropdown_on_form',$posted));
		update_mo_option('otp_length'                ,MoUtility::sanitizeCheck('mo_otp_length',$posted));
		update_mo_option('otp_validity'              ,MoUtility::sanitizeCheck('mo_otp_validity',$posted));
		update_mo_option('generate_alphanumeric_otp' ,MoUtility::sanitizeCheck('mo_generate_alphanumeric_otp',$posted));
		update_mo_option('globally_banned_phone'     ,MoUtility::sanitizeCheck('mo_globally_banned_phone',$posted));
		update_mo_option('masterotp_validity'        ,MoUtility::sanitizeCheck('mo_masterotp_validity',$posted));
		update_mo_option('masterotp_admin'           ,MoUtility::sanitizeCheck('mo_masterotp_admin',$posted));
		update_mo_option('masterotp_user'            ,MoUtility::sanitizeCheck('mo_masterotp_user',$posted));
		update_mo_option('masterotp_admins'            ,MoUtility::sanitizeCheck('mo_masterotp_admins',$posted));
		update_mo_option('masterotp_specific_user'            ,MoUtility::sanitizeCheck('mo_masterotp_specific_user',$posted));
		update_mo_option('masterotp_specific_user_details'       ,MoUtility::sanitizeCheck('masterotp_specific_user_details',$posted));



		do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::EXTRA_SETTINGS_SAVED),'SUCCESS');
	}


    
	function _mo_validation_support_query($postData)
	{
	    $email = MoUtility::sanitizeCheck('query_email',$postData);
	    $query = MoUtility::sanitizeCheck('query',$postData);
	    $phone = MoUtility::sanitizeCheck('query_phone',$postData);

		if(!$email || !$query)
		{
			do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::SUPPORT_FORM_VALUES),'ERROR');
			return;
		}

		$submitted  = MocURLOTP::submit_contact_us( $email, $phone, $query );

		if(json_last_error() == JSON_ERROR_NONE && $submitted)
		{
			do_action('mo_registration_show_message',MoMessages::showMessage(MoMessages::SUPPORT_FORM_SENT),'SUCCESS');
			return;
		}

		do_action('mo_registration_show_message',MoMessages::showMessage(MoMessages::SUPPORT_FORM_ERROR),'ERROR');
	}


	
	public function otp_transactions_glance_counter()
	{
		if(!MoUtility::micr() || !MoUtility::isMG()) return;
		$email = get_mo_option('email_transactions_remaining');
		$phone = get_mo_option('phone_transactions_remaining');
		echo "<li class='mo-trans-count'><a href='" . admin_url() . "admin.php?page=mosettings'>"
				. MoMessages::showMessage(MoMessages::TRANS_LEFT_MSG,array('email'=>$email,'phone'=>$phone)). "</a></li>";
	}


	
	public function checkIfPopupTemplateAreSet()
	{
		$email_templates = maybe_unserialize(get_mo_option('custom_popups'));
		if(empty($email_templates)) {
			$templates = apply_filters( 'mo_template_defaults', array() );
			update_mo_option('custom_popups',maybe_serialize($templates));
		}
	}


	
	public function showFormHTMLData()
	{
		$this->isValidRequest();
		$formName = sanitize_text_field($_POST['form_name']);

		$controller = MOV_DIR . 'controllers/';
		$disabled = !MoUtility::micr() ? "disabled" : "";
		$page_list = admin_url().'edit.php?post_type=page';
		ob_start();
		include $controller . 'forms/'.$formName . '.php';
		$string = ob_get_clean();
		wp_send_json( MoUtility::createJson($string,MoConstants::SUCCESS_JSON_TYPE));
	}

	
	public function showGatewayConfig()			
	{
		$this->isValidRequest();
		$gatewayType = $_POST['gateway_type'];
		$gatewayClass = "OTP\Helper\Gateway\\".$gatewayType;
		$disabled = !MoUtility::micr() ? "disabled" : "";
		 $gateway_url  =   get_mo_option('custom_sms_gateway')
                                        ? get_mo_option('custom_sms_gateway')
                                        : '';
		$gatewayConfigView = $gatewayClass::instance()->getGatewayConfigView($disabled,$gateway_url);
		wp_send_json( MoUtility::createJson($gatewayConfigView,MoConstants::SUCCESS_JSON_TYPE));	
	}

	
	function moScheduleTransactionSync()
	{
		if (! wp_next_scheduled('hourlySync') && MoUtility::micr()) {
            wp_schedule_event(time(), 'daily', 'hourlySync');
        }
    }


    
	function _mo_validation_feedback_query()
    {
        $this->isValidRequest();
        $submitType = sanitize_text_field($_POST['miniorange_feedback_submit']);

        if($submitType==="Skip & Deactivate"){
            deactivate_plugins( [MOV_PLUGIN_NAME]);
            delete_mo_option("mo_hide_notice");
            return;
        }

        $deactivatingPlugin = strcasecmp(sanitize_text_field($_POST['plugin_deactivated']),"true")==0;
        $type =  !$deactivatingPlugin ? mo_("[ Plugin Feedback ] : ") : mo_("[ Plugin Deactivated ]");
        
        $feedback = sanitize_text_field($_POST['query_feedback']);
        $feedbackTemplate = file_get_contents(MOV_DIR . 'includes/html/feedback.min.html');
        $current_user = wp_get_current_user();
        $customerType = MoUtility::micv() ? "Premium" : "Free";
        $email = get_mo_option("admin_email");
        $activationDate = get_mo_option("plugin_activation_date");
        $activationDays = round(( strtotime(date("Y-m-d h:i:sa")) - strtotime($activationDate)) / (60 * 60 * 24));
        $activationDateHTML = "<br><br>Days since Activated: ".$activationDays; 
        $feedbackTemplate = str_replace("{{FIRST_NAME}}",$current_user->first_name,$feedbackTemplate);
        $feedbackTemplate = str_replace("{{LAST_NAME}}",$current_user->last_name,$feedbackTemplate);
        $feedbackTemplate = str_replace("{{PLUGIN_TYPE}}",MOV_TYPE.":".$customerType.$activationDateHTML,$feedbackTemplate);
        $feedbackTemplate = str_replace("{{SERVER}}",$_SERVER['SERVER_NAME'],$feedbackTemplate);
        $feedbackTemplate = str_replace("{{EMAIL}}",$email,$feedbackTemplate);
        $feedbackTemplate = str_replace("{{PLUGIN}}",MoConstants::AREA_OF_INTEREST,$feedbackTemplate);
        $feedbackTemplate = str_replace("{{VERSION}}",MOV_VERSION,$feedbackTemplate);
        
        $feedbackTemplate = str_replace("{{TYPE}}",$type,$feedbackTemplate);
        $feedbackTemplate = str_replace("{{FEEDBACK}}",$feedback,$feedbackTemplate);
        
        
        $notif = MoUtility::send_email_notif(
            $email,
            "Xecurify",
            MoConstants::FEEDBACK_EMAIL,
            "WordPress OTP Verification Plugin Feedback",
            $feedbackTemplate
        );

        if($notif) {
            do_action('mo_registration_show_message',MoMessages::showMessage(MoMessages::FEEDBACK_SENT),'SUCCESS');
        } else {
            do_action('mo_registration_show_message',MoMessages::showMessage(MoMessages::FEEDBACK_ERROR),'ERROR');
        }

        
        if($deactivatingPlugin) deactivate_plugins( [MOV_PLUGIN_NAME]);
        delete_mo_option("mo_hide_notice");
    }

	
    function _mo_check_transactions()
    {
        if ( ! empty( $_POST ) && check_admin_referer( 'mo_check_transactions_form', '_nonce' ) ) {
        MoUtility::_handle_mo_check_ln(true,
            get_mo_option('admin_customer_key'),
            get_mo_option('admin_api_key')
        );

        }
    }

    
    function _mo_check_l()
    {
        $this->isValidRequest();
        MoUtility::_handle_mo_check_ln(true,
            get_mo_option('admin_customer_key'),
            get_mo_option('admin_api_key')
        );
    }

    function _mo_configure_sms_template($posted)
    {
        
        if(isset($posted['mo_customer_validation_custom_sms_gateway']) && empty(sanitize_text_field($posted['mo_customer_validation_custom_sms_gateway'])))
            do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::SMS_TEMPLATE_ERROR),'ERROR');

        else
            do_action('mo_registration_show_message',MoMessages::showMessage(MoMessages::SMS_TEMPLATE_SAVED),'SUCCESS');

        $gateway = GatewayFunctions::instance();
        $gateway->_mo_configure_sms_template($posted);
    }

    function _mo_configure_email_template($posted)
    {
        
        $gateway = GatewayFunctions::instance();
        $gateway->_mo_configure_email_template($posted);
    }
}