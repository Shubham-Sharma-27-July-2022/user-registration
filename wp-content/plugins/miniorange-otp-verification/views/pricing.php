<?php

use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;
use OTP\Helper\MoAddonListContent;
use OTP\Helper\MoOffer;

echo MoOffer::showOfferPricing('div.mo_charges',['$39','$59','$119'],'New Season');

echo'
    <div class="mo_registration_divided_layout mo-otp-full">
        <div class="mo_registration_pricing_layout mo-otp-center">
        <div class="mo-cd-pricing-switcher">
                    <p class="fieldset" style="background-color: #266184;">
                        
                        <input type="radio" class="mo_price_selector" name="sitetype" value="premium_addons" id="premium_addons" onclick="mo_otp_show_addons();">
                        <label for="premium_addons">Premium Addons</label>
                        <input type="radio" class="mo_price_selector" name="sitetype" value="regular_plans" id="regular_plans" onclick="mo_otp_show_plans();" checked>
                        <label for="regular_plans">Licensing Plans</label>';
                        
                        
echo'               </p>
                    </p>
                    </div>
            <div id="mo_otp_plans_pricing_table">
            <table class="mo_registration_pricing_table">
                <h2>'.mo_("LICENSING PLANS").'
                    <span style="float:right">
                    <input type="button" style="" name="mo_firebase_gateway_plan" id="mo_firebase_gateway_plan"
                                class="button button-primary button-large" value="'.mo_("Firebase Gateway Plan").'"/>
                    <input type="button"  name="Supported_payment_methods" id="pmt_btn"
                                class="button button-primary button-large" value="'.mo_("Supported Payment Methods").'"/>
                        <input type="button" '.mo_esc_string($disabled,"attr").' name="check_btn" id="check_btn"
                                class="button button-primary button-large" value="'.mo_("Check License").'"/>
                    </span>
                <h2>
                <hr>';
