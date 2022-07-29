<?php

use OTP\Helper\CountryList;
use OTP\Helper\FormList;
use OTP\Helper\GatewayFunctions;
use OTP\Helper\MoUtility;
use OTP\Helper\Templates\DefaultPopup;
use OTP\Helper\Templates\ErrorPopup;
use OTP\Helper\Templates\ExternalPopup;
use OTP\Helper\Templates\UserChoicePopup;
use OTP\Objects\FormHandler;
use OTP\Objects\TabDetails;
use OTP\Objects\Tabs;


function get_plugin_form_link($formalink)
{
    if(MoUtility::sanitizeCheck('formLink',$formalink)) {
        echo '<a    class="dashicons mo-form-links dashicons-feedback mo_form_icon" 
                    href="' . mo_esc_string($formalink['formLink'],"url") . '"
                    title="' . mo_esc_string($formalink['formLink'],"url") . '"
                    id="formLink"  
                    target="_blank">'.
                '<span class="mo-link-text">'.mo_("FormLink").'</span>'.
             '</a>';
    }
    if(MoUtility::sanitizeCheck('guideLink',$formalink)) {
        echo '<a    class="dashicons mo-form-links dashicons-book-alt mo_book_icon" 
                    href="' . mo_esc_string($formalink['guideLink'],"url") . '"
                    title="Instruction Guide"
                    id="guideLink" 
                    target="_blank">'.
                '<span class="mo-link-text">'.mo_("Setup Guide").'</span>'.
             '</a>';
    }
    if(MoUtility::sanitizeCheck('videoLink',$formalink)) {
        echo '<a    class="dashicons mo-form-links dashicons-video-alt3 mo_video_icon" 
                    href="' . mo_esc_string($formalink['videoLink'],"url") . '"
                    title="Tutorial Video"
                    id="videoLink"  
                    target="_blank">'.
                '<span class="mo-link-text">'.mo_("Video Tutorial").'</span>'.
             '</a>';
    }
    echo  '<br/><br/>';
}



function mo_draw_tooltip($header,$message)
{
    echo '<span class="tooltip">
            <span class="dashicons dashicons-editor-help"></span>
            <span class="tooltiptext">
                <span class="header"><b><i>'.mo_esc_string(mo_( $header),"attr").'</i></b></span><br/><br/>
                <span class="body">'.mo_esc_string(mo_($message),"attr").'</span>
            </span>
          </span>';
}



function extra_post_data($data=null)
{
    $ignore_fields  = [
        "moFields"     => [
            'option','mo_otp_token','miniorange_otp_token_submit',
            'miniorange-validate-otp-choice-form','submit',
            'mo_customer_validation_otp_choice',
            'register_nonce','timestamp'
        ],
        "loginOrSocialForm"  => [
            'user_login','user_email','register_nonce','option',
            'register_tml_nonce',
            'mo_otp_token'
        ],
    ];

    $extraPostData      = '';
    $loginOrSocialForm  = FALSE;
    $loginOrSocialForm  = apply_filters('is_login_or_social_form',$loginOrSocialForm);
    $fields = !$loginOrSocialForm ? "moFields" : "loginOrSocialForm";
    foreach ($_POST as $key => $value) {
        $extraPostData .= !in_array($key,$ignore_fields[$fields]) ? get_hidden_fields($key,$value) : "";
    }
    return $extraPostData;
}



function get_hidden_fields($key,$value)
{
    if($key =='wordfence_userDat') return;
    $hiddenVal = '';
    if(is_array($value))
        foreach ($value as $t => $val)
            $hiddenVal .= get_hidden_fields($key.'['.$t.']',$val);
    else
        $hiddenVal .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
    return $hiddenVal;
}



function miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,$message,$otp_type,$from_both)
{
    if(!headers_sent()) header('Content-Type: text/html; charset=utf-8');
    
    $errorPopupHandler =  ErrorPopup::instance();
    
    $defaultPopupHandler =  DefaultPopup::instance();
    $htmlContent = MoUtility::isBlank($user_email) && MoUtility::isBlank($phone_number) ?
                    apply_filters( 'mo_template_build', '', $errorPopupHandler->getTemplateKey() ,$message,$otp_type,$from_both)
                    : apply_filters( 'mo_template_build', '', $defaultPopupHandler->getTemplateKey() ,$message,$otp_type,$from_both);
    echo $htmlContent;
    exit();
}



