<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoPHPSessions;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;


class RealEstate7 extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formKey = 'REAL_ESTATE_7';
        $this->_formSessionVar = FormSessionVars::REALESTATE_7;
        $this->_isFormEnabled = get_mo_option('realestate_enable');
        $this->_typePhoneTag = "mo_realestate_contact_phone_enable";
        $this->_typeEmailTag = "mo_realestate_contact_email_enable";
        $this->_formName = mo_("Real Estate 7 Pro Theme");
        parent::__construct();

    }

    

    public function handleForm()
    {   
        $this->_phoneFormId = "#mo_ct_user_phone";
        $this->_generateOTPAction = "miniorange-real-estate-7-send-otp";
        $this->_validateOTPAction = "miniorange-real-estate-7-verify-code";
        $this->_otpType= get_mo_option('realestate_otp_type');
        $this->_formDocuments = MoOTPDocs::REALESTATE7_THEME_LINK;
        $this->_buttonText    = $this->setButtonText();

        add_action('wp_enqueue_scripts', array($this,'addPhoneFieldScript'));

        add_action("wp_ajax_{$this->_generateOTPAction}", [$this,'_send_otp']);
        add_action("wp_ajax_nopriv_{$this->_generateOTPAction}", [$this,'_send_otp']);
        add_action("wp_ajax_{$this->_validateOTPAction}", [$this,'processFormAndValidateOTP']);
        add_action("wp_ajax_nopriv_{$this->_validateOTPAction}", [$this,'processFormAndValidateOTP']);
        $this->_formDetails=[ 'ct_registration_form'     => ['phonekey' => 'mo_ct_user_phone',
                                                             'emailkey'=> 'ct_user_email']];
        if(!array_key_exists('option',$_POST))  return;

        switch(trim($_POST['option']))
        {
            case 'realestate_register':
                $this->_sanitizeAndRouteData($_POST);   break;
        }
    
          if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType())){

            $this->unsetOTPSessionVariables();
            return;


        }
       
   }

function setButtonText() {
        if(strcasecmp(get_mo_option('realestate_otp_type'),$this->_typePhoneTag)==0){
            return mo_("Send OTP to Phone");
        }
        else{
            return mo_("Send OTP to Email");
        }
    }