echo '
<div class="mo_main_price_table">
    <table class="mo_price-table">
        <tbody>
        <tr>
        </tr>
            <tr class="mo_price-table-head">
                <td>FEATURES</td>
                <td>
                    MINIORANGE GATEWAY WITH ADDONS
                    <span class="mo_pricing_question tooltip">
                                        <i class="dashicons dashicons-warning" data-toggle="onprem"></i>
                                        <span class="tooltiptext">
                                            <span class="header">
                                                <b><i>WHAT DO YOU MEAN BY miniOrange GATEWAY? WHEN DO I OPT FOR THIS PLAN?
                                                </i></b>
                                            </span>
                                            <br><br><span class="body">miniOrange Gateway means that you want the complete package of OTP generation, delivery ( to users phone or email ) and verification. Opt for this plan when you dont have your own SMS or Email gateway for message delivery. <br><br> <b><i>NOTE:</i></b> SMS Delivery charges depend on the country you want to send the OTP to. Click on the Upgrade Now button below and select your country to see the full pricing.
                                            </span>
                                        </span>
                                    </span>
                </td>
                <td>
                    CUSTOM GATEWAY WITH ADDONS
                    <span class="mo_pricing_question tooltip">
                                        <i class="dashicons dashicons-warning" data-toggle="onprem"></i>
                                        <span class="tooltiptext">
                                            <span class="header">
                                                <b><i>WHAT DO YOU MEAN BY CUSTOM GATEWAY? WHEN DO I OPT FOR THIS PLAN?
                                                </i></b>
                                            </span><br><br>
                                            <span class="body">Custom Gateway means that you have your own SMS or Email Gateway for delivering OTP to the users email or phone. The plugin will handle OTP generation and verification but your existing gateway would be used to deliver the message to the user. <br><br><b><i>NOTE:</i></b> You will still need to pay SMS and Email delivery charges to your gateway separately.
                                            </span>
                                        </span>
                                    </span>
                </td>
                <td>
                    TWILIO GATEWAY WITH ADDONS + MSG91 GATEWAY
                    <span class="mo_pricing_question tooltip">
                                        <i class="dashicons dashicons-warning" data-toggle="onprem"></i>
                                        <span class="tooltiptext">
                                            <span class="header">
                                                <b><i>WHAT DO YOU MEAN BY TWILIO GATEWAY? WHEN DO I OPT FOR THIS PLAN?
                                                </i></b>
                                            </span><br><br>
                                            <span class="body">Custom Gateway (Includes Twilio gateway support) means that you have your own SMS or Email Gateway for delivering OTP to the users email or phone. The plugin will handle OTP generation and verification but your existing gateway would be used to deliver the message to the user. <br><br><b><i>NOTE:</i></b> You will still need to pay SMS and Email delivery charges to your gateway separately.
                                            </span>
                                        </span>
                                    </span>
                </td>
                <td>
                    ENTERPRISE PLAN - All Inclusive + AWS SNS
                    <span class="mo_pricing_question tooltip">
                                        <i class="dashicons dashicons-warning" data-toggle="onprem"></i>
                                        <span class="tooltiptext">
                                            <span class="header">
                                                <b><i>WHAT DO YOU MEAN BY ENTERPRISE GATEWAY? WHEN DO I OPT FOR THIS PLAN?
                                                </i></b>
                                            </span><br><br>
                                            <span class="body">Enterprise is an all inclusive plan that includes gateway support for miniOrange as well as other 3rd party SMS gateways. This plan comes with additional features such as Alphanumeric OTP format, OTP enabled for certain countries, Backup SMS gateway, Globally banned phone numbers etc.<br><br><b><i>NOTE:</i></b> If you opt to use your own SMS gateway then you need to pay SMS and Email delivery charges to your gateway separately.
                                            </span>
                                        </span>
                                    </span>
                </td>
            </tr>
            <tr class="mo_price-table-plan-pricing">
                <td class="mo_price"><img class="mo_features_graphic" src="'.MOV_FEATURES_GRAPHIC.'" style="width: 150px;"></td>
                                <td class="mo_price"><br><br>
                                    <div class="mo-pricing-div-miniorange">
                                        <table>
                                            <tr>
                                                <td class="mo_registration_pricing_text" style="width:27%">For Email:</td>
                                                <td style="width:100%"><select class="mo-form-control" style="width:100%">
                                                    <option>$2 per 100 Email</option>
                                                    <option>$5 per 500 Email</option>
                                                    <option>$7 per 1k Email</option>
                                                    <option>$20 per 5k Email</option>
                                                    <option>$30 per 10k Email</option>
                                                    <option>$45 per 50k Email</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mo_registration_pricing_text" style="width:27%">For SMS:</td>
                                                <td style="width:100%"><select class="mo-form-control" style="width:100%">
                                                    <option>$1 per 100 OTP*</option>
                                                    <option>$5 per 500 OTP*</option>
                                                    <option>$7 per 1k OTP*</option>
                                                    <option>$20 per 5k OTP*</option>
                                                    <option>$30 per 10k OTP*</option>
                                                    <option>$45 per 50k OTP*</option>
                                                    </select>
                                                </td>
                                            </tr>
                                    </div>
                                        </table>';
        echo '<div class="mo_plan_description">
        <span style="font-size: 10px; color:red;"> <b> * SMS pricing may vary depending on country.</b></span><br><br>
        <b>Hassle-Free Setup, Just recharge and Enjoy!</b><br>
        <br>Wordpress Registration & Contact Forms
        <br>WooCommerce SMS Notifications
        <br>Premium Support</div><br>';
                                                              if(strcmp($plan,MoConstants::PCODE)!=0 && strcmp($plan,MoConstants::BCODE)!=0
                                            && strcmp($plan,MoConstants::CCODE)!=0 && strcmp($plan,MoConstants::NCODE)!=0)
        echo'                                <div class="mo_pricing_button"><input type="button"  '.mo_esc_string($disabled,"attr").' class="button button-primary button-large mo_dollar_price"
                                                    onclick="mo2f_upgradeform(\'wp_otp_verification_basic_plan\')"
                                                    value="'.mo_("Upgrade Now").'"/></div><br><br>';
                                        else
        echo'                               <div class="mo_pricing_button"><input type="button"  '.mo_esc_string($disabled,"attr").' class="button button-primary button-large mo_dollar_price"
                                                    onclick="mo2f_upgradeform(\'wp_otp_verification_upgrade_plan\')"
                                                    value="'.mo_("Recharge").'"/></div><br><br>';
        echo'                </td>
                                <td class="mo_price">
                                <div class="mo_charges">'.mo_esc_string($gateway_plus_addon,"attr").'</div>
                                <br><br><br><br>
                                 <div class="mo_plan_description">
                                 Custom SMS Gateway
                                 <br>WooCommerce SMS Notification
                                 <br>Wordpress Registration & Contact Forms
                                 <br>Premium Support</div><br><br><br>';

                                if(strcmp($plan,MoConstants::AACODE)==0 || strcmp($plan,MoConstants::AACODE2)==0
                                    || strcmp($plan,MoConstants::AACODE3)==0)
                                    echo '<div class="mo_pricing_button"><input type="button" ' . mo_esc_string($disabled,"attr" ). '
                                                class="mo_dollar_price button button-primary button-large"
                                                onclick="mo2f_upgradeform(\'email_verification_upgrade_instances_plan\')"
                                                value="' . mo_("Buy More Instances") . '"/></div>';
                                else
                             echo '<div class="mo_pricing_button"><input type="button" ' . mo_esc_string($disabled,"attr") . '
                                                class="mo_dollar_price button button-primary button-large"
                                                onclick="mo2f_upgradeform(\'wp_email_verification_intranet_basic_plan\')"
                                                value="' . mo_("Upgrade Now") . '"/></div>';

                echo '      </td>
                                <td class="mo_price"><div class="mo_charges">'.$twilio_gateway_plus_addon.'</div><br><br><br><br>
                                <div class="mo_plan_description">
                                Twilio + MSG91 + Custom SMS Gateway
                                <br>WooCommerce SMS Notification
                                <br>Wordpress Registration & Contact Forms
                                <br>Premium Support</div><br><br><br>';
                                
                             if(strcmp($plan,MoConstants::TACODE)==0 || strcmp($plan,MoConstants::TACODE2)==0
                                    || strcmp($plan,MoConstants::TACODE3)==0)
                                    echo '<div class="mo_pricing_button"><input type="button" ' . mo_esc_string($disabled,"attr") . '
                                                class="mo_dollar_price button button-primary button-large"
                                                onclick="mo2f_upgradeform(\'wp_email_verification_intranet_twilio_basic_plan\')"
                                                value="' . mo_("Buy More Instances") . '"/></div>';
                            else
                                    echo '<div class="mo_pricing_button"><input type="button" ' . mo_esc_string($disabled,"attr") . '
                                                class="mo_dollar_price button button-primary button-large"
                                                onclick="mo2f_upgradeform(\'wp_email_verification_intranet_twilio_basic_plan\')"
                                                value="' . mo_("Upgrade Now") . '"/></div>';

                echo '
                                </td>
                                <td class="mo_price"><div class="mo_charges">'.$enterprise_addon.'</div><br><br><br><br>
                                <div class="mo_plan_description">
                                miniOrange + AWS SNS
                                <br> Twilio + Custom SMS Gateway
                                <br>Alphanumeric OTP
                                <br>Selected countries OTP
                                <br>Premium Support</div><br><br><br>';

                            if(strcmp($plan,MoConstants::ECODE)==0 || strcmp($plan,MoConstants::ECODE2)==0)
                                        echo '<div class="mo_pricing_button"><input type="button" ' . mo_esc_string($disabled,"attr") . '
                                                    class="mo_dollar_price button button-primary button-large"
                                                    onclick="mo2f_upgradeform(\'wp_email_verification_intranet_enterprise_plan\')"
                                                    value="' . mo_("Buy More Instances") . '"/></div>';
                                    else
                                        echo '<div class="mo_pricing_button"><input type="button" ' . mo_esc_string($disabled,"attr") . '
                                                    class="mo_dollar_price button button-primary button-large"
                                                    onclick="mo2f_upgradeform(\'wp_email_verification_intranet_enterprise_plan\')"
                                                    value="' . mo_("Upgrade Now") . '"/></div>';
                 echo '
                                </td>
            </tr>



            <tr class=mo_feature_list>
                <td>40+ popular Wordpress Forms and Themes supported</span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>WooCommerce Forms</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>Contact Form 7</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>WooCommerce & Ultimate Member SMS Notifications</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
           <tr class=mo_feature_list>
                <td>Passwordless Login</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>Password Reset Over OTP</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>Enable Country Code Dropdown</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
             <tr class=mo_feature_list>
                <td>Custom SMS Template</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>Custom Email Template</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
             <tr class=mo_feature_list>
                <td>Custom OTP Length and Validity</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>Custom Messages</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
              <tr class=mo_feature_list>
                <td>Blocked Phone Number</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
              <tr class=mo_feature_list>
                <td>Blocked Email Domain</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
             <tr class=mo_feature_list>
                <td>OTP over Call - Twilio <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>Elementor PRO<span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>WooCommece FrontEnd Manager [WCFM] <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
               <tr class=mo_feature_list>
                <td>Social Login with OTP <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>miniOrange SMS Gateway</td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>Custom SMS/SMTP Gateway</td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>Twilio SMS Gateway Support</td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
             <tr class=mo_feature_list>
                <td>AWS Gateway Support <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
            <tr class=mo_feature_list>
                <td>Test SMS Configuration <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
             <tr class=mo_feature_list>
                <td>Back-Up SMS Gateway <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
               <tr class=mo_feature_list>
                <td>WooCommerce Password Reset OTP <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
             <tr class=mo_feature_list>
                <td>Allow OTP for Selected Countries <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
              <tr class=mo_feature_list>
                <td>Geolocation Based Country Code Dropdown <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
              <tr class=mo_feature_list>
                <td>Master OTP <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
              <tr class=mo_feature_list>
                <td>Enable Alphanumeric OTP Format <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
                <tr class=mo_feature_list>
                <td>Globally Banned Phone Numbers Blocking <span class="mo_feature_new new_feature_tooltip"><span class="new_feature_tooltiptext"><span class="new_feature_header"><b>New Feature</b></span></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_unavailable"></span></td>
                <td><span class="mo_feature_available"></span></td>
            </tr>
           
        </tbody>
    </table>