function miniorange_verification_user_choice($user_login, $user_email,$phone_number,$message,$otp_type)
{
    if(!headers_sent()) header('Content-Type: text/html; charset=utf-8');
    $userChoicePopup =  UserChoicePopup::instance();
    $htmlcontent = apply_filters( 'mo_template_build', '',$userChoicePopup->getTemplateKey() ,$message,$otp_type,TRUE);
    echo $htmlcontent;
    exit();
}



function mo_external_phone_validation_form($goBackURL,$user_email,$message,$form,$usermeta)
{
    if(!headers_sent()) header('Content-Type: text/html; charset=utf-8');
    $externalPopUp =  ExternalPopup::instance();
    $htmlcontent = apply_filters( 'mo_template_build', '', $externalPopUp->getTemplateKey() ,$message,NULL,FALSE);
    echo $htmlcontent;
    exit();
}



function get_otp_verification_form_dropdown()
{
    
    $count=0;
    $formHandler = FormList::instance();
    $tabDetails = TabDetails::instance();
    $request_uri = $_SERVER['REQUEST_URI'];
    echo '
        <div class="modropdown" id="modropdown">
            <span class="dashicons dashicons-search"></span>
                <input type="text" id="searchForm" class="dropbtn" placeholder="'.mo_( 'Search and select your Form.' ).'" />				
            <div class="modropdown-content" id="formList">';
                
                foreach ($formHandler->getList() as $key=>$form)
                {
                    $count++;
                    $className = get_mo_class($form);
                    $className = $form->isFormEnabled() ? "configured_forms#".$className : $className."#".$className;
                    $url = add_query_arg(
                        ['page' => $tabDetails->_tabDetails[Tabs::FORMS]->_menuSlug,'form' => $className],
                        $request_uri
                    );
                    if(!$form->isAddOnForm()) {
                        echo '<div class="search_box">';
                        echo '<a class="mo_search"';
                        echo ' href="'.mo_esc_string($url,"url").'" ';
                        echo ' data-value="' . mo_esc_string($form->getFormName(),"attr") . '" data-form="' . mo_esc_string($className,"attr") . '">';
                        echo ' <span class="';
                        echo $form->isFormEnabled() ? 'enabled">' : '">';
                        if(strrpos($className, "YourOwnForm") == 0)
                        echo $count.'.&nbsp';
                        echo $form->isFormEnabled() ? "( ENABLED ) " : "";
                        echo $form->getFormName() . '</span></a></div>';
                    }
                }
    echo	'</div>
        </div>';
}



function get_country_code_dropdown()
{
    echo '<select name="default_country_code" id="mo_country_code">';
    echo '<option value="" disabled selected="selected">
            --------- '.mo_( 'Select your Country' ).' -------
          </option>';
    foreach (CountryList::getCountryCodeList() as $key => $country)
    {
        echo '<option data-countrycode="'.mo_esc_string($country['countryCode'],"attr").'" value="'.mo_esc_string($key,"attr").'"';
        echo CountryList::isCountrySelected(mo_esc_string($country['countryCode'],"attr"),mo_esc_string($country['alphacode'],"attr")) ? 'selected' : '';
        echo '>'.mo_esc_string($country['name'],"attr").'</option>';
    }
    echo '</select>';
}



function get_country_code_multiple_dropdown()
{
    echo '<select multiple size="5" name="allow_countries[]" id="mo_country_code">';
    echo '<option value="" disabled selected="selected">
            --------- '.mo_( 'Select your Countries' ).' -------
          </option>';
    
    echo '</select>';
}



function show_configured_form_details($controller,$disabled,$page_list)
{
    
    $formHandler = FormList::instance();
    
    foreach ($formHandler->getList() as $form) {
        if($form->isFormEnabled() && !$form->isAddOnForm()) {
            $namespaceClass = get_class($form);
            $className = substr($namespaceClass, strrpos($namespaceClass, '\\') + 1);
            include $controller . 'forms/' . $className . '.php';
            echo '<br/>';
        }
    }
}



