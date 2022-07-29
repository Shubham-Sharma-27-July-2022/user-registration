<?php

namespace OTP\Handler;
if(! defined( 'ABSPATH' )) exit;
use OTP\Helper\GatewayFunctions;
use OTP\Helper\MoConstants;
use OTP\Helper\MocURLOTP;
use OTP\Helper\MoMessages;
use OTP\Helper\MoUtility;
use OTP\Objects\BaseActionHandler;
use OTP\Traits\Instance;


class MoRegistrationHandler extends BaseActionHandler
{
    use Instance;

	function __construct()
	{
	    parent::__construct();
	    $this->_nonce = 'mo_reg_actions';
		add_action( 'admin_init',  array( $this, 'handle_customer_registration' ) );
	}


	
	function handle_customer_registration()
	{
		if ( !current_user_can( 'manage_options' )) return;
		if(!isset($_POST['option'])) return;
		$option = sanitize_text_field(trim($_POST['option']));
		switch($option)
		{
			case "mo_registration_register_customer":
				$this->_register_customer($_POST);											   			break;
			case "mo_registration_connect_verify_customer":
				$this->_verify_customer($_POST);													   	break;
			case "mo_registration_validate_otp":
				$this->_validate_otp($_POST);														   	break;
			case "mo_registration_resend_otp":
				$this->_send_otp_token(get_mo_option('admin_email'),
                    "",'EMAIL');  	                                                    break;
			case "mo_registration_phone_verification":
				$this->_send_phone_otp_token($_POST);												   	break;
			case "mo_registration_go_back":
				$this->_revert_back_registration();												   		break;
			case "mo_registration_forgot_password":
				$this->_reset_password();															   	break;
            case "mo_go_to_login_page":
            case "remove_account":
				$this->removeAccount();													                break;
			case "mo_registration_verify_license":
				$this->_vlk($_POST);																	break;
		}
	}


	
	function _register_customer($post)
	{
	    $this->isValidRequest();
		$email 			 = sanitize_email( $_POST['email'] );
		$company 		 = sanitize_text_field($_POST['company']);
		$first_name 	 = sanitize_text_field($_POST['fname']);
		$last_name 		 = sanitize_text_field($_POST['lname']);
		$password 		 = sanitize_text_field($_POST['password'] );
		$confirmPassword = sanitize_text_field($_POST['confirmPassword'] );

		if( strlen( $password ) < 6 || strlen( $confirmPassword ) < 6)
		{
			do_action('mo_registration_show_message',MoMessages::showMessage(MoMessages::PASS_LENGTH),'ERROR');
			return;
		}

		if( $password != $confirmPassword )
		{
			delete_mo_option('verify_customer');
			do_action('mo_registration_show_message',MoMessages::showMessage(MoMessages::PASS_MISMATCH),'ERROR');
			return;
		}

		if( MoUtility::isBlank( $email ) || MoUtility::isBlank( $password )
				|| MoUtility::isBlank( $confirmPassword ) )
		{
			do_action('mo_registration_show_message',MoMessages::showMessage(MoMessages::REQUIRED_FIELDS),'ERROR');
			return;
		}

		update_mo_option( 'company_name'		, $company);
		update_mo_option( 'first_name'		, $first_name);
		update_mo_option( 'last_name'		    , $last_name);
		update_mo_option( 'admin_email'		, $email );
				update_mo_option( 'admin_password'	, $password );

		$content  = json_decode(MocURLOTP::check_customer($email), true);
		switch ($content['status'])
		{
			case 'CUSTOMER_NOT_FOUND':
				$this->_send_otp_token($email,"",'EMAIL');
				break;
			default:
				$this->_get_current_customer($email,$password);
				break;
		}

	}


	
	function _send_otp_token($email,$phone,$auth_type)
	{
        $this->isValidRequest();
		$content  = json_decode(MocURLOTP::mo_send_otp_token($auth_type,$email,$phone), true);
		if(strcasecmp($content['status'], 'SUCCESS') == 0)
		{
			update_mo_option('transactionId',$content['txId']);
			update_mo_option('registration_status','MO_OTP_DELIVERED_SUCCESS');
			if($auth_type=='EMAIL')
				do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::OTP_SENT,array('method'=>$email)),'SUCCESS');
			else
				do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::OTP_SENT,array('method'=>$phone)),'SUCCESS');
		}
		else
		{
			update_mo_option('registration_status','MO_OTP_DELIVERED_FAILURE');
			do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::ERR_OTP),'ERROR');
		}
	}


    
	private function _get_current_customer($email,$password)
	{
		$content     = MocURLOTP::get_customer_key($email,$password);
		$customerKey = json_decode($content, true);
		if(json_last_error() == JSON_ERROR_NONE)
		{
			update_mo_option('admin_email', $email );
			update_mo_option( 'admin_phone', $customerKey['phone'] );
			$this->save_success_customer_config(
			    $customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']
            );
			MoUtility::_handle_mo_check_ln(false,$customerKey['id'], $customerKey['apiKey']);
			do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::REG_SUCCESS),'SUCCESS');
		}
		else
		{
            update_mo_option('admin_email', $email );
			update_mo_option('verify_customer', 'true');
			delete_mo_option('new_registration');
			do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::ACCOUNT_EXISTS),'ERROR');
		}
	}

		function save_success_customer_config($id, $apiKey, $token, $appSecret)
	{
		update_mo_option( 'admin_customer_key'  , $id       );
		update_mo_option( 'admin_api_key'       , $apiKey   );
		update_mo_option( 'customer_token'      , $token    );
		update_mo_option( 'plugin_activation_date'      , date("Y-m-d h:i:sa"));
		delete_mo_option( 'verify_customer'                 );
		delete_mo_option( 'new_registration'                );
		delete_mo_option( 'admin_password'                  );
	}

    
	function _validate_otp($post)
	{
        $this->isValidRequest();
		$otp_token 		 = sanitize_text_field( $post['otp_token'] );
		$email 			 = get_mo_option( 'admin_email');
		$company 		 = get_mo_option( 'company_name');
		$password 		 = get_mo_option( 'admin_password');

		if( MoUtility::isBlank( $otp_token ) )
		{
			update_mo_option('registration_status','MO_OTP_VALIDATION_FAILURE');
			do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::REQUIRED_OTP),'ERROR');
			return;
		}

		
		$content = json_decode(MocURLOTP::validate_otp_token(get_mo_option('transactionId'), $otp_token ),true);
		if(strcasecmp($content['status'], 'SUCCESS') == 0)
		{
			$customerKey = json_decode(
			    MocURLOTP::create_customer($email, $company, $password, $phone = '', $first_name = '', $last_name = ''),
                true
            );
            if(strcasecmp($customerKey['status'], 'INVALID_EMAIL_QUICK_EMAIL')==0){
            	do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::ENTERPRIZE_EMAIL),'ERROR');
            }
			if(strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0)
			{
				$this->_get_current_customer($email,$password);
			}
			else if(strcasecmp($customerKey['status'], 'EMAIL_BLOCKED') == 0 && $customerKey['message']=='error.enterprise.email'){
				do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::ENTERPRIZE_EMAIL),'ERROR');	
			}
			else if(strcasecmp($customerKey['status'], 'FAILED') == 0){
				do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::REGISTRATION_ERROR),'ERROR');	
			}
			else if(strcasecmp($customerKey['status'], 'SUCCESS') == 0)
			{
				$this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
				update_mo_option('registration_status','MO_CUSTOMER_VALIDATION_REGISTRATION_COMPLETE');
				update_mo_option('email_transactions_remaining',MoConstants::EMAIL_TRANS_REMAINING);
				update_mo_option('phone_transactions_remaining',MoConstants::PHONE_TRANS_REMAINING);
				do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::REG_COMPLETE),'SUCCESS');
				header('Location: admin.php?page=pricing');
			}
		}
		else
		{
			update_mo_option('registration_status','MO_OTP_VALIDATION_FAILURE');
			do_action('mo_registration_show_message', MoUtility::_get_invalid_otp_method() ,'ERROR');
		}
	}


		function _send_phone_otp_token($post)
	{
        $this->isValidRequest();
		$phone = sanitize_text_field($_POST['phone_number']);
		$phone = str_replace(' ', '', $phone);
		$pattern = "/[\+][0-9]{1,3}[0-9]{10}/";
		if(preg_match($pattern, $phone, $matches, PREG_OFFSET_CAPTURE))
		{
			update_mo_option('admin_phone',$phone);
			$this->_send_otp_token("",$phone,'SMS');
		}
		else
		{
			update_mo_option('registration_status','MO_OTP_DELIVERED_FAILURE');
			do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::INVALID_SMS_OTP),'ERROR');
		}
	}


    
	function _verify_customer($post)
	{
        $this->isValidRequest();
		$email 	  = sanitize_email( $post['email'] );
		$password = stripslashes($post['password']);

		if( MoUtility::isBlank( $email ) || MoUtility::isBlank( $password ) )
		{
			do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::REQUIRED_FIELDS),'ERROR');
			return;
		}
		$this->_get_current_customer($email,$password);
	}


    
	function _reset_password()
	{
        $this->isValidRequest();
		$email 	  = get_mo_option('admin_email');
		if(!$email)
			do_action('mo_registration_show_message',MoMessages::showMessage(MoMessages::FORGOT_PASSWORD_MESSAGE),"SUCCESS");
		else{
		$forgot_password_response = json_decode(MocURLOTP::forgot_password($email));
		if($forgot_password_response->status == 'SUCCESS')
			do_action('mo_registration_show_message', MoMessages::showMessage(MoMessages::RESET_PASS),'SUCCESS');
		else
			do_action('mo_registration_show_message',MoMessages::showMessage(MoMessages::UNKNOWN_ERROR),'ERROR');
		}
		
	}


    
	function _revert_back_registration()
	{
        $this->isValidRequest();
		update_mo_option('registration_status','');
		delete_mo_option('new_registration');
		delete_mo_option('verify_customer' ) ;
		delete_mo_option('admin_email');
		delete_mo_option('sms_otp_count');
		delete_mo_option('email_otp_count');
		delete_mo_option('plugin_activation_date');
	}


    
    function removeAccount()
    {
        $this->isValidRequest();
        $this->flush_cache();
        wp_clear_scheduled_hook('hourlySync');
        delete_mo_option('transactionId');
        delete_mo_option('admin_password');
        delete_mo_option('registration_status');
        delete_mo_option('admin_phone');
        delete_mo_option('new_registration');
        delete_mo_option('admin_customer_key');
        delete_mo_option('admin_api_key');
        delete_mo_option('customer_token');
        delete_mo_option('verify_customer');
        delete_mo_option('message');
        delete_mo_option('check_ln');
        delete_mo_option('site_email_ckl');
        delete_mo_option('email_verification_lk');
        update_mo_option("verify_customer",true);
        delete_mo_option('plugin_activation_date');
    }

    
    function flush_cache()
    {
        
        $gateway = GatewayFunctions::instance();
        $gateway->flush_cache();
    }

    
    function _vlk($post)
    {
        
        $gateway = GatewayFunctions::instance();
        $gateway->_vlk($post);
    }
}