</div>';

























                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                




























































                   





































                                   




















































































































































































                                    
















































        
        
        
        
        
          echo '
            <div id="mo_otp_addons_pricing" hidden>
            <table class="mo_registration_pricing_table">
                <h2>'.mo_("PREMIUM ADDONS").'
                    <span style="float:right">
                    <input type="button"  name="Supported_payment_methods" id="pmt_btn_addon"
                                class="button button-primary button-large" value="'.mo_("Supported Payment Methods").'"/>
                        <input type="button" '.mo_esc_string($disabled,"attr").' name="check_btn" id="check_btn"
                                class="button button-primary button-large" value="'.mo_("Check License").'"/>
                    </span>
                <h2>
                <hr></table>';
            MoAddonListContent::showAddonsContent();
echo'
            </div>
            </div></div>
            <div class="mo_registration_divided_layout mo-otp-full" >
                <div class="mo_registration_firebase_layout mo-otp-center" id="mo_registration_firebase_layout">
                    <div class="mo_firebase_adv_header" style="text-align:center;padding-top:0.5%;">
                        <img src="'.MOV_FIREBASE.'" style="height:40px;width:40px;" >
                        <span style="font-family: ui-serif;font-size: 30px;padding-left: 10px;vertical-align: super;">Firebase Gateway Plan</span>
                    </div>
                    <br>
                    <div class="mo_firebase_adv_content" style="font-size: 15px!important;">
                        <div>
                            <b style="margin-left: 3%;">Use Firebase as your custom SMS gateway with <a href="https://firebase.google.com/pricing"target="_blank">10K free transactions</a> to send One Time Passcodes (OTP).</b>
                            <input type="button" style="float:right;margin-right:4%;width:10%;" onclick="otpSupportOnClick(\'Hi! I am interested in the Firebase Gateway plan, could you please tell me more about this addon?\');" class="button button-primary button-large" id="mo_firebase_plan_interested" value="Interested">
                        </div>
                        <br><br><br>
                        <div class="mo_firebase_feature_container" style="display:flex;border-radius: 7px;margin-left: 3%;line-height: 175%;">
                            <div style="width:46%;background: #e0eeee99;margin-right: 1%;padding: 1%;border-radius: 8px;">
                                <b>&#9733; Login With Phone</b><br>
                                <b>&#9733; Custom Redirection on Login Form & Registration Form</b><br>
                                <b>&#9733; Custom CSS for Login and Registration Forms</b>
                            </div>
                            <div style="width:46%;background: #e0eeee99;padding: 1%;border-radius: 8px;">
                                <b>&#9733; Password Less Login</b><br>
                                <b>&#9733; OTP Verification on Registration Form</b><br>
                                <b>&#9733; User role Selection on registration</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  ';



































