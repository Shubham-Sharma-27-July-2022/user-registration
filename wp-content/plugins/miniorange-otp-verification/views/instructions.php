<?php

use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;


echo '<div class="mo_registration_table_layout mo-otp-left">';
echo'       <table style="width:100%">
                <tr>
                    <td colspan="2">
                        <h2>'.mo_("PREMIUM FEATURES").'
                            <span style="float:right;margin-top:-10px;">
                                <span   class="mo-dashicons dashicons dashicons-arrow-up toggle-div" 
                                        data-show="false" 
                                        data-toggle="mo_otp_new_features">                                            
                                </span>
                            </span>
                        </h2> 
                        To know more, please kindly contact us at <a style="cursor:pointer;" onClick="otpSupportOnClick();"><u> otpsupport@xecurify.com</u></a>.
                        <hr>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
            <div id="mo_otp_new_features">

            
            <div class="mo_otp_note">
            <table>
            <tr>
            <td> <img class = "mo_support_form_new_firebase_feature mo_otp_new_feature_class" src="'.MOV_URL.'includes/images/mo_firebase.png"></td>
            <td> <div class = "mo_otp_new_feature_class_note"> <b>Use Firebase as your Custom SMS gateway to send One Time Passcodes for Phone Verification. </b></div> </td>
            </tr>
            </table>
            </div>
            
            <div class="mo_otp_note">
            <table>
            <tr>
            <td> <img class = "mo_support_form_new_feature mo_otp_new_feature_class" src="'.MOV_URL.'includes/images/mo_wcfm.jpeg"></td>
            <td> <div class = "mo_otp_new_feature_class_note"> <b>Phone or Email Verification via OTP on the WCFM Vendor Registration and WCFM Vendor Membership Forms.</b></div> </td>
            </tr>
            </table>
            </div>

            <div class="mo_otp_note">
            <table>
            <tr>
            <td> <img class = "mo_support_form_new_feature mo_otp_new_feature_class" src="'.MOV_URL.'includes/images/mo_elementor_pro.jpg"></td>
            <td> <div class = "mo_otp_new_feature_class_note"> <b>Phone or Email Verification via OTP on Elementor PRO form.</b></div> </td>
            </tr>
            </table>
            </div>

            <div class="mo_otp_note">
            <table>
            <tr>
            <td> <img class = "mo_support_form_new_feature mo_otp_new_feature_class" src="'.MOV_URL.'includes/images/mo_aws_sns.png"></td>
            <td> <div class = "mo_otp_new_feature_class_note"> <b>Use AWS SNS as your Custom SMS gateway to send One Time Passcodes, Custom messages or SMS Notifications.</b> </div> </td>
            </tr>
            </table>
            </div>

               <div class="mo_otp_note">
            <table>
            <tr>
            <td> <img class = "mo_support_form_new_feature mo_otp_new_feature_class" src="'.MOV_URL.'includes/images/mo_user_reg.png"></td>
            <td> <div class = "mo_otp_new_feature_class_note"> <b>Phone or Email Verification via OTP on User Registration forms- WPEverest. </b></div> </td>
            </tr>
            </table>
            </div>

            <div class="mo_otp_note">
            <table>
            <tr>
            <td> <img class = "mo_support_form_new_feature mo_otp_new_feature_class" src="'.MOV_URL.'includes/images/mo_social_login.png"></td>
            <td> <div class = "mo_otp_new_feature_class_note"> <b>Support for OTP Verification to be initiated after login/registration through Social media. </b></div> </td>
            </tr>
            </table>
            </div>


                               </div>
                               </td>
                               </tr>

                </table>';


echo'       <table style="width:100%">
                <tr>
                    <td colspan="2">
                        <h2>'.mo_("FREQUENTLY ASKED QUESTIONS").'
                            <span style="float:right;margin-top:-10px;">
                                <span   class="mo-dashicons dashicons dashicons-arrow-down toggle-div" 
                                        data-show="false" 
                                        data-toggle="mo_form_instructions">                                            
                                </span>
                            </span>
                        </h2> <hr>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="mo_form_instructions" hidden>
                            <div class="mo_otp_note">
                                <b><div class="mo_otp_dropdown_note" data-toggle="how_to_use_the_otp_plugin">
                                    '.mo_('HOW DO I USE THE PLUGIN').'
                                    </div></b>
                                <div id="how_to_use_the_otp_plugin" hidden >
                                    '.mo_("By following these easy steps you can verify your users email or phone number instantly").':
                                    <ol>
                                        <li>'.mo_("Select the form from the list.");
