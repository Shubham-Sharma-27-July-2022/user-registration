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
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;
use \WP_Error;

class SocialLoginIntegration extends FormHandler implements IFormHandler{

    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::SOCIAL_LOGIN;
        $this->_phoneKey = 'phone';
        $this->_phoneFormId = "#phone_number_mo";
        $this->_formKey = 'SOCIAL_LOGIN';
        $this->_typePhoneTag = "mo_social_login_phone_enable";
        $this->_typeEmailTag = "mo_wp_default_email_enable";
        $this->_typeBothTag = 'mo_wp_default_both_enable';
        $this->_formName = mo_("miniOrange Social Login");
        $this->_isFormEnabled = get_mo_option('mo_social_login_enable');
        $this->_formDocuments = MoOTPDocs::SOCIAL_LOGIN;
        parent::__construct();
    }

    public function handleForm()
    {
        $this->_otpType = $this->_isFormEnabled ? $this->_typePhoneTag : '';
        add_action("mo_before_insert_user",[$this,"social_login_verification"],1,2);
        MoPHPSessions::checkSession();
        if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType())) {
            $this->unsetOTPSessionVariables();
            $data=$_POST;
            $random_password = wp_generate_password(10, false);
            $userdetails=MoPHPSessions::getSessionVar("userdetails");
            $cust_reg_val=MoPHPSessions::getSessionVar("cust_reg_val");
            $userdata = array(
                'user_login' => $userdetails['user_login'],
                'user_email' => $userdetails['user_email'],
                'user_pass' => $random_password,
                'display_name' => $userdetails['display_name'],
                'first_name' => $userdetails['first_name'],
                'last_name' => $userdetails['last_name'],
                'user_url' => $userdetails['user_url'],
                'phone' => sanitize_text_field($data['mo_phone_number']),
            );
            if(get_option('mo_openid_restricted_domains')=='mo_openid_restrict_domain') {
                $this->restricted_domain($userdata['user_email']);
            }else {
                $this->allowed_domain($userdata['user_email']);
            }

            $_SESSION['registered_user'] = '1';

            if(get_option('mo_openid_enable_registration_on_page') == '1') {
                $user_id=$this->mo_openid_check_registration_block($userdata);
            }
            else {
                $user_id = wp_insert_user( $userdata);
            }

            if($cust_reg_val!="")
                $this->update_custom_data($user_id,$cust_reg_val);

            if(get_option('mo_openid_user_moderation')==1)
            {
                add_user_meta($user_id, 'activation_state','1');
            }

            if(isset($_COOKIE['mo_openid_signup_url'])) {
                add_user_meta($user_id, 'registered_url', sanitize_text_field($_COOKIE["mo_openid_signup_url"]));
            }

            $user	= get_user_by('email', $userdata['user_email'] );
            if ( $user_id && !is_wp_error( $user_id )&& get_option("mo_openid_email_activation")==1) {
                $this->mo_send_activation_mail($user,$user_id);
                $this->mo_openid_insert_query($userdetails['social_app_name'], $userdetails['user_email'], $user_id, $userdetails['social_user_id'], $userdetails['user_picture']);
                exit;
            }

            if(is_wp_error( $user_id )) {
                print_r($user_id);
                wp_die("Error Code 5: ".get_option('mo_registration_error_message'));
            }

            update_option('mo_openid_user_count',get_option('mo_openid_user_count')+1);

            $session_values= array(
                'username' => sanitize_text_field($userdetails['user_login']),
                'user_email' => sanitize_email($userdetails['user_email']),
                'user_full_name' => sanitize_text_field($userdetails['display_name']),
                'first_name' => sanitize_text_field($userdetails['first_name']),
                'last_name' => sanitize_text_field($userdetails['last_name']),
                'user_url' => sanitize_text_field($userdetails['user_url']),
                'user_picture' => sanitize_text_field($userdetails['user_picture']),
                'social_app_name' => sanitize_text_field($userdetails['social_app_name']),
                'social_user_id' => sanitize_text_field($userdetails['social_user_id']),
            );

            $this->mo_openid_start_session_login($session_values);
            $user	= get_user_by('id', $user_id );
            update_user_meta($user_id, 'verified_number', sanitize_text_field($data['mo_phone_number']));
                        do_action( 'mo_user_register', $user_id,$userdetails['user_profile_url']);
            $this->mo_openid_paid_membership_pro_integration($user_id);
            $this->mo_openid_link_account($user->user_login, $user);
            global $wpdb;
            $db_prefix = $wpdb->prefix;
            $linked_email_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM " . $db_prefix . "mo_openid_linked_user where linked_social_app = \"%s\" AND identifier = %s", $userdetails['social_app_name'], $userdetails['social_user_id']));
            $this->mo_openid_login_user($linked_email_id,$user_id,$user,$userdetails['user_picture'],0);
        }

        $this->routeData();
    }

    function restricted_domain($user_email){
        $allowed = false;
        $restricted_domain = get_option("mo_openid_restricted_domains_name");

        if(empty($restricted_domain) || empty($user_email)){
            return;
        }
        $email = explode(';', $restricted_domain);
        foreach($email as $value){
            $data = explode('@',$user_email);
            $user_domain = isset($data[1]) ? $data[1]:'';

            if($value == $user_domain){
                $allowed = true;
                break;
            }
        }
        if($allowed){
            wp_die('Permission denied. You are not allowed to register. Please contact the administrator. Click <a href="'.get_site_url().'">here</a> to go back to the website.');
        }
    }

    function allowed_domain($user_email){
        $allowed = false;
        $restricted_domain = get_option("mo_openid_allowed_domains_name");

        if(empty($restricted_domain) || empty($user_email)){
            return;
        }
        $email = explode(';', $restricted_domain);

        foreach($email as $value){
            $data = explode('@',$user_email);
            $user_domain = isset($data[1]) ? $data[1]:'';

            if($value == $user_domain){
                $allowed = true;
                break;
            }
        }

        if(!$allowed){
            wp_die('Permission denied. You are not allowed to register. Please contact the administrator. Click <a href="'.get_site_url().'">here</a> to go back to the website.');
        }
    }

    function mo_openid_check_registration_block($userdata){
        $registration_urls=explode(";",get_option('mo_openid_registration_page_urls'));
        foreach($registration_urls as $val){
            if(strpos($_COOKIE["mo_openid_signup_url"],$val) !== false)
            {
                $user_id = wp_insert_user($userdata);
                return $user_id;
            }
        }
        wp_redirect(get_option('mo_openid_block_registration_redirect_url'));
        exit;
    }

    function mo_openid_start_session_login($session_values){
        mo_openid_start_session();
        $_SESSION['mo_login'] = true;
        $_SESSION['username'] = isset($session_values['username']) ? $session_values['username'] : '';
        $_SESSION['user_email'] = isset($session_values['user_email']) ? $session_values['user_email'] : '';
        $_SESSION['user_full_name'] = isset($session_values['user_full_name']) ? $session_values['user_full_name'] : '';
        $_SESSION['first_name'] = isset($session_values['first_name']) ? $session_values['first_name'] : '';
        $_SESSION['last_name'] = isset($session_values['last_name']) ? $session_values['last_name'] : '';
        $_SESSION['user_url'] = isset($session_values['user_url']) ? $session_values['user_url'] : '';
        $_SESSION['user_picture'] = isset($session_values['user_picture']) ? $session_values['user_picture'] : '';
        $_SESSION['social_app_name'] = isset($session_values['social_app_name']) ? $session_values['social_app_name'] : '';
        $_SESSION['social_user_id'] = isset($session_values['social_user_id']) ? $session_values['social_user_id'] : '';
    }

    function mo_openid_paid_membership_pro_integration($user_id){
        global $wpdb;
        if(get_option('mo_openid_paid_memb_default') == 1 )
        {
            global $wpdb;
            $db_prefix = $wpdb->prefix;
            $id = $wpdb->get_var("SELECT COUNT(*) FROM wp_pmpro_memberships_users ");
            $id=$id+1;
            $membership_id=get_option('mo_openid_paid_memb_default_opt');
            $c_time=date("Y-m-d H:i:s");
            $sql = "insert into ".$db_prefix."memberships_users values ($id, $user_id, $membership_id, 0, 0.00, 0.00, 0, '', 0, 0.00, 0, 'active', '$c_time', '0000-00-00 00:00:00', '$c_time')";
            $s=$wpdb->query($sql);
            if($s === false){
                $wpdb->show_errors();
                $wpdb->print_error();
                wp_die('Error in insert Query');
                exit;
            }
        }
        if(get_option('mo_openid_paid_memb_choose') == 1 )
        {
            update_user_meta($user_id,"chosen_membership",0);
        }
    }

    function mo_openid_link_account( $username, $user ){

        if($user){
            $userid = $user->ID;
        }
        mo_openid_start_session();
        $user_email =  isset($_SESSION['user_email']) ? sanitize_text_field($_SESSION['user_email']):'';
        $social_app_identifier = isset($_SESSION['social_user_id']) ? sanitize_text_field($_SESSION['social_user_id']):'';
        $social_app_name = isset($_SESSION['social_app_name']) ? sanitize_text_field($_SESSION['social_app_name']):'';
                if(isset($userid) && empty($social_app_identifier) && empty($social_app_name) ) {
            return;
        }
        elseif(!isset($userid)){
            return;
        }
        global $wpdb;
        $db_prefix = $wpdb->prefix;
        $linked_email_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM ".$db_prefix."mo_openid_linked_user where linked_email = \"%s\" AND linked_social_app = \"%s\"",$user_email,$social_app_name));
                if(!isset($linked_email_id)){
            $this->mo_openid_insert_query($social_app_name,$user_email,$userid,$social_app_identifier);
        }
    }

    function mo_openid_login_user($linked_email_id,$user_id,$user,$user_picture,$user_mod_msg){
        if (get_option('moopenid_social_login_avatar') && isset($user_picture))
            update_user_meta($user_id, 'moopenid_user_avatar', $user_picture);
                        if(get_option("mo_openid_email_activation")==1){
            mo_verify_activated_user($user,$user->ID);
            exit;
        }
        if (get_option("mo_openid_user_moderation") == 1) {
            $x = get_user_meta($linked_email_id, 'activation_state');
            if ($x[0] != '1') {
                $this->mo_openid_paid_membership_pro_integration($user_id);
                do_action('wp_login', $user->user_login, $user);
            } else {
                $this->mo_openid_paid_membership_pro_integration($user_id);
                $this->mo_openid_link_account($user->user_login, $user);
                ?>
                <script>
                    var pop_up = '<?php echo get_option('mo_openid_popup_window');?>';
                    if (pop_up== '0') {
                        alert("Successfully registered! You will get notification after activation of your account.");
                        window.location = "<?php  echo get_option('siteurl');?>";
                    } else {
                        alert("Successfully registered! You will get notification after activation of your account.");
                        window.close();
                    }
                </script>
                <?php
                exit();
            }
        }
        else
            $this->mo_openid_paid_membership_pro_integration($user_id);
        do_action( 'wp_login', $user->user_login, $user );
        wp_set_auth_cookie( $user_id, true );
        $redirect_url = mo_openid_get_redirect_url();
        wp_redirect($redirect_url);
        exit;
    }

    function mo_openid_insert_query($social_app_name,$user_email,$userid,$social_app_identifier){
        
        if(!empty($social_app_name) && !empty($user_email) && !empty($userid) && !empty($social_app_identifier)){

            date_default_timezone_set('Asia/Kolkata');
            $date = date('Y-m-d H:i:s');

            global $wpdb;
            $db_prefix = $wpdb->prefix;
            $table_name = $db_prefix. 'mo_openid_linked_user';

            $result = $wpdb->insert(
                $table_name,
                array(
                    'linked_social_app' => $social_app_name,
                    'linked_email' => $user_email,
                    'user_id' =>  $userid,
                    'identifier' => $social_app_identifier,
                    'timestamp' => $date,
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%s',
                    '%s'
                )
            );
            if($result === false){
                
                wp_die('Error in insert query');
            }
        }
    }

    function mo_send_activation_mail($user,$user_id){
        update_user_meta($user_id,'mo_user_status','0');
        $redirectURL = wp_login_url();
        $to = $user->user_email;
        $websitename = get_option('siteurl');

        $act_code=base64_encode($user_id . time());
        $subject = "Please Verify your account";
        $user_id_encoded = base64_encode( $user_id );
        
        $replace = "<html><body>
            <a href= $redirectURL?uid=$user_id_encoded&act_code=$act_code>VERIFY YOUR ACCOUNT </a><br><br><br>
             </body></html>";

        $msg = get_option('mo_openid_activation_email_message');
        $msg = str_replace('##activation_link##', $replace, $msg);
        $msg = str_replace('##website_name##', $websitename, $msg);

        $headers = "Content-Type: text/html";
        wp_mail( $to, $subject, $msg,$headers);
        update_user_meta($user_id,'activation_code',$act_code);
        ?>
        <script>
            var pop_up = '<?php echo get_option('mo_openid_popup_window');?>';
            var redirect_home =  '<?php echo get_option('mo_openid_activation_page_urls');?>';
            if (pop_up=='0') {
                window.location = redirect_home;
            }else {
                window.close();
            }
        </script>
        <?php
                do_action( 'mo_user_register', $user_id,$user->user_profile_url);
        do_action( 'miniorange_collect_attributes_for_authenticated_user', $user, mo_openid_get_redirect_url());

    }

    function update_custom_data($user_id,$cust_reg_val)
    {
        foreach ($cust_reg_val as $x)
            foreach ($x as $field => $res)
                update_user_meta($user_id, $field, $res);
    }

    function routeData()
    {
        if(!array_key_exists('option', $_REQUEST)) return;

        switch (trim($_REQUEST['option']))
        {
            case "miniorange-ajax-otp-generate":
                $this->_handle_social_login_ajax_send_otp();				break;
            case "miniorange-ajax-otp-validate":
                $this->_handle_social_login_ajax_form_validate_action();	break;
        }
    }

    function _handle_social_login_ajax_send_otp()
    {
        $data = $_POST;
        MoPHPSessions::checkSession();
        MoUtility::initialize_transaction($this->_formSessionVar);
        if(SessionUtils::isOTPInitialized($this->_formSessionVar)) {
            $this->sendChallenge('ajax_phone', '', null, trim(sanitize_text_field($data['user_phone'])), VerificationType::PHONE, sanitize_text_field($data['user_pass']), $data);
        }
    }

    function _handle_social_login_ajax_form_validate_action()
    {
        $data = $_POST;
        MoPHPSessions::checkSession();
        $phone = MoPHPSessions::getSessionVar('phone_number_mo');
        if (strcmp($phone, MoUtility::processPhoneNumber(sanitize_text_field($data['user_phone'])))) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::PHONE_MISMATCH), MoConstants::ERROR_JSON_TYPE)
            );
        }else {
            $this->validateChallenge($this->getVerificationType(),NULL,sanitize_text_field($data['mo_otp_token']));
            if(SessionUtils::isStatusMatch($this->_formSessionVar,self::VALIDATED,$this->getVerificationType())) {
                wp_send_json(MoUtility::createJson(
                    MoConstants::SUCCESS_JSON_TYPE, MoConstants::SUCCESS_JSON_TYPE
                ));
            }else{
                wp_send_json(MoUtility::createJson(
                    MoMessages::showMessage(MoMessages::INVALID_OTP), MoConstants::ERROR_JSON_TYPE
                ));
            }
        }
    }

    function isPhoneVerificationEnabled()
    {
        $otpType = $this->getVerificationType();
        return $otpType===VerificationType::PHONE || $otpType===VerificationType::BOTH;
    }

    function social_login_verification($userdetails,$cust_reg_val){
        MoUtility::initialize_transaction($this->_formSessionVar);
        MoPHPSessions::addSessionVar("cust_reg_val",$cust_reg_val);
        MoPHPSessions::addSessionVar("userdetails",$userdetails);
        $this->sendChallenge(
            NULL,null,NULL,NULL,
            'external',$userdetails['user_pass'], [
                'data'=>$userdetails['user_pass'],
                'message'=>MoMessages::showMessage(MoMessages::REGISTER_PHONE_LOGIN),
                'form'=>$this->_phoneKey,'curl'=>MoUtility::currentPageUrl()
            ]
        );

    }

    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId,$this->_formSessionVar]);

    }

    public function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {
        MoPHPSessions::checkSession();
        SessionUtils::addStatus($this->_formSessionVar,self::VALIDATED,$otpType);
    }

    public function handle_failed_verification($user_login,$user_email,$phone_number, $otpType)
    {
        MoPHPSessions::checkSession();
        $otpVerType = $this->getVerificationType();
        $fromBoth = $otpVerType===VerificationType::BOTH ? TRUE : FALSE;
        miniorange_site_otp_validation_form(
            $user_login,$user_email,$phone_number,MoUtility::_get_invalid_otp_method(),$otpVerType,$fromBoth
        );
    }



    public function getPhoneNumberSelector($selector)
    {
        MoPHPSessions::checkSession();
        if($this->isFormEnabled() && $this->isPhoneVerificationEnabled()) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;

    }

    public function handleFormOptions()
    {

        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('mo_social_login_enable');

        update_mo_option('mo_social_login_enable', $this->_isFormEnabled);
    }
}

?>