<?php

namespace OTP\Handler;
if(! defined( 'ABSPATH' )) exit;
use OTP\Helper\FormSessionVars;
use OTP\Helper\GatewayFunctions;
use OTP\Helper\MoMessages;
use OTP\Helper\MoPHPSessions;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\BaseActionHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;

class FormActionHandler extends BaseActionHandler
{
    use Instance;

    function __construct()
    {
        parent::__construct();
        $this->_nonce = 'mo_form_actions';
        add_action('init', array($this, 'handleFormActions'), 1);
        add_action('mo_validate_otp', array($this,'validateOTP'), 1, 3);
        add_action('mo_generate_otp', array($this,'challenge'), 2, 8);
        add_filter('mo_filter_phone_before_api_call', array($this, 'filterPhone'), 1, 1);
    }

    
    public function challenge($user_login, $user_email, $errors, $phone_number = null,
                              $otp_type="email", $password = "", $extra_data = null, $from_both = false)
    {

        $phone_number = MoUtility::processPhoneNumber($phone_number);
        MoPHPSessions::addSessionVar('current_url' , MoUtility::currentPageUrl());
        MoPHPSessions::addSessionVar('user_email' , $user_email);
        MoPHPSessions::addSessionVar('user_login' , $user_login);
        MoPHPSessions::addSessionVar('user_password' , $password);
        MoPHPSessions::addSessionVar('phone_number_mo' , $phone_number);
        MoPHPSessions::addSessionVar('extra_data' , $extra_data);
        $this->handleOTPAction($user_login, $user_email, $phone_number, $otp_type, $from_both, $extra_data);
    }


    
    private function handleResendOTP($otp_type, $from_both)
    {

        $user_email = MoPHPSessions::getSessionVar('user_email');
        $user_login = MoPHPSessions::getSessionVar('user_login');
        $phone_number = MoPHPSessions::getSessionVar('phone_number_mo');
        $extra_data = MoPHPSessions::getSessionVar('extra_data');
        $this->handleOTPAction($user_login, $user_email, $phone_number, $otp_type, $from_both, $extra_data);
    }


    
    function handleOTPAction($user_login, $user_email, $phone_number, $otp_type, $from_both, $extra_data)
    {
        
        
        global $phoneLogic, $emailLogic;
        switch ($otp_type)
        {
            case VerificationType::PHONE:
                $phoneLogic->_handle_logic($user_login, $user_email, $phone_number, $otp_type, $from_both);     break;
            case VerificationType::EMAIL:
                $emailLogic->_handle_logic($user_login, $user_email, $phone_number, $otp_type, $from_both);     break;
            case VerificationType::BOTH:
                miniorange_verification_user_choice($user_login, $user_email, $phone_number,
                    MoMessages::showMessage(MoMessages::CHOOSE_METHOD), $otp_type);                             break;
            case VerificationType::EXTERNAL:
                mo_external_phone_validation_form($extra_data['curl'], $user_email,
                    $extra_data['message'], $extra_data['form'], $extra_data['data']);                          break;
        }
    }


    
    function handleGoBackAction()
    {

        $url = MoPHPSessions::getSessionVar('current_url');
        do_action('unset_session_variable');
        header("location:" . $url);
    }


    
    function validateOTP($otpType, $requestVar, $otp)
    {
        $user_login = MoPHPSessions::getSessionVar('user_login');
        $user_email = MoPHPSessions::getSessionVar('user_email');
        $phone_number = MoPHPSessions::getSessionVar('phone_number_mo');
        $password = MoPHPSessions::getSessionVar('user_password');
        $extra_data = MoPHPSessions::getSessionVar('extra_data');

        $txID = Sessionutils::getTransactionId($otpType);
        $token = MoUtility::sanitizeCheck($requestVar,$_REQUEST);
        $token = !$token ? $otp : $token;
        if (!is_null($txID)) {
            
            $gateway = GatewayFunctions::instance();
            $content = $gateway->mo_validate_otp_token($txID,$token);
            switch ($content['status'])
            {
                case 'SUCCESS':
                    $this->onValidationSuccess($user_login, $user_email, $password, $phone_number, $extra_data, $otpType); break;
                default:
                    $this->onValidationFailed($user_login, $user_email, $phone_number, $otpType); break;
            }
        }
    }


    
    private function onValidationSuccess($user_login, $user_email, $password, $phone_number, $extra_data, $otpType)
    {
        $redirect_to = array_key_exists('redirect_to', $_POST) ? sanitize_text_field($_POST['redirect_to']) : '';
        do_action('otp_verification_successful', $redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data, $otpType);
    }


    
    private function onValidationFailed($user_login, $user_email, $phone_number, $otpType)
    {
        do_action('otp_verification_failed', $user_login, $user_email, $phone_number, $otpType);
    }


    
    private function handleOTPChoice($postData)
    {

        $userLogin = MoPHPSessions::getSessionVar('user_login');
        $userEmail = MoPHPSessions::getSessionVar('user_email');
        $userPhone = MoPHPSessions::getSessionVar('phone_number_mo');
        $userPass = MoPHPSessions::getSessionVar('user_password');
        $extraData = MoPHPSessions::getSessionVar('extra_data');

        $otpVerType = strcasecmp($postData['mo_customer_validation_otp_choice'], 'user_email_verification') == 0
            ? VerificationType::EMAIL : VerificationType::PHONE;

        $this->challenge($userLogin,$userEmail,null,$userPhone,$otpVerType,$userPass,$extraData,true);
    }


    
    function filterPhone($phone)
    {
        return str_replace("+", "", $phone);
    }


    
    function handleFormActions()
    {
        if (array_key_exists('option', $_REQUEST)) {

            $from_both = MoUtility::sanitizeCheck('from_both',$_POST);
            $otpType = MoUtility::sanitizeCheck('otp_type',$_POST);

            switch (trim($_REQUEST['option'])) {
                case "validation_goBack":
                    $this->handleGoBackAction();                            break;
                case "miniorange-validate-otp-form":
                    $this->validateOTP($otpType,"mo_otp_token",null);       break;
                case "verification_resend_otp":
                    $this->handleResendOTP($otpType, $from_both);           break;
                case "miniorange-validate-otp-choice-form":
                    $this->handleOTPChoice($_POST);                         break;
            }
        }
    }
}