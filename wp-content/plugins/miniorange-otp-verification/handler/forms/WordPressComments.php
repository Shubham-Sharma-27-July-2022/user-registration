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
use WP_Comment;


class WordPressComments extends FormHandler implements IFormHandler
{
    use Instance;

    protected function __construct()
    {
        $this->_isLoginOrSocialForm = FALSE;
        $this->_isAjaxForm = TRUE;
        $this->_formSessionVar = FormSessionVars::WPCOMMENT;
        $this->_phoneFormId = "input[name=phone]";
        $this->_formKey = 'WPCOMMENT';
        $this->_typePhoneTag = "mo_wpcomment_phone_enable";
        $this->_typeEmailTag = "mo_wpcomment_email_enable";
        $this->_formName = mo_("WordPress Comment Form");
        $this->_isFormEnabled = get_mo_option('wpcomment_enable');
        $this->_formDocuments = MoOTPDocs::WP_COMMENT_LINK;
        parent::__construct();
    }

    
    function handleForm()
    {
        $this->_otpType = get_mo_option('wpcomment_enable_type');
        $this->_byPassLogin = get_mo_option('wpcomment_enable_for_loggedin_users');

        if(!$this->_byPassLogin) {
            add_action( 'comment_form_logged_in_after', array($this,'_add_scripts_and_additional_fields'),1 );
            add_action( 'comment_form_after_fields', array($this,'_add_scripts_and_additional_fields'),1);
        }else{
            add_filter('comment_form_default_fields', array($this,'_add_custom_fields'),99,1);
        }
        add_filter( 'preprocess_comment', array($this,'verify_comment_meta_data'),1,1);
        add_action( 'comment_post', array($this,'save_comment_meta_data') ,1 ,1);
        add_action( 'add_meta_boxes_comment', array($this,'extend_comment_add_meta_box'),1,1);
        add_action( 'edit_comment', array($this,'extend_comment_edit_metafields'),1,1);

        $this->routeData();
    }


    
    function routeData()
    {
        if(!array_key_exists('option', $_GET)) return;

        switch (trim($_GET['option']))
        {
            case "mo-comments-verify":
                $this->_startOTPVerificationProcess($_POST);	break;
        }
    }


    
    function _startOTPVerificationProcess($getData)
    {

        MoUtility::initialize_transaction($this->_formSessionVar);

        if(strcasecmp($this->_otpType, $this->_typeEmailTag)===0 && MoUtility::sanitizeCheck('user_email',$getData))
        {
            SessionUtils::addEmailVerified($this->_formSessionVar,sanitize_email($getData['user_email']));
            $this->sendChallenge('',sanitize_email($getData['user_email']),null,sanitize_email($getData['user_email']),VerificationType::EMAIL);
        }
        else if(strcasecmp($this->_otpType, $this->_typePhoneTag)===0 && MoUtility::sanitizeCheck('user_phone',$getData))
        {
            SessionUtils::addPhoneVerified($this->_formSessionVar,trim(sanitize_text_field($getData['user_phone'])));
            $this->sendChallenge('','',null, trim(sanitize_text_field($getData['user_phone'])),VerificationType::PHONE);
        }
        else
        {
            $message =  strcasecmp($this->_otpType,$this->_typePhoneTag)===0
                        ? MoMessages::showMessage(MoMessages::ENTER_PHONE) : MoMessages::showMessage(MoMessages::ENTER_EMAIL);
            wp_send_json(MoUtility::createJson( $message, MoConstants::ERROR_JSON_TYPE ));
        }
    }


    
    function extend_comment_edit_metafields( $comment_id )
    {
        if( ! isset( $_POST['extend_comment_update'] )
            || ! wp_verify_nonce( $_POST['extend_comment_update'], 'extend_comment_update' ) ) return;

        if ( ( isset( $_POST['phone'] ) ) && ( sanitize_text_field($_POST['phone']) != '') ){
            $phone = sanitize_text_field($_POST['phone']);
            $phone = wp_filter_nohtml_kses($phone);             
            update_comment_meta( $comment_id, 'phone', $phone );
        }else{
            delete_comment_meta( $comment_id, 'phone');
        }
    }


    
    function extend_comment_add_meta_box()
    {
        add_meta_box( 'title', mo_( 'Extra Fields'  ), array($this,'extend_comment_meta_box')
                    , 'comment', 'normal', 'high' );
    }


    
    function extend_comment_meta_box ( $comment )
    {
        $phone = get_comment_meta( $comment->comment_ID, 'phone', true );
        wp_nonce_field( 'extend_comment_update', 'extend_comment_update', false );

        echo '<table class="form-table editcomment">
                <tbody>
                <tr>
                    <td class="first"><label for="phone">'.mo_( 'Phone'  ).':</label></td>
                    <td><input type="text" name="phone" size="30" value="'.esc_attr( $phone ).'" id="phone"></td>
                </tr>
                </tbody>
            </table>';
    }


    
    function verify_comment_meta_data( $commentdata )
    {
        if($this->_byPassLogin && is_user_logged_in()) return $commentdata;


        if ( ! isset( $_POST['phone'] ) && strcasecmp($this->_otpType,$this->_typePhoneTag)===0) {
            wp_die(MoMessages::showMessage(MoMessages::WPCOMMNENT_PHONE_ENTER));
        }

        if ( ! isset( $_POST['verifyotp'] ) ) {
            wp_die(MoMessages::showMessage(MoMessages::WPCOMMNENT_VERIFY_ENTER));
        }

        $otpVerType = $this->getVerificationType();

        if(!SessionUtils::isOTPInitialized($this->_formSessionVar)) {
            wp_die(MoMessages::showMessage(MoMessages::PLEASE_VALIDATE));
        }

        if($otpVerType===VerificationType::EMAIL
            && !SessionUtils::isEmailVerifiedMatch($this->_formSessionVar,sanitize_email($_POST['email']))) {
            wp_die(MoMessages::showMessage(MoMessages::EMAIL_MISMATCH));
        }

        if($otpVerType===VerificationType::PHONE
            && !SessionUtils::isPhoneVerifiedMatch($this->_formSessionVar,sanitize_text_field($_POST['phone']))) {
            wp_die(MoMessages::showMessage(MoMessages::PHONE_MISMATCH));
        }

        $this->validateChallenge($otpVerType,NULL,sanitize_text_field($_POST['verifyotp']));

        return $commentdata;
    }


    
    function _add_scripts_and_additional_fields()
    {
        if(strcasecmp($this->_otpType, $this->_typeEmailTag)===0)
            echo $this->_getFieldHTML('email');

        if(strcasecmp($this->_otpType, $this->_typePhoneTag)===0)
            echo $this->_getFieldHTML('phone');

        echo $this->_getFieldHTML('verifyotp');
    }


    
    function _add_custom_fields($fields)
    {
        
        if(strcasecmp($this->_otpType, $this->_typeEmailTag)===0)
            $fields[ 'email' ] = $this->_getFieldHTML('email');

        if(strcasecmp($this->_otpType, $this->_typePhoneTag)===0)
            $fields['phone'] = $this->_getFieldHTML('phone');

        $fields[ 'verifyotp' ] = $this->_getFieldHTML('verifyotp');
        return $fields;
    }


    
    function _getFieldHTML($fieldName)
    {
        $fieldHTML = [
            'email' => 	(
                !is_user_logged_in() && !$this->_byPassLogin ? '' :
                '<p class="comment-form-email">'
                    .'<label for="email">' . mo_( 'Email *' ) . '</label>'
                    .'<input id="email" name="email" type="text" size="30"  tabindex="4" />'
                .'</p>'
            )
            . $this->get_otp_html_content("email"),

            'phone'	=>	'<p class="comment-form-email">'
                            .'<label for="phone">' . mo_( 'Phone *' ) . '</label>'
                            .'<input id="phone" name="phone" type="text" size="30"  tabindex="4" />'
                        .'</p>'
                        . $this->get_otp_html_content("phone"),

            'verifyotp'=>   '<p class="comment-form-email">'.
                                '<label for="verifyotp">' . mo_( 'Verification Code'  ) . '</label>'.
                                '<input id="verifyotp" name="verifyotp" type="text" size="30"  tabindex="4" />'
                            .'</p><br>'
        ];

        return $fieldHTML[$fieldName];
    }


    
    function get_otp_html_content($id)
    {
        $img   = "<div style='display:table;text-align:center;'><img src='".MOV_URL. "includes/images/loader.gif'></div>";

        $html  = '<div style="margin-bottom:3%"><input type="button" class="button alt" style="width:100%" id="miniorange_otp_token_submit"';
        $html .= strcasecmp($this->_otpType, $this->_typePhoneTag)===0 ? 'title="Please Enter a phone number to enable this." '
                                                                    : 'title="Please Enter a email number to enable this." ';
        $html .= strcasecmp($this->_otpType, $this->_typePhoneTag)===0 ? 'value="Click here to verify your Phone">'
                                                                    : 'value="Click here to verify your Email">';
        $html .= '<div id="mo_message" hidden="" style="background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;"></div></div>';

        $html .= '<script>jQuery(document).ready(function(){$mo=jQuery;$mo("#miniorange_otp_token_submit").click(function(o){';
        $html .= 'var e=$mo("input[name='.$id.']").val(); $mo("#mo_message").empty(),$mo("#mo_message").append("'.$img.'"),';
        $html .= '$mo("#mo_message").show(),$mo.ajax({url:"'.site_url().'/?option=mo-comments-verify",type:"POST",';
        $html .= 'data:{user_phone:e,user_email:e},crossDomain:!0,dataType:"json",success:function(o){ if(o.result==="success"){';
        $html .= '$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),$mo("#mo_message").css("border-top","3px solid green"),';
        $html .= '$mo("input[name=email_verify]").focus()}else{$mo("#mo_message").empty(),$mo("#mo_message").append(o.message),';
        $html .= '$mo("#mo_message").css("border-top","3px solid red"),$mo("input[name=phone_verify]").focus()} ;},';
        $html .= 'error:function(o,e,n){}})});});</script>';
        return $html;
    }


    
    function save_comment_meta_data( $comment_id ) {
        if ( ( isset( $_POST['phone'] ) ) && ( sanitize_text_field($_POST['phone']) != '') ){
            $phone = sanitize_text_field($_POST['phone']);
            $phone = wp_filter_nohtml_kses($phone);
            add_comment_meta( $comment_id, 'phone', $phone );
        }
    }


    
    function handle_failed_verification($user_login,$user_email,$phone_number,$otpType)
    {

        wp_die(MoUtility::_get_invalid_otp_method());
    }


    
    function handle_post_verification($redirect_to,$user_login,$user_email,$password,$phone_number,$extra_data,$otpType)
    {

        $this->unsetOTPSessionVariables();
    }


    
    public function unsetOTPSessionVariables()
    {
        SessionUtils::unsetSession([$this->_txSessionId, $this->_formSessionVar]);
    }


    
    public function getPhoneNumberSelector($selector)
    {

        if($this->isFormEnabled() && $this->_otpType===$this->_typePhoneTag) {
            array_push($selector, $this->_phoneFormId);
        }
        return $selector;
    }


    
    function handleFormOptions()
    {
        if(!MoUtility::areFormOptionsBeingSaved($this->getFormOption())) return;

        $this->_isFormEnabled = $this->sanitizeFormPOST('wpcomment_enable');
        $this->_otpType = $this->sanitizeFormPOST('wpcomment_enable_type');
        $this->_byPassLogin = $this->sanitizeFormPOST('wpcomment_enable_for_loggedin_users');

        update_mo_option('wpcomment_enable', $this->_isFormEnabled);
        update_mo_option('wpcomment_enable_type', $this->_otpType);
        update_mo_option('wpcomment_enable_for_loggedin_users',$this->_byPassLogin);
    }
}