echo                                     '<span class="tooltip">
                                                <span class="dashicons dashicons-editor-help"></span>
                                                <span class="tooltiptext">
                                                    <span class="header"><b><i>'.mo_esc_string(MoMessages::showMessage(MoMessages::FORM_NOT_AVAIL_HEAD),"attr").'</i></b></span><br/><br/>
                                                    <span class="body">We are actively adding support for more forms. Please contact us using the support form on your right or email us at <a onClick="otpSupportOnClick();""><font color="white"><u>'.mo_esc_string(MoConstants::FEEDBACK_EMAIL,"attr").'</u>.</font></a> While contacting us please include enough information about your registration form and how you intend to use this plugin. We will respond promptly.</span>
                                                </span>
                                              </span>';
echo'                                   </li>
                                        <li>'.mo_("Save your form settings from under the <i><a href='''#mo_forms'>Form Settings</a></i> section.").'</li>
                                        <li>'.mo_("To add a dropdown to your phone field or select a default country code check the ").'
                                            <i><a href="'.mo_esc_string($otpSettings,"url").'">'.mo_("OTP Settings Tab").'</a></i></li>
                                        <li>'.mo_("To customize your SMS/Email messages/gateway check under").'
                                           <i><a href="'.mo_esc_string($config,"url").'">'.mo_("SMS/Email Templates Tab").'</a></i></li>
                                        <li>'.mo_("Log out and go to your registration or landing page for testing.").'</li>
                                        <li>'.mo_("For any query related to custom SMS/Email messages/gateway check our").' 
                                           <i><a href="'.mo_esc_string($help_url,"url").'"> '.mo_("FAQs").'</a></i></li>
                                        <li>
                                            <div>
                                                '.mo_("Cannot see your registration form in the list above? Have your own custom registration form?"
                                                            ).'';
echo                                     '<span class="tooltip">
                                                <span class="dashicons dashicons-editor-help"></span>
                                                <span class="tooltiptext">
                                                    <span class="header"><b><i>'.mo_esc_string(MoMessages::showMessage(MoMessages::FORM_NOT_AVAIL_HEAD),"attr").'</i></b></span><br/><br/>
                                                    <span class="body">We are actively adding support for more forms. Please contact us using the support form on your right or email us at <a onClick="otpSupportOnClick();""><font color="white"><u>'.mo_esc_string(MoConstants::FEEDBACK_EMAIL,"attr").'</u>.</font></a> While contacting us please include enough information about your registration form and how you intend to use this plugin. We will respond promptly.</span>
                                                </span>
                                              </span>';