function _sanitizeAndRouteData($data)
{   
        $id = key($this->_formDetails);
        if(!array_key_exists($id,$this->_formDetails)) return;

        if (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0 )
        {
           
        $this->_processPhone(sanitize_text_field($data['mo_ct_user_phone']));
        }  
        if (strcasecmp($this->_otpType,$this->_typeEmailTag)==0 || strcasecmp($this->_otpType,$this->_typeBothTag)==0 )
        {  

        $this->_processEmail(sanitize_email($data['ct_user_email']));
        }
}
      function _send_otp()
    {

        MoUtility::initialize_transaction($this->_formSessionVar);
    
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $this->_processPhoneAndSendOTP($_POST);
        else
            $this->_processEmailAndSendOTP($_POST);
    }


    
    private function _processEmailAndSendOTP($data)
    {
        if(!MoUtility::sanitizeCheck('user_email',$data)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_EMAIL), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $user_email = sanitize_email($data['user_email']);
            SessionUtils::addEmailVerified($this->_formSessionVar,$user_email);
            $this->sendChallenge('',$user_email,NULL,NULL,VerificationType::EMAIL);
        }
    }


    
    private function _processPhoneAndSendOTP($data)
    {
        if(!MoUtility::sanitizeCheck('user_phone',$data)) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_PHONE), MoConstants::ERROR_JSON_TYPE
            ));
        } else {
            $user_phone = sanitize_text_field($data['user_phone']);
            SessionUtils::addPhoneVerified($this->_formSessionVar,$user_phone);
            $this->sendChallenge('',NULL,NULL,$user_phone,VerificationType::PHONE);
        }
    }

    function processFormAndValidateOTP()
    {
        $this->validateAjaxRequest();
        $this->checkIfOTPSent();
        $this->checkIntegrityAndValidateOTP($_POST);
    }

    function checkIfOTPSent()
    {   

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) {

            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }

    private function checkIntegrityAndValidateOTP($data)
    {
   
        $this->checkIntegrity($data);
    
        $this->validateChallenge(sanitize_text_field($data['otpType']),NULL,sanitize_text_field($data['otp_token']));
       
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,sanitize_text_field($data['otpType']))) {
        
            wp_send_json(MoUtility::createJson(
                    MoConstants::SUCCESS_JSON_TYPE, MoConstants::SUCCESS_JSON_TYPE
            ));
        }else{
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::INVALID_OTP), MoConstants::ERROR_JSON_TYPE
            ));
        }
    }

    
    private function checkIntegrity($data)
    {
        if(sanitize_text_field($data['otpType'])==='phone' ) {
            if(!SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,sanitize_text_field(sanitize_text_field($data['user_phone'])))) {
                wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::PHONE_MISMATCH), MoConstants::ERROR_JSON_TYPE
                ));
            }
        } else if(!SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,sanitize_email(sanitize_email($data['user_email'])))) {
            wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::EMAIL_MISMATCH), MoConstants::ERROR_JSON_TYPE
            ));
              
        }
    }

    public function unsetOTPSessionVariables()
    {
        Sessionutils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);
    }

    
    public function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {
        
        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }


    
    public function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {
        SessionUtils::addStatus($this->_formSessionVar,self::VERIFICATION_FAILED,$otpType);
    }

    

    
    private function _processPhone($phone)
    {
  
        if(!SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,VerificationType::PHONE))
            ct_errors()->add('Please Validate', __(MoMessages::showMessage(MoMessages::PLEASE_VALIDATE), 'contempo'));
        if(!SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,sanitize_text_field($phone)))
             ct_errors()->add('Please Validate', __(MoMessages::showMessage(MoMessages::PHONE_MISMATCH), 'contempo'));
    }


    

    private function _processEmail($email)
    {
        if(!SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,VerificationType::EMAIL))
            ct_errors()->add('Please Validate', __(MoMessages::showMessage(MoMessages::PLEASE_VALIDATE), 'contempo'));
        if(!SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,sanitize_text_field($email)))
            ct_errors()->add('Please Validate', __(MoMessages::showMessage(MoMessages::EMAIL_MISMATCH), 'contempo'));
    }


    
    public function addPhoneFieldScript()
    {  
        wp_register_script('realEstate7Script', MOV_URL . 'includes/js/realEstate7Script.min.js?version='.MOV_VERSION , array('jquery'));
        wp_localize_script( 'realEstate7Script', 'realEstate7Script', array(
            'siteURL'       =>  wp_ajax_url(),
            'otpType'       =>  $this->ajaxProcessingFields(),
            'formDetails'   =>  $this->_formDetails,
            'buttontext'    =>  $this->_buttonText,
            'validated'     =>  $this->getSessionDetails(),
            'imgURL'        =>  MOV_LOADER_URL,
            'fieldText'     =>  mo_('Enter OTP here'),
            'gnonce'        =>  wp_create_nonce($this->_nonce),
            'nonceKey'      =>  wp_create_nonce($this->_nonceKey),
            'vnonce'        =>  wp_create_nonce($this->_nonce),
            'gaction'       =>  $this->_generateOTPAction,
            'vaction'       =>  $this->_validateOTPAction
             ) );
        wp_enqueue_script( 'realEstate7Script' );
    }


    function getSessionDetails()
    {
        return [
            VerificationType::EMAIL => SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,VerificationType::EMAIL),
            VerificationType::PHONE => SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,VerificationType::PHONE)
        ];
    }

    public function getPhoneNumberSelector($selector)
    {

        if(self::isFormEnabled() && $this->_otpType==$this->_typePhoneTag) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    public function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('realestate_enable');
        $this->_otpType = $this->sanitizeFormPOST('realestate_contact_type');

        update_mo_option('realestate_enable',$this->_isFormEnabled);
        update_mo_option('realestate_otp_type',$this->_otpType);
    }
}
