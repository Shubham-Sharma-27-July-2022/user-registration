<?php

namespace OTP\Handler\Forms;

use OTP\Helper\FormSessionVars;
use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoOTPDocs;
use OTP\Helper\MoPHPSessions;
use OTP\Helper\MoUtility;
use OTP\Helper\SessionUtils;
use OTP\Objects\BaseMessages;
use OTP\Objects\FormHandler;
use OTP\Objects\IFormHandler;
use OTP\Objects\VerificationType;
use OTP\Traits\Instance;
use ReflectionException;
use WC_Checkout;
use WP_Error;


class WooCommerceCheckOutForm extends FormHandler implements IFormHandler
{
    use Instance;

    
    private $_guestCheckOutOnly;

    
    private $_showButton;

    
    private $_popupEnabled;

    
    private $_paymentMethods;

    
    private $_selectivePayment;

    
    private $_disableAutoLogin;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::WC_CHECKOUT;
        $this->_typePhoneTag = 'mo_wc_phone_enable';
        $this->_typeEmailTag = 'mo_wc_email_enable';
        $this->_phoneFormId = 'input[name=billing_phone]';
        $this->_formKey = 'WC_CHECKOUT_FORM';
        $this->_formName = mo_("Woocommerce Checkout Form");
        $this->_isFormEnabled = get_mo_option('wc_checkout_enable');
        $this->_buttonText = get_mo_option('wc_checkout_button_link_text');
        $this->_buttonText = !MoUtility::isBlank($this->_buttonText) ? $this->_buttonText
                             : (!$this->_popupEnabled ? mo_("Verify Your Purchase") : mo_("Place Order"));
        $this->_formDocuments = MoOTPDocs::WC_CHECKOUT_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        if(!function_exists('is_plugin_active')) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) return;

        $this->_disableAutoLogin = get_mo_option('wc_checkout_disable_auto_login');
        $this->_paymentMethods = maybe_unserialize(get_mo_option('wc_checkout_payment_type'));
        $this->_paymentMethods = $this->_paymentMethods ? $this->_paymentMethods : WC()->payment_gateways->payment_gateways();
        $this->_popupEnabled = get_mo_option('wc_checkout_popup');
        $this->_guestCheckOutOnly= get_mo_option('wc_checkout_guest');
        $this->_showButton = get_mo_option('wc_checkout_button');
        $this->_otpType = get_mo_option('wc_checkout_type');
        $this->_selectivePayment = get_mo_option('wc_checkout_selective_payment');
        $this->_restrictDuplicates = get_mo_option('wc_checkout_restrict_duplicates');

        if($this->_popupEnabled) {
            add_action( 'woocommerce_after_checkout_billing_form' , array($this,'add_custom_popup')     ,99		);
            add_action( 'woocommerce_review_order_after_submit'   , array($this,'add_custom_button')	, 1, 1	);
        } else {
            add_action( 'woocommerce_after_checkout_billing_form' , array($this,'my_custom_checkout_field'), 99	);
        }

        
        if($this->_disableAutoLogin) {
            add_action('woocommerce_thankyou', array($this, 'disable_auto_login_after_checkout'), 1, 1);
        }

        add_filter( 'woocommerce_checkout_posted_data', array($this,'billing_phone_process'),99,1 );
        add_action( 'wp_enqueue_scripts', array($this,'enqueue_script_on_page'));
        add_action( 'woocommerce_after_checkout_validation', array($this,'my_custom_checkout_field_process') ,99,2);
        $this->routeData();
    }

    
    function billing_phone_process($data)
    {
        $data['billing_phone'] = MoUtility::processPhoneNumber($data['billing_phone']);
        return $data;
    }

    
    function disable_auto_login_after_checkout($order)
    {
        if(is_user_logged_in()) {
            wp_logout();
            wp_safe_redirect($_SERVER['REQUEST_URI']);
            exit();
        }
    }

    
    function routeData()
    {
        if(!array_key_exists('option', $_GET)) return;
        if(strcasecmp(sanitize_text_field(trim($_GET['option'])),'miniorange-woocommerce-checkout') == 0) {
            $this->handle_woocommerce_checkout_form($_POST);
        }
    }


    
    function handle_woocommerce_checkout_form($getdata)
    {

        MoUtility::initialize_transaction($this->_formSessionVar);
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0) {
            $this->checkPhoneValidity($getdata);
            $this->sendChallenge(
                'test', sanitize_email($getdata['user_email']), null, sanitize_text_field(trim($getdata['user_phone'])), VerificationType::PHONE
            );
        } else {
            $this->sendChallenge(
                'test', sanitize_email($getdata['user_email']), null, null, VerificationType::EMAIL
            );
        }
    }


    
    private function checkPhoneValidity($getData)
    {
        if ($this->isPhoneNumberAlreadyInUse(sanitize_text_field($getData['user_phone'])) && $this->_restrictDuplicates ) {
            wp_send_json(MoUtility::createJson(
                MoMessages::showMessage(MoMessages::PHONE_EXISTS), MoConstants::ERROR_JSON_TYPE
            ));
            exit;
        }
    }


    
    private function isPhoneNumberAlreadyInUse($phone)
    {
        global $wpdb;
        $phone = MoUtility::processPhoneNumber($phone);
        $key = 'billing_phone';
        $current_userID = wp_get_current_user()->ID;
        $results = $wpdb->get_row("SELECT `user_id` FROM `{$wpdb->prefix}usermeta` WHERE `meta_key` = '$key' AND `meta_value` =  '$phone'");
        return MoUtility::isBlank($results) ? FALSE : $results->user_id != $current_userID;
    }


    
    function checkIfVerificationNotStarted()
    {

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)){
            wc_add_notice(  MoMessages::showMessage(MoMessages::ENTER_VERIFY_CODE), MoConstants::ERROR_JSON_TYPE );
            return TRUE;
        }
        return FALSE;
    }


    
    function checkIfVerificationCodeNotEntered()
    {
        if(array_key_exists('order_verify', $_POST) && !MoUtility::isBlank(sanitize_text_field($_POST['order_verify']))) return FALSE;

        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            wc_add_notice(  MoMessages::showMessage(MoMessages::ENTER_PHONE_CODE), MoConstants::ERROR_JSON_TYPE );
        else
            wc_add_notice(  MoMessages::showMessage(MoMessages::ENTER_EMAIL_CODE), MoConstants::ERROR_JSON_TYPE );
        return TRUE;
    }


    
    function add_custom_button($order_id)
    {
        if($this->_guestCheckOutOnly && is_user_logged_in())  return;
        $this->show_validation_button_or_text();
        $this->common_button_or_link_enable_disable_script();
        echo ',$mo("#miniorange_otp_token_submit").click(function(o){
                    if($mo("#mo_message").length == 0)
                    {
                    $mo("<div id=\"mo_message\"></div>").insertBefore("#mo_validate_field");
                    }
                    var requiredFields = areAllMandotryFieldsFilled(),
                    e=$mo("input[name=billing_email]").val(),
                    m=$mo("#billing_phone").val(),
                    a=$mo("div.woocommerce");
                    if(requiredFields=="")
                    {
                        a.addClass("processing").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});
                        $mo.ajax({
                            url:"'.site_url().'/?option=miniorange-woocommerce-checkout",type:"POST",
                            data:{user_email:e,user_phone:m},crossDomain:!0,dataType:"json",
                            success:function(o){
                                "success"==o.result?(
                                    $mo(".blockUI").hide(),$mo("#mo_message").empty(),
                                    $mo("#mo_message").append(o.message).show(),
                                    $mo("#mo_message").addClass("woocommerce-message").removeClass("woocommerce-error"),
                                    //$mo("#myModal .modal-content").append(popupTemplate),
                                    $mo("#myModal").show(),$mo("#mo_validate_field").show()):($mo(".blockUI").hide(),$mo("#mo_message").empty(),
                                    $mo("#mo_message").append(o.message),$mo("#mo_message").addClass("woocommerce-error"),
                                    $mo("#mo_validate_field").hide(),$mo("#myModal").show()
                                )
                            },
                            error:function(o,e,m){}
                        });
                    }else{
                        $mo(".woocommerce-NoticeGroup-checkout").empty();
                        $mo("form.woocommerce-checkout").prepend(requiredFields);
                        $mo("html, body").animate({scrollTop: $mo(".woocommerce-error").offset().top}, 2000);
                    }
                    o.preventDefault()});
                    $mo("#miniorange_otp_validate_submit").click(function(o){$mo("#myModal").hide(),$mo(\'form[name="checkout"]\').submit()}),
                    $mo(".close").click(function(){$mo(".modal").hide();});});';

        echo 'function areAllMandotryFieldsFilled(){
                var err = \'<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">\'+
                                \'<ul class="woocommerce-error" role="alert">{{errors}}</ul>\'+
                         \'</div>\';
                var errors = "";
                $mo(".validate-required").each(function(){
                    var id = $mo(this).attr("id");
                    if(id!=undefined){
                        var n = id.replace("_field","");
                        if(n!="") {
                            var val = $mo("#"+n).val();
                            if((val=="" || val=="select") && checkOptionalMandatoryFields(n) ) {
                                $mo("#"+n).addClass("woocommerce-invalid woocommerce-invalid-required-field");
                                errors  += "<li><strong>"+
                                                $mo("#"+n+"_field").children("label").text().replace("*","")+
                                                "</strong> is a required field."+
                                            "</li>";
                            }
                        }
                    }
                });
                return errors != "" ? err.replace("{{errors}}",errors) : 0;
            }
            function checkOptionalMandatoryFields(n){
                var optional = { "shipping": { "fields": [ "shipping_first_name","shipping_last_name","shipping_postcode","shipping_address_1","shipping_address_2","shipping_city","shipping_state"],"checkbox": "ship-to-different-address-checkbox"},"account": {"fields": ["account_password","account_username"],"checkbox": "createaccount"}};
                if(n.indexOf("shipping") != -1){
                   return check_fields(n,optional["shipping"]);
                }else if(n.indexOf("account") != -1){
                   return check_fields(n,optional["account"]);
                }
                return true;
            }
            function check_fields(n,data){
                if($mo.inArray(n,data["fields"]) == -1 || ($mo.inArray(n,data["fields"]) > -1 &&
                        $mo("#"+data[\'checkbox\']+":checked").length > 0)) {
                    return true;
                }
                return false;
            }</script>';
    }


    
    function add_custom_popup()
    {
        if($this->_guestCheckOutOnly && is_user_logged_in())  return;
        echo '<style>@media only screen and (max-width: 800px) {.modal-content {width: 90% !important;}.modal-header .close{margin-left: 80% !important;}}.modal{display:none;position:fixed;z-index:1;padding-top:100px;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgb(0,0,0);background-color:rgba(0,0,0,0.4);}.modal-content{position:relative;background-color:#fefefe;margin:auto;padding:0;border:1px solid #888;width:40%;box-shadow:04px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);-webkit-animation-name:animatetop;-webkit-animation-duration:0.4s;animation-name:animatetop;animation-duration:0.4s}@-webkit-keyframes animatetop{from{top:-300px;opacity:0}to{top:0;opacity:1}}@keyframes animatetop{from{top:-300px;opacity:0}to{top:0;opacity:1}}.close{color:white;font-weight:bold;}.close:hover,.close:focus{color:#000;text-decoration:none;cursor:pointer;}.modal-header{background-color:#5cb85c;color:white;}.modal-footer{background-color:#5cb85c;color:white;</style>';
        echo '<script>
                var e = \'<div id="myModal" class="modal"><div class="modal-content"><div class="modal-header"> <i><span style="margin-left:90%;" class="close" id="close"> close </span></i> </div><div class="modal-body"><div id="mo_message"></div><div id="mo_validate_field" style="margin:1em"><input type="text" name="order_verify" autofocus="true" placeholder="" id="mo_otp_token" required="true" style="color: #000;font-family: Helvetica,sans-serif;padding: 7px;text-shadow: 1px 1px 0 #fff;width: 100%;border-radius: 2px;" class="mo_customer_validation-textbox" autofocus="true"/><input type="button" name="miniorange_otp_validate_submit"  style="margin-top:1%;width:100%" id="miniorange_otp_validate_submit" class="miniorange_otp_token_submit"  value="'.mo_("Validate OTP").'" /></div></div></div></div>\';
                jQuery(\'form[name="checkout"]\').append(e);
             </script>';
            }


    
    function my_custom_checkout_field( $checkout )
    {
        if($this->_guestCheckOutOnly && is_user_logged_in())  return;
        echo '<div id="mo_validation_wrapper">';
        $this->show_validation_button_or_text();

        echo '<div id="mo_message" hidden></div>';

        woocommerce_form_field( 'order_verify', array(
        'type'          => 'text',
        'class'         => array('form-row-wide'),
        'label'         => mo_('Verify Code'),
        'required'  	=> true,
        'placeholder'   => mo_('Enter Verification Code'),
        ), $checkout->get_value( 'order_verify' ));
        $this->place_after_validating_field();
        $this->common_button_or_link_enable_disable_script();

        
        echo ',$mo("#miniorange_otp_token_submit").click(function(o){
            if($mo("#mo_message").length==0)
            {
                $mo("<div id=\"mo_message\"></div>").insertBefore("#order_verify_field");
            }
        })';
        echo ',$mo(".woocommerce-error").length>0&&$mo("html, body").animate({scrollTop:$mo("div.woocommerce").offset().top-50},1e3),$mo("#miniorange_otp_token_submit").click(function(o){var e=$mo("input[name=billing_email]").val(),n=$mo("#billing_phone").val(),a=$mo("div.woocommerce");a.addClass("processing").block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),$mo.ajax({url:"'.site_url().'/?option=miniorange-woocommerce-checkout",type:"POST",data:{user_email:e, user_phone:n},crossDomain:!0,dataType:"json",success:function(o){ if(o.result=="success"){$mo(".blockUI").hide(),$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").addClass("woocommerce-message").removeClass("woocommerce-error"),$mo("#mo_message").show()}else{$mo(".blockUI").hide(),$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").addClass("woocommerce-error"),$mo("#mo_message").show();} ;},error:function(o,e,n){}}),o.preventDefault()});});</script></div>';
    }


    
    function show_validation_button_or_text()
    {
        if(!$this->_showButton) $this->showTextLinkOnPage();
        if($this->_showButton) $this->_showButtonOnPage();
    }


    
    function showTextLinkOnPage()
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0) {
            echo '<div style = "margin-bottom: 15px;" title="' . mo_("Please Enter a Phone Number to enable this link") . '">
                        <a  href="#" style="text-align:center;color:grey;pointer-events:initial;display:none;" 
                            id="miniorange_otp_token_submit" 
                            class="" >' . mo_($this->_buttonText) . '
                        </a>
                   </div>';
        } else {
            echo '<div style = "margin-bottom: 15px;" title="' . mo_("Please Enter an Email Address to enable this link") . '">
                        <a  href="#" 
                            style="text-align:center;color:grey;pointer-events:initial;display:none;" 
                            id="miniorange_otp_token_submit" 
                            class="" >' . mo_($this->_buttonText) . '
                        </a>
                   </div>';
        }
    }


    
    function _showButtonOnPage()
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            echo '<input type="button" class="button alt" style="'
                . ( $this->_popupEnabled ? 'float: right;line-height: 1;margin-right: 2em;padding: 1em 2em; display:none;' : 'display:none;width:100%;margin-bottom: 15px;' )
                .'" id="miniorange_otp_token_submit" title="'
                .mo_("Please Enter a Phone Number to enable this.").'" value="';
        else
            echo '<input type="button" class="button alt" style="'
                . ( $this->_popupEnabled ? 'float: right;line-height: 1;margin-right: 2em;padding: 1em 2em; display:none;' : 'display:none;width:100%;margin-bottom: 15px;' )
                .'" id="miniorange_otp_token_submit" title="'
                .mo_("Please Enter an Email Address to enable this.").'" value="';
        echo mo_($this->_buttonText).'"></input>';
    }


    
    function common_button_or_link_enable_disable_script()
    {
        echo '<script>jQuery(document).ready(function() { $mo = jQuery,';
        echo '$mo(".woocommerce-message").length>0&&($mo("#mo_message").addClass("woocommerce-message"),$mo("#mo_message").show())';
    }

    
    function place_after_validating_field(){

        echo '<script>jQuery(document).ready(function(){
                    setTimeout(function(){
                        jQuery("#mo_validation_wrapper").insertAfter("#billing_'. (strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? 'phone':'email') .'_field");
                    },200);
        });</script>';
    }

    
    function my_custom_checkout_field_process($data,$errors)
    {
        if(!MoUtility::isBlank($errors->get_error_messages())) return;
        if($this->_guestCheckOutOnly && is_user_logged_in()) return;
        if(!$this->isPaymentVerificationNeeded()) return;
        if($this->checkIfVerificationNotStarted()) return;
        if($this->checkIfVerificationCodeNotEntered()) return;
        $this->handle_otp_token_submitted();
    }


    
    function handle_otp_token_submitted()
    {
        if(strcasecmp($this->_otpType,$this->_typePhoneTag)==0)
            $error = $this->processPhoneNumber();
        else
            $error = $this->processEmail();
        if(!$error) $this->validateChallenge($this->getVerificationType(),'order_verify');
    }


    
    function isPaymentVerificationNeeded()
    {
        $payment_method = sanitize_text_field($_POST['payment_method']);
        return $this->_selectivePayment ? array_key_exists($payment_method,$this->_paymentMethods) : TRUE;
    }


    
    function processPhoneNumber()
    {

        $phone = MoUtility::processPhoneNumber($_POST['billing_phone']);
        if(strcasecmp(MoPHPSessions::getSessionVar('phone_number_mo'),$phone)!=0)
        {
            wc_add_notice(  MoMessages::showMessage(MoMessages::PHONE_MISMATCH), MoConstants::ERROR_JSON_TYPE );
            return TRUE;
        }
        return FALSE;
    }


    
    function processEmail()
    {
        if(strcasecmp(MoPHPSessions::getSessionVar('user_email'), $_POST['billing_email'])!=0)
        {
            wc_add_notice(  MoMessages::showMessage(MoMessages::EMAIL_MISMATCH), MoConstants::ERROR_JSON_TYPE );
            return TRUE;
        }
        return FALSE;
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        wc_add_notice( MoUtility::_get_invalid_otp_method(), MoConstants::ERROR_JSON_TYPE );
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        $this->unsetOTPSessionVariables();
    }


    
    function enqueue_script_on_page()
    {
        $script_url = MOV_URL . 'includes/js/wccheckout.min.js?version='.MOV_VERSION;
        wp_register_script('wccheckout',$script_url, array('jquery') ,MOV_VERSION,true);
        wp_localize_script( 'wccheckout', 'mowccheckout', array(
            'paymentMethods' => $this->_paymentMethods,
            'selectivePaymentEnabled' => $this->_selectivePayment,
            'popupEnabled' => $this->_popupEnabled,
            'isLoggedIn' => $this->_guestCheckOutOnly && is_user_logged_in(),
            'otpType' => strcasecmp($this->_otpType,$this->_typePhoneTag)==0 ? 'phone':'email',
        ));
        wp_enqueue_script('wccheckout');
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId, $this->_formSessionVar]);
    }

    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && ($this->_otpType == $this->_typePhoneTag)) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;
        if(!function_exists('is_plugin_active')) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if(!is_plugin_active( 'woocommerce/woocommerce.php' ) ) return;
        $_paymentMethods = array();
        if(array_key_exists('wc_payment',$_POST)){
            foreach ($_POST['wc_payment'] as $selected) {
                $_paymentMethods[$selected] = $selected;
            }
        }

        $this->_isFormEnabled = $this->sanitizeFormPOST('wc_checkout_enable');
        $this->_otpType = $this->sanitizeFormPOST('wc_checkout_type');
        $this->_guestCheckOutOnly = $this->sanitizeFormPOST('wc_checkout_guest');
        $this->_showButton = $this->sanitizeFormPOST('wc_checkout_button');
        $this->_popupEnabled = $this->sanitizeFormPOST('wc_checkout_popup');
        $this->_selectivePayment = $this->sanitizeFormPOST('wc_checkout_selective_payment');
        $this->_buttonText = $this->sanitizeFormPOST('wc_checkout_button_link_text');
        $this->_paymentMethods = $_paymentMethods;
        $this->_disableAutoLogin = $this->sanitizeFormPOST('wc_checkout_disable_auto_login');
        $this->_restrictDuplicates = $this->sanitizeFormPOST('wc_checkout_restrict_duplicates');

        if($this->basicValidationCheck(BaseMessages::WC_CHECKOUT_CHOOSE)) {
            update_mo_option('wc_checkout_restrict_duplicates', $this->_restrictDuplicates);
            update_mo_option('wc_checkout_disable_auto_login', $this->_disableAutoLogin);
            update_mo_option('wc_checkout_enable', $this->_isFormEnabled);
            update_mo_option('wc_checkout_type', $this->_otpType);
            update_mo_option('wc_checkout_guest', $this->_guestCheckOutOnly);
            update_mo_option('wc_checkout_button', $this->_showButton);
            update_mo_option('wc_checkout_popup', $this->_popupEnabled);
            update_mo_option('wc_checkout_selective_payment', $this->_selectivePayment);
            update_mo_option('wc_checkout_button_link_text', $this->_buttonText);
            update_mo_option('wc_checkout_payment_type', maybe_serialize($_paymentMethods));
        }
    }

    

    
    public function isGuestCheckoutOnlyEnabled(){ return $this->_guestCheckOutOnly; }

    
    public function showButtonInstead(){ return $this->_showButton; }

    
    public function isPopUpEnabled(){ return $this->_popupEnabled; }

    
    public function getPaymentMethods(){ return $this->_paymentMethods; }

    
    public function isSelectivePaymentEnabled(){ return $this->_selectivePayment; }

    
    public function isAutoLoginDisabled(){ return $this->_disableAutoLogin; }
}