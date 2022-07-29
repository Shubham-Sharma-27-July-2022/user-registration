<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoPHPSessions;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Traits\Instance;
use ReflectionException;
use WC_Emails;
use WC_Social_Login_Provider_Profile;


class WooCommerceSocialLoginForm extends FormHandler implements IFormHandler
{
    use Instance;

    
    private $_oAuthProviders 	= array(
        "facebook","twitter","google",
        "amazon","linkedIn","paypal",
        "instagram","disqus","yahoo","vk"
    );

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = TRUE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::WC_SOCIAL_LOGIN;
        $this->_otpType = "phone";
        $this->_phoneFormId = "#mo_phone_number";
        $this->_formKey = 'WC_SOCIAL_LOGIN';
        $this->_formName = mo_("Woocommerce Social Login ( SMS Verification Only )");
        $this->_isFormEnabled = get_mo_option('wc_social_login_enable');
        $this->_formDocuments = MoOTPDocs::WC_SOCIAL_LOGIN;
        parent::__construct();
    }


    
    function handleForm()
    {
        $this->includeRequiredFiles();
        foreach ($this->_oAuthProviders as $provider)
        {
            add_filter( 'wc_social_login_'.$provider.'_profile', array($this,'mo_wc_social_login_profile'), 99 ,2 );
            add_filter( 'wc_social_login_' . $provider . '_new_user_data', array($this,'mo_wc_social_login'), 99 ,2 );
        }
        $this->routeData();
    }


    function routeData()
    {
        if(!array_key_exists('option', $_REQUEST)) return;

        switch (trim($_REQUEST['option']))
        {
            case "miniorange-ajax-otp-generate":
                $this->_handle_wc_ajax_send_otp($_POST);			break;
            case "miniorange-ajax-otp-validate":
                $this->processOTPEntered($_REQUEST);				break;
            case "mo_ajax_form_validate":
                $this->_handle_wc_create_user_action($_POST);		break;
        }
    }


    
    function includeRequiredFiles()
    {
        if( !function_exists('is_plugin_active') ) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if( is_plugin_active( 'woocommerce-social-login/woocommerce-social-login.php' ) ) {
            require_once plugin_dir_path(MOV_DIR) . 'woocommerce-social-login/includes/class-wc-social-login-provider-profile.php';
        }
    }


    
    function mo_wc_social_login_profile($profile,$provider_id)
    {

        MoUtility::initialize_transaction($this->_formSessionVar);
        MoPHPSessions::addSessionVar('wc_provider',$profile);
        $_SESSION['wc_provider_id'] = maybe_serialize($provider_id);
            return $profile;
    }


    
    function mo_wc_social_login($usermeta,$profile)
    {
        $this->sendChallenge(NULL,$usermeta['user_email'],NULL,NULL,'external',NULL,
            array('data'=>$usermeta,'message'=>MoMessages::showMessage(MoMessages::PHONE_VALIDATION_MSG),
            'form'=>'WC_SOCIAL','curl'=>MoUtility::currentPageUrl()));
    }


    
    function _handle_wc_create_user_action($postData)
    {

        if(!$this->checkIfVerificationNotStarted()
            && SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType())) {
            $this->create_new_wc_social_customer($postData);
        }
    }


    
    function create_new_wc_social_customer($userData)
    {
        require_once  plugin_dir_path(MOV_DIR) . 'woocommerce/includes/class-wc-emails.php';
        WC_Emails::init_transactional_emails();


        $auth = MoPHPSessions::getSessionVar('wc_provider');
        $provider_id = maybe_unserialize(sanitize_text_field($_SESSION['wc_provider_id']));
        $this->unsetOTPSessionVariables();
        $profile = new WC_Social_Login_Provider_Profile(  $provider_id,$auth );
        $phone = $userData['mo_phone_number'];
        $userData = array(
            'role'		=>'customer',
            'user_login' => $profile->has_email() ? sanitize_email( $profile->get_email() ) : $profile->get_nickname(),
            'user_email' => $profile->get_email(),
            'user_pass'  => wp_generate_password(),
            'first_name' => $profile->get_first_name(),
            'last_name'  => $profile->get_last_name(),
        );

        if ( empty( $userData['user_login'] ) )
            $userData['user_login'] = $userData['first_name'] . $userData['last_name'];

        $append     = 1;
        $o_username = $userData['user_login'];

        while ( username_exists( $userData['user_login'] ) ) {
            $userData['user_login'] = $o_username . $append;
            $append ++;
        }

        $customer_id = wp_insert_user( $userData );

        update_user_meta( $customer_id, 'billing_phone', MoUtility::processPhoneNumber($phone) );
        update_user_meta( $customer_id, 'telephone', MoUtility::processPhoneNumber($phone) );

        do_action( 'woocommerce_created_customer', $customer_id, $userData, false );

        $user = get_user_by( 'id', $customer_id );

        $profile->update_customer_profile( $user->ID, $user );

        if ( ! $message = apply_filters( 'wc_social_login_set_auth_cookie', '', $user ) ) {
            wc_set_customer_auth_cookie( $user->ID );
            update_user_meta( $user->ID, '_wc_social_login_' . $profile->get_provider_id() . '_login_timestamp', current_time( 'timestamp' ) );
            update_user_meta( $user->ID, '_wc_social_login_' . $profile->get_provider_id() . '_login_timestamp_gmt', time() );
            do_action( 'wc_social_login_user_authenticated', $user->ID, $profile->get_provider_id() );
        } else {
            wc_add_notice( $message, 'notice' );
        }

        if ( is_wp_error( $customer_id ) ) {
            $this->redirect( 'error', 0, $customer_id->get_error_code() );
        } else {
            $this->redirect( null, $customer_id );
        }
    }


    
    function redirect( $type = null, $user_id = 0, $error_code = 'wc-social-login-error' )
    {
        $user = get_user_by( 'id', $user_id );

        if ( MoUtility::isBlank( $user->user_email ) ) {
            $return_url = add_query_arg( 'wc-social-login-missing-email', 'true', wc_customer_edit_account_url() );
        } else {
            $return_url = get_transient( 'wcsl_' . md5( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) );
            $return_url = $return_url ? esc_url( urldecode( $return_url ) ) : wc_get_page_permalink( 'myaccount' );
            delete_transient( 'wcsl_' . md5( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) );
        }

        if ( 'error' === $type )
            $return_url = add_query_arg( $error_code, 'true', $return_url );

        wp_safe_redirect( esc_url_raw( $return_url ) );
        exit;
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        wp_send_json( MoUtility::createJson(
            MoUtility::_get_invalid_otp_method(),MoConstants::ERROR_JSON_TYPE
        ));
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
        wp_send_json( MoUtility::createJson(MoConstants::SUCCESS ,MoConstants::SUCCESS_JSON_TYPE) );
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId, $this->_formSessionVar]);
    }


    
    function _handle_wc_ajax_send_otp($data)
    {

        if(!$this->checkIfVerificationNotStarted()) {
            $this->sendChallenge('ajax_phone', '', null, trim($data['user_phone']), $this->_otpType, null, $data);
        }
    }


    
    function processOTPEntered($data)
    {

        if($this->checkIfVerificationNotStarted()) return;

        if($this->processPhoneNumber($data)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::PHONE_MISMATCH), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $this->validateChallenge($this->getVerificationType());
        }
    }


    
    function processPhoneNumber($data)
    {
        $phone = MoPHPSessions::getSessionVar('phone_number_mo');
        return strcmp($phone,MoUtility::processPhoneNumber($data['user_phone']))!=0;
    }


    
    function checkIfVerificationNotStarted()
    {

        return !SessionUtils::isOTPInitialized($this->_formSessionVar);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled()) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;
        $this->_isFormEnabled = $this->sanitizeFormPOST('wc_social_login_enable');
        update_mo_option('wc_social_login_enable', $this->_isFormEnabled);
    }
}