function get_wc_payment_dropdown($disabled,$checkout_payment_plans)
{
    if( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        echo mo_( '[ Please activate the WooCommerce Plugin ]' ); return;
    }
    $paymentPlans = WC()->payment_gateways->payment_gateways();
    echo '<select multiple size="5" name="wc_payment[]" id="wc_payment">';
    echo 	'<option value="" disabled>'.mo_( 'Select your Payment Methods' ).'</option>';
    foreach ($paymentPlans as $paymentPlan) {
        echo '<option ';
        if($checkout_payment_plans && array_key_exists($paymentPlan->id, $checkout_payment_plans)) echo 'selected';
        elseif(!$checkout_payment_plans) echo 'selected';
        echo ' value="'.esc_attr( $paymentPlan->id ).'">'.$paymentPlan->title.'</option>';
    }
    echo '</select>';
}



function get_multiple_form_select($form_details,$showVerifyField,$showEmailAndPhoneField,$disabled,$key,$formName,$keyType)
{

    $rowTemplate = "<div id='row{FORM}{KEY}_{INDEX}'>
                            %s : 
                            <input 	id='{FORM}_form_{KEY}_{INDEX}' 
                                    class='field_data' 
                                    name='{FORM}_form[form][]' 
                                    type='text' 
                                    value='{FORM_ID_VAL}'>
                                    {EMAIL_AND_PHONE_FIELD}
                                    {VERIFY_FIELD}
                        </div>";

    $emailAndPhoneField = " <span {HIDDEN1}>
                                    %s: 
                                    <input  id='{FORM}_form_email_{KEY}_{INDEX}' 
                                            class='field_data' 
                                            name='{FORM}_form[emailkey][]' 
                                            type='text' 
                                            value='{EMAIL_KEY_VAL}'>
                                </span>
                                <span {HIDDEN2}>
                                    %s: 
                                    <input  id='{FORM}_form_phone_{KEY}_{INDEX}' 
                                            class='field_data'  
                                            name='{FORM}_form[phonekey][]' 
                                            type='text' value='{PHONE_KEY_VAL}'>
                                </span>";

    $verifyField = "<span>
                            %s: 
                            <input 	class='field_data' 
                                    id='{FORM}_form_verify_{KEY}_{INDEX}' 
                                    name='{FORM}_form[verifyKey][]' 
                                    type='text' value='{VERIFY_KEY_VAL}'>
                        </span>";

    $verifyField = $showVerifyField ? $verifyField : "";

    $emailAndPhoneField = $showEmailAndPhoneField ? $emailAndPhoneField : "";

    $rowTemplate = MoUtility::replaceString(['VERIFY_FIELD'=> $verifyField, 'EMAIL_AND_PHONE_FIELD'=> $emailAndPhoneField ], $rowTemplate);

    $rowTemplate = sprintf(
        $rowTemplate,
        mo_("Form ID"),
        mo_("Email Field $keyType"),
        mo_("Phone Field $keyType"),
        mo_("Verification Field $keyType")
    );

    $counter = 0;
    if(MoUtility::isBlank($form_details))
    {
        $details = [
            'KEY' => $key,
            'INDEX' => 0,
            'FORM' => $formName,
            'HIDDEN1' => $key===2 ? 'hidden' : '',
            'HIDDEN2' => $key===1 ? 'hidden' : '',
            'FORM_ID_VAL' => '',
            'EMAIL_KEY_VAL' => '',
            'PHONE_KEY_VAL' => '',
            'VERIFY_KEY_VAL' => '',
        ];
        echo MoUtility::replaceString($details,$rowTemplate);
    }
    else
    {
        foreach ($form_details as $formKey => $form_detail) {
            $details = [
                'KEY' => $key,
                'INDEX' => $counter,
                'FORM' => $formName,
                'HIDDEN1' => $key===2 ? 'hidden' : '',
                'HIDDEN2' => $key===1 ? 'hidden' : '',
                'FORM_ID_VAL' => $showEmailAndPhoneField ? $formKey : $form_detail,
                'EMAIL_KEY_VAL' => $showEmailAndPhoneField ? $form_detail['email_show'] : '',
                'PHONE_KEY_VAL' => $showEmailAndPhoneField ? $form_detail['phone_show'] : '',
                'VERIFY_KEY_VAL' => $showVerifyField ? $form_detail['verify_show'] : '',
            ];
            echo MoUtility::replaceString($details,$rowTemplate);
            $counter++;
        }
    }
    $result['counter']	 = $counter;
    return $result;
}