echo'  
    <div class="mo_registration_divided_layout mo-otp-full">
        <div class="mo_registration_pricing_layout mo-otp-center">

            <!-----------------------------------------------------------------------------------------------------------------
                                                    EXTRA INFORMATION ABOUT THE PLANS
            ------------------------------------------------------------------------------------------------------------------->

            <br>
            <div id="disclaimer" style="margin-bottom:15px;">
                <span style="font-size:15px;">
                    <b>'.mo_("SMS gateway").'</b>
                        '.mo_(" is a service provider for sending SMS on your behalf to your users.").'<br>
                    <b>'.mo_("SMTP gateway").'</b>
                        '.mo_(" is a service provider for sending Emails on your behalf to your users.").'<br><br>
                    *'.mo_("Transaction prices may very depending on country. If you want to use more than 50k transactions, mail us at").'
                        <a href="mailto:'.MoConstants::SUPPORT_EMAIL.'"><b>'.MoConstants::SUPPORT_EMAIL.'</b></a>
                        '.mo_("or submit a support request using the Need Help button.").'<br/><br/>
                    **'.mo_("If you want to <b>use miniorange SMS/SMTP gateway</b>, and your country is not in list, mail us at").' <a href="mailto:'.MoConstants::SUPPORT_EMAIL.'">
                            <b>'.MoConstants::SUPPORT_EMAIL.'</b></a>
                            '.mo_("or submit a support request using the Need Help button.").'
                            '.mo_("We will get back to you promptly.").'<br><br>
                    ***'.mo_("<b>Custom integration charges</b> will be applied for supporting a registration form which is not already supported
                            by our plugin. Each request will be handled on a per case basis.").'<br>
                </span>
            </div>
        </div>
    </div>
     <div class="mo_registration_divided_layout mo-otp-full" id="otp_payment">
       <div class="mo_registration_pricing_layout mo-otp-center" id="otp_pay_method">
           <h3>'.mo_("Supported Payment Methods :").'</h3><hr>
            <div class="mo-pricing-container">
           <div class="mo-card-pricing-deck">
           <div class="mo-card-pricing mo-animation">
                <div class="mo-card-pricing-header">
                <img  src="'.MOV_CARD.'"  style="size: landscape;width: 100px;
           height: 27px; margin-bottom: 4px;margin-top: 4px;opacity: 1;padding-left: 8px;">
                </div>
                <hr style=" margin-left: -26px; margin-right: -26px;border-top: 4px solid #fff;">
                <div class="mo-card-pricing-body">
                <p>If payment is made through Credit Card/Intenational debit card, the license will be created automatically once payment is completed.</p>
                <p><i><b>For guide <a href='.MoConstants::FAQ_PAY_URL.' target="blank">Click Here.</a></b></i></p>
                </div>
            </div>
          <div class="mo-card-pricing mo-animation">
                <div class="mo-card-pricing-header">
                <img  src="'.MOV_PAYPAL.'"  style="size: landscape;width: 100px;
           height: 27px; margin-bottom: 4px;margin-top: 4px;opacity: 1;padding-left: 8px;">
                </div>
                <hr style=" margin-left: -26px; margin-right: -26px;border-top: 4px solid #fff;">
                <div class="mo-card-pricing-body">
                <p>Use the following PayPal ID for payment via PayPal.</p><p><i><b style="color:#1261d8">'.MoConstants::SUPPORT_EMAIL.'</b></i></p>
                 <p style="margin-top: 35%;"><i><b>Note:</b> There is an additional 18% GST applicable via PayPal.</i></p>

                </div>
            </div>
          <div class="mo-card-pricing mo-animation">
                <div class="mo-card-pricing-header">
                <img  src="'.MOV_NETBANK.'"  style="size: landscape;width: 100px;
           height: 27px; margin-bottom: 4px;margin-top: 4px;opacity: 1;padding-left: 8px;">
                </div>
                <hr style=" margin-left: -26px; margin-right: -26px;border-top: 4px solid #fff;">
                <div class="mo-card-pricing-body">
                <p>If you want to use net banking for payment then contact us at <i><b style="color:#1261d8">'.MoConstants::SUPPORT_EMAIL.'</b></i> so that we can provide you bank details. </i></p>
                <p style="margin-top: 32%;"><i><b>Note:</b> There is an additional 18% GST applicable via Bank Transfer.</i></p>
                </div>
                </div>
              </div>
          </div>
             <div class="mo-supportnote">
                <p><b>Note :</b> Once you have paid through PayPal/Net Banking, please inform us so that we can confirm and update your License.</p>
                <p>For more information about payment methods visit <a href='.MoConstants::FAQ_PAY_URL.' target="blank">Supported Payment Methods.</a></p></p> 
                </div>
     </div>
 </div>
    <div class="mo_registration_divided_layout mo-otp-full">
        <div class="mo_registration_pricing_layout mo-otp-center">
            <h3>'.mo_("Return Policy").'</h3>
            <p>'.mo_("At miniOrange, we want to ensure you are 100% happy with your purchase.".
                    " If the premium plugin you purchased is not working as advertised and you have attempted to ".
                    "resolve any feature issues with our support team, which couldn't get resolved. We will refund the".
                    " whole amount within 10 days of the purchase. Please email us at").'
                    <a href="mailto:'.MoConstants::SUPPORT_EMAIL.'">'.MoConstants::SUPPORT_EMAIL.'</a>
                '.mo_("for any queries regarding the return policy.<br> If you have any doubts regarding ".
                    "the licensing plans, you can mail us at").
                    ' <a href="mailto:'.MoConstants::SUPPORT_EMAIL.'">'.MoConstants::SUPPORT_EMAIL.'</a>
                '.mo_("or submit a query using the support form.").'</p>
            <h3>'.mo_("What is not covered?").'</h3>
            <p>
                <ol>
                    <li>'.mo_("Any returns that are because of features that are not advertised.").'</li>
                    <li>'.mo_("Any returns beyond 10 days.").'</li>
                    <li>
                        '.mo_("Any returns for Do it yourself plan if you are unable to do the setup on your own ".
                        "and need our help.").
                    '</li>
                </ol>
            </p>
        </div>
    </div>

    <form style="display:none;" id="mocf_loginform" action="'.mo_esc_string($form_action,"url").'" target="_blank" method="post">
        <input type="email" name="username" value="'.mo_esc_string($email,"attr").'" />
        <input type="text" name="redirectUrl" value="'.mo_esc_string($redirect_url,"attr").'" />
        <input type="text" name="requestOrigin" id="requestOrigin"  />
    </form>
    <form id="mo_ln_form" style="display:none;" action="" method="post">';

        wp_nonce_field($nonce);

    echo'<input type="hidden" name="option" value="check_mo_ln" />
    </form>
    <script>
    $mo = jQuery;
    $mo(document).ready(function () {
        var subPage = window.location.href.split("subpage=")[1];
            if(subPage !== "undefined")
            {
                if(subPage=="premaddons"){
                   mo_otp_show_addons()
                }
                //else if(subPage=="monthlyplan"){
                   //mo_otp_show_monthly_plan()
                //}
                
            }
        })
        function mo2f_upgradeform(planType){
            jQuery("#requestOrigin").val(planType);
            jQuery("#mocf_loginform").submit();
        }
        function mo_otp_show_plans(){
            $mo("#mo_otp_plans_pricing_table").show();
            $mo("#mo_otp_addons_pricing").hide();
            $mo("#mo_otp_show_monthly_plan").hide();
        }
        function mo_otp_show_addons(){
            $mo("#premium_addons").prop("checked",true);
            $mo("#mo_otp_addons_pricing").show();
            $mo("#mo_otp_plans_pricing_table").hide();
            $mo("#mo_otp_show_monthly_plan").hide();
        }
        function mo_otp_show_monthly_plan(){
            $mo("#monthly_plan").prop("checked",true);
            $mo("#mo_otp_addons_pricing").hide();
            $mo("#mo_otp_plans_pricing_table").hide();
            $mo("#mo_otp_show_monthly_plan").show();
        }
        function mo_get_montly_plan_data()
        {
            var monthly_sms = $mo("#mo_monthly_sms").val();
            var monthly_email = $mo("#mo_monthly_email").val();
            var monthly_country = $mo("#mo_country_code option:selected" ).text();
            var queryBody = "Hi! I am interested in the miniOrange monthly subscription module, My target country is monthly_country, Please provide a quote for monthly_sms SMS and monthly_email Emails per month.";
            var mapObj = {
               monthly_country:monthly_country,
               monthly_sms:monthly_sms,
               monthly_email:monthly_email
            };
            var queryReplaced = queryBody.replace(/monthly_country|monthly_sms|monthly_email/gi, function(matched){
              return mapObj[matched];
            });
            otpSupportOnClick(queryReplaced);
        }
    </script>';