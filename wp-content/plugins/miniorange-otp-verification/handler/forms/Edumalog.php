<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;
use \WP_Error;
use \WP_User;


class Edumalog extends FormHandler implements IFormHandler
{
    use Instance;

    private $_byPassAdmin;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
                $this->_formSessionVar = FormSessionVars::EDUMALOG;
        $this->_typePhoneTag = 'mo_edumalog_phone_enable';
        $this->_typeEmailTag = 'mo_edumalog_email_enable';
        $this->_formKey = 'EDUMA_LOGIN';
        $this->_formName = mo_('Eduma Theme Login Form');
        $this->_isFormEnabled = get_mo_option('edumalog_enable');
        $this->_phoneFormId = 'input[name=phone_number]';
        $this->_formDocuments = MoOTPDocs::EDUMA_LOG;
        parent::__construct();
    }

    
    function handleForm()
    {

     $this->_otpType = get_mo_option('edumalog_enable_type');
     $this->_phoneKey = get_mo_option('edumalog_phone_field_key');
     $this->_byPassAdmin = get_mo_option('edumalog_bypass_admin');

     add_action('login_enqueue_scripts',array($this, 'miniorange_register_login_script'));
     add_action('wp_enqueue_scripts'   ,array($this, 'miniorange_register_login_script'));
     add_filter( 'authenticate', array($this,'_handle_mo_wp_login'), 10,3);
     
    }

     function _handle_mo_wp_login($user,$username,$password)
    {   
         if(!MoUtility::isBlank($username)) {
         $user = $this->getUser($username,$password);
         $user_meta     = get_userdata($user->data->ID);
        $user_role  = $user_meta->roles;
        if(($this->_byPassAdmin) &&(in_array('administrator',$user_role))) return;
           if(is_wp_error($user)) return $user;                                
          $this->startOTPVerificationProcess($user,$username,$password);
        }
        return $user;
    }


   function startOTPVerificationProcess($user,$username,$password){

          if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,VerificationType::PHONE)) {
             $this->unsetOTPSessionVariables();
            return;
        }
                                    
            if($this->_otpType === $this->_typePhoneTag){
            $phone_number = get_user_meta($user->data->ID, $this->_phoneKey,true);
            $phone_number = $this->check_phone_length($phone_number);
                        $this->fetchPhoneAndStartVerification($username,$password,$phone_number);
        } else if($this->_otpType ===$this->_typeEmailTag){
                        $email= $user->data->user_email;
            $this->startEmailVerification($username,$email);
        }

   }

    function startEmailVerification($username,$email)
    {   
                MoUtility::initialize_transaction($this->_formSessionVar);
        $this->sendChallenge($username,$email,null,null,VerificationType::EMAIL);
    }

    function fetchPhoneAndStartVerification($username,$password,$phone_number)
    {
        MoUtility::initialize_transaction($this->_formSessionVar);
        $redirect_to = isset($_REQUEST['redirect_to']) ? sanitize_text_field($_REQUEST['redirect_to']) : $_SERVER['HTTP_HOST'];
        $this->sendChallenge($username,null,null,$phone_number,VerificationType::PHONE, $password,$redirect_to,false);
    }

   private function check_phone_length($phone)
    {
        $phone_check=MoUtility::processPhoneNumber($phone);
        return strlen($phone_check)>=5 ? $phone_check: "";
            
    }

     function getUser($username, $password = null)
    {
         $user = is_email( $username ) ? get_user_by("email",$username) : get_user_by("login",$username);
        if($this->_typePhoneTag && MoUtility::validatePhoneNumber($username)){
            $username = MoUtility::processPhoneNumber($username);
             $user = $this->getUserFromPhoneNumber($username);
                      }
             $user = wp_authenticate_username_password(NULL,$user->data->user_login,$password);

        return $user ? $user : new WP_Error( 'INVALID_USERNAME' , mo_(" <b>ERROR:</b> Invalid UserName. ") );
    }

    function getUserFromPhoneNumber($username)
    {
        global $wpdb;
        $results = $wpdb->get_row("SELECT `user_id` FROM `{$wpdb->prefix}usermeta`"
                                    ."WHERE `meta_key` = '$this->_phoneKey' AND `meta_value` =  '$username'");
        return !MoUtility::isBlank($results) ? get_userdata($results->user_id) : false;
    }

     function miniorange_register_login_script()
    {   
        wp_register_script( 'eduumalog', MOV_URL . 'includes/js/edumalog.min.js',array('jquery') );
        wp_localize_script( 'eduumalog', 'eduumalog', array(
                          'otpType'       =>  $this->getVerificationType(),
             'siteURL'          =>  wp_ajax_url(),
                    ));
        wp_enqueue_script( 'eduumalog' );
    }

    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        if(SessionUtils::isOTPInitialized($this->_formSessionVar)){
            miniorange_site_otp_validation_form($user_login, $user_email, $phone_number,
                MoUtility::_get_invalid_otp_method(), "phone", FALSE);
        }
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

                    $username = MoUtility::isBlank($user_login) ? MoUtility::sanitizeCheck('log',$_POST) : $user_login;
            $username = MoUtility::isBlank($username) ? MoUtility::sanitizeCheck('username',$_POST) : $username;
            $this->login_wp_user($username,$extra_data);
    }

    function login_wp_user($user_log,$extra_data=null)
    {
       $user = is_email( $user_log ) ? get_user_by("email",$user_log)                  : (MoUtility::validatePhoneNumber($user_log)                        ? $this->getUserFromPhoneNumber(MoUtility::processPhoneNumber($user_log)) : get_user_by("login",$user_log) ); 
        wp_set_auth_cookie($user->data->ID);
        if($this->_delayOtp && $this->_delayOtpInterval>0) {
        update_user_meta($user->data->ID,$this->_timeStampMetaKey,time());
        }
        $this->unsetOTPSessionVariables();
        do_action( 'wp_login', $user->user_login, $user );
        $redirect = MoUtility::isBlank($extra_data) ? site_url() : $extra_data;
        wp_redirect($redirect);
        exit;
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_formSessionVar,$this->_txSessionId]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && ($this->_otpType === $this->_typePhoneTag)) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_otpType = $this->sanitizeFormPOST('edumalog_enable_type');
        $this->_isFormEnabled = $this->sanitizeFormPOST('edumalog_enable');
        $this->_phoneKey = $this->sanitizeFormPOST('edumalog_phone_field_key');
        $this->_byPassAdmin = $this->sanitizeFormPOST('edumalog_bypass_admin');


        update_mo_option('edumalog_enable',$this->_isFormEnabled);
        update_mo_option('edumalog_enable_type', $this->_otpType);
        update_mo_option('edumalog_phone_field_key', $this->_phoneKey);
        update_mo_option('edumalog_bypass_admin', $this->_byPassAdmin);
    }

         

      function byPassCheckForAdmins() { return $this->_byPassAdmin; }
}