echo'                                       </div>
                                        </li>
                                        </i>
                                    </ol>
                                </div>
                            </div>
                            <div class="mo_otp_note">
                                <b><div class="mo_otp_dropdown_note" data-toggle="wp_dropdown">
                                    '.mo_('HOW DO I SHOW A COUNTRY CODE DROP-DOWN ON MY FORM?').'
                                    </div></b>
                                <div id="wp_dropdown" hidden >
                                    '.mo_( "To enable a country dropdown for your phone number field simply enable the option from the Country Code Settings under <i><a href='".mo_esc_string($otpSettings,"url")."'>OTP Settings Tab</a></i>").'
                                </div>
                            </div>
                             <div class="mo_otp_note">
                                <b><div class="mo_otp_dropdown_note" data-toggle="mo_payment_method">
                                 '.mo_('SUPPORTED PAYMENT METHODS FOR OTP VERIFICATION').'
                                    </div></b>
                                    <div id="mo_payment_method" hidden >
                                  ' .mo_("Two types of methods which we support;").'<br>
                                                 '.mo_("<b>A. Default Method:</b>").' 
                                                 <ul>
                                                  <li>'.mo_("Payment by Credit card/International debit card.").'</li>
                                                 <li>'.mo_("If payment is done through Credit Card/Intrnational debit card, the license would be made automatically once payment is completed. For guide <a href=".MoConstants::FAQ_PAY_URL.">Click Here.</a>").'</li>
                                                 </ul>
                                                 '.mo_("<b>B. Alternative Methods:</b>").'
                                                 <ol>
                                                 <li>'.mo_("<b>Paypal:</b>Use the following PayPal id for payment via PayPal.").'
                                                 '.mo_("<i style='color:#0073aa'>info@xecurify.com</i>").'</li>
                                                  <li>'.mo_("<b>Net Banking:</b>If you want to use net banking for payment then contact us at <i style='color:#0073aa'>".MoConstants::SUPPORT_EMAIL."</i> so that we will provide you bank details.").'</li>
                                                 </ol>
                                                 '.mo_("Once you Paid through any of the above methods, please inform us so that we can confirm and update your License.").'<br>
                                                 '.mo_("<b>Note:</b> There is an additional 18% GST applicable via PayPal and Bank Transfer.").'<br>
                                                 '.mo_("For more information about payment methods visit 
                                                 <i><a href=".MoConstants::FAQ_PAY_URL.">
                                                 Supported Payment Methods.</a></i>").'

                                    </div>
                            </div>
                            <div class="mo_otp_note">
                                <b><div class="mo_otp_dropdown_note" data-toggle="wp_sms_email_template">
                                    '.mo_('HOW DO I CHANGE THE BODY OF THE SMS AND EMAIL GOING OUT?').'
                                    </div></b>
                                <div id="wp_sms_email_template" hidden >
                                    '.mo_( "You can change the body of the SMS and Email going out to users by following instructions under the <i><a href='".mo_esc_string($config,"url")."'>SMS/Email Template Tab</a></i>").'
                                </div>
                            </div>
                            <div class="mo_otp_note">
                                <!--<div class="mo_corner_ribbon shadow">'.mo_("NEW").'</div>-->
                                <b><div class="mo_otp_dropdown_note notification" data-toggle="wc_sms_notif_addon">
                                    '.mo_('LOOKING FOR A WOOCOMMERCE OR ULTIMATE MEMBER SMS NOTIFICATION PLUGIN?').'
                                    </div></b>
                                <div id="wc_sms_notif_addon" hidden >
                                    '.mo_( "<b>Looking for a plugin that will send out SMS notifications to users and admin for WooCommerce or Ultimate Member? </b>We have a separate add-on for that. Check the <i><a href='".mo_esc_string($addon,"url")."'>AddOns Tab</a></i> for more information.").'
                                </div>
                            </div>
                            <div class="mo_otp_note">
                                <b><div class="mo_otp_dropdown_note" data-toggle="wp_sms_transaction_upgrade">
                                    '.mo_('HOW DO I BUY MORE TRANSACTIONS? HOW DO I UPGRADE?').'
                                    </div></b>
                                <div id="wp_sms_transaction_upgrade" hidden >
                                    '.mo_( "You can upgrade and recharge at any time. You can even configure any external SMS/Email gateway provider with the plugin. <i><a href='".mo_esc_string($license_url,"url")."'>Click Here</i></a> or the upgrade button on the top of the page to check our pricing and plans.").'
                                </div>
                            </div>
                            <div class="mo_otp_note">
                                <b><div class="mo_otp_dropdown_note" data-toggle="wp_design_custom">
                                    '.mo_('HOW DO I CHANGE THE DESIGN OF THE POPUP?').'
                                    </div></b>
                                <div id="wp_design_custom" hidden >
                                    '.mo_( "If you wish to change how the popup looks to match your sites look and feel then you can do so from the <i><a href='".mo_esc_string($design,"url")."'>PopUp Design Tab.</a></i>").'
                                </div>
                            </div>   
                            <div class="mo_otp_note">
                                <b><div class="mo_otp_dropdown_note" data-toggle="wp_sms_integration">
                                    '.mo_('NEED TO ENABLE OTP VERIFICATION ON A CUSTOM FORM?').'
                                    </div></b>
                                <div id="wp_sms_integration" hidden >
                                    '.mo_( "If you wish to integrate the plugin with your form then please contact us at <a onclick= 'otpSupportOnClick();'><i>".mo_esc_string($support,"url")."</i></a> or use the support form to send us a query.").'
                                </div>
                            </div>    
                            <div class="mo_otp_note">
                                <b><div class="mo_otp_dropdown_note" data-toggle="wp_reports">
                                    '.mo_('NEED TO TRACK TRANSACTIONS?').'
                                    </div></b>
                                <div id="wp_reports" hidden>
                                    <div >
                                        <b>'.mo_("Follow these steps to view your transactions:").'</b>
                                        <ol>
                                            <li>'.mo_("Click on the button below.").'</li>
                                            <li>'.mo_("Login using the credentials you used to register for this plugin.").'</li>
                                            <li>'.mo_("You will be presented with <i><b>View Transactions</b></i> page.").'</li>
                                            <li>'.mo_("From this page you can track your remaining transactions").'</li>
                                        </ol>
                                        <div style="margin-top:2%;text-align:center">
                                            <input  type="button" 
                                                    title="'.mo_("Need to be registered for this option to be available").'" 
                                                    value="'.mo_("View Transactions").'" 
                                                    onclick="extraSettings(\''.MoConstants::HOSTNAME.'\',\''.MoConstants::VIEW_TRANSACTIONS.'\');" 
                                                    class="button button - primary button - large" style="margin - right: 3%;">
                                        </div>
                                    </div>
                                    <form id="showExtraSettings" action="'.MoConstants::HOSTNAME.'/moas/login" target="_blank" method="post">
                                       <input type="hidden" id="extraSettingsUsername" name="username" value="'.mo_esc_string($email,"attr").'" />
                                       <input type="hidden" id="extraSettingsRedirectURL" name="redirectUrl" value="" />
                                       <input type="hidden" id="" name="requestOrigin" value="'.mo_esc_string($plan_type,"attr").'" />
                                    </form>
                                </div>
                            </div>                            
                        </div>
                    </td>
                </tr>
            </table></div>';













                