function multiple_from_select_script_generator($showVerifyField,$showEmailAndPhoneField,$formName,$keyType,$counters)
{
    $rowTemplate = "<div id='row{FORM}{KEY}_{INDEX}'>
                            %s : 
                            <input  id='{FORM}_form_{KEY}_{INDEX}' 
                                    class='field_data' 
                                    name='{FORM}_form[form][]' 
                                    type='text' 
                                    value=''> 
                                    {EMAIL_AND_PHONE_FIELD}{VERIFY_FIELD} 
                        </div>";

    $verifyField = "<span> %s: 
                            <input 	class='field_data' 
                                    id='{FORM}_form_verify_{KEY}_{INDEX}' 
                                    name='{FORM}_form[verifyKey][]' 
                                    type='text' value=''>
                        </span>";
    $emailAndPhoneField = "<span {HIDDEN1}> %s: 
                                    <input 	id='{FORM}_form_email_{KEY}_{INDEX}' 
                                            class='field_data' 
                                            name='{FORM}_form[emailkey][]' 
                                            type='text' value=''>
                                </span>
                                <span {HIDDEN2}> %s: 
                                    <input 	id='{FORM}_form_phone_{KEY}_{INDEX}' 
                                            class='field_data'  
                                            name='{FORM}_form[phonekey][]' 
                                            type='text' 
                                            value=''>
                                </span>";

    $verifyField = $showVerifyField ? $verifyField : "";
    $emailAndPhoneField = $showEmailAndPhoneField ? $emailAndPhoneField : "";

    $rowTemplate = MoUtility::replaceString(
        ['VERIFY_FIELD'=> $verifyField, 'EMAIL_AND_PHONE_FIELD'=> $emailAndPhoneField ],
        $rowTemplate
    );

    $rowTemplate = sprintf(
        $rowTemplate,
        mo_("Form ID"),
        mo_("Email Field $keyType"),
        mo_("Phone Field $keyType"),
        mo_("Verification Field $keyType")
    );

    $rowTemplate = trim(preg_replace('/\s\s+/', ' ', $rowTemplate));

    $scriptTemplate = " <script>
                                var {FORM}_counter1, {FORM}_counter2, {FORM}_counter3;
                                jQuery(document).ready(function(){  
                                    {FORM}_counter1 = ". $counters[0] ."; {FORM}_counter2 = " .$counters[1]. "; {FORM}_counter3 = " . $counters[2]. "; 
                                });
                            </script>
                            <script>
                                function add_{FORM}(t,n)
                                {
                                    var count = this['{FORM}_counter'+n];
                                    var hidden1='',hidden2='',both='';
                                    var html = \"".$rowTemplate."\";
                                    if(n===1) hidden2 = 'hidden';
                                    if(n===2) hidden1 = 'hidden';
                                    if(n===3) both = 'both_';
                                    count++;
                                    html = html.replace('{KEY}', n).replace('{INDEX}',count).replace('{HIDDEN1}',hidden1).replace('{HIDDEN2}',hidden2);
                                    if(count!==0) {
                                        \$mo(html).insertAfter(\$mo('#row{FORM}'+n+'_'+(count-1)+''));
                                    }
                                    this['{FORM}_counter'+n]=count;
                                }
                            
                                function remove_{FORM}(n)
                                {
                                    var count =   Math.max(this['{FORM}_counter1'],this['{FORM}_counter2'],this['{FORM}_counter3']);
                                    if(count !== 0) {
                                        \$mo('#row{FORM}1_' + count).remove();
                                        \$mo('#row{FORM}2_' + count).remove();
                                        \$mo('#row{FORM}3_' + count).remove();
                                        count--;
                                        this['{FORM}_counter3']=this['{FORM}_counter1']=this['{FORM}_counter2']=count;
                                    }       
                                }
                            </script>";
    $scriptTemplate = MoUtility::replaceString(['FORM'=>$formName],$scriptTemplate);
    echo $scriptTemplate;

}



function show_addon_list()
{
    
    $gateway = GatewayFunctions::instance();
    $gateway->showAddOnList();
}