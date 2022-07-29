<?php

use OTP\Helper\MoMessages;

echo'	<div class="mo_otp_form" id="'.esc_attr(get_mo_class($handler)).'">
            <input  type="checkbox" '.esc_attr($disabled).'
                    id="wp_login"
                    class="app_enable"
                    data-toggle="wp_login_options"
                    name="mo_customer_validation_wp_login_enable"
                    value="1"
                    '.esc_attr($wp_login_enabled).' />
            <strong>'.esc_attr($form_name).'</strong>';

echo'	    <div class="mo_registration_help_desc" '.esc_attr($wp_login_hidden).' id="wp_login_options">
    
                 <p>
                    <input  type="radio" '.esc_attr($disabled).'
                            id="wp_form_phone"
                            class="app_enable"
                            data-toggle="wp_phone_option"
                            name="mo_customer_validation_wp_login_enable_type"
                            value="'.esc_attr($wp_phone_type).'"
                            '.( esc_attr($wp_enabled_type) == esc_attr($wp_phone_type) ? "checked" : "").' />
                    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
                 </p>
                 <div   '.(esc_attr($wp_enabled_type) != esc_attr($wp_phone_type) ? "hidden" :"").'
                        class="mo_registration_help_desc"
                        id="wp_phone_option" '.esc_attr($disabled).'">
                    '. mo_( "Follow the following steps to add a users phone number in the database" ).':
                    <ol>
                        <li>'. mo_( "Enter the phone User Meta Key" );

                            mo_draw_tooltip(
                                MoMessages::showMessage(MoMessages::META_KEY_HEADER),
                                MoMessages::showMessage(MoMessages::META_KEY_BODY)
                            );

echo'					    : <input    class="mo_registration_table_textbox"
                                        id="mo_customer_validation_wp_login_phone_field_key"
                                        name="mo_customer_validation_wp_login_phone_field_key"
                                        type="text"
                                        value="'.esc_attr($wp_login_field_key).'">
                            <div class="mo_otp_note" style="margin-top:1%">
                                '.mo_( "If you don't know the metaKey against which the phone number ".
                                        "is stored for all your users then put the default value as phone." ).'
                            </div>
                        </li>                            
                        <li>'. mo_( "Click on the Save Button to save your settings." ).'</li>
                    </ol>

                    <input  type="checkbox" '.esc_attr($disabled).' id="wp_login_reg" 
                            name="mo_customer_validation_wp_login_register_phone" value="1"
                        '.esc_attr($wp_login_enabled_type) .' />'.
                        '<strong>'.
                            mo_( "Allow the user to add a phone number if it does not exist." ).
                        '</strong>
                    <p>
                        <input  type="checkbox" '.esc_attr($disabled).'
                                id="wp_login_admin"
                                name="mo_customer_validation_wp_login_allow_phone_login"
                                value="1"
                                class="app_enable"
                                data-toggle="wp_change_labels"
                                '.esc_attr($wp_login_with_phone).' /><strong>'.
                                mo_( "Allow users to login with their phone number." ).'</strong>
                        <div    '.( !$wp_login_with_phone ? "hidden":"").'
                                id="wp_change_labels"
                                class="mo_registration_help_desc">
                            <p style="margin-left:2%;">
                                <i><b>'.mo_("Username Field text").' : </b></i>
                                    <input  class="mo_registration_table_textbox"
                                            name="mo_customer_validation_wp_username_label_text"
                                            type="text"
                                            value="'.esc_attr($user_field_text).'">
                            </p>
                        </div>
                    </p>
                    <input  type="checkbox" '.esc_attr($disabled).'
                            id="wp_login_admin"
                            name="mo_customer_validation_wp_login_restrict_duplicates"
                            value="1"
                            '.esc_attr($wp_handle_duplicates).' />
                    <strong>'. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'</strong>
                  </div>

                <p>
                    <input  type="radio" '.esc_attr($disabled).'
                            id="wp_form_email"
                            class="app_enable"
                            data-toggle="wp_email_option"
                            name="mo_customer_validation_wp_login_enable_type"
                            value="'.esc_attr($wp_email_type).'"
                            '.( esc_attr($wp_enabled_type) == esc_attr($wp_email_type) ? "checked" : "").' />
                    <strong>'. mo_( "Enable Email Verification" ).'</strong>
                </p>
                <p>
                    <input  type="checkbox" '.esc_attr($disabled).'
                            id="wp_login_admin"
                            name="mo_customer_validation_wp_login_bypass_admin"
                            value="1"
                            '.esc_attr($wp_login_admin).' />
                    <strong>'. mo_( "Allow the administrator to bypass OTP verification during login." ).'</strong>
                </p>
                <p>
                    <p>
                        <input  type="checkbox" '.esc_attr($disabled).'
                                id="otp_skip_pass"
                                class="app_enable"
                                data-toggle="otp_skip_pass_fallback_div"
                                name="mo_customer_validation_wp_login_skip_password"
                                value="1" '.esc_attr($skip_pass).' />
                        <strong>'. mo_( "Skip password and use OTP instead." ).'</strong>
                    </p>
                    <div class="mo_registration_help_desc" '.esc_attr($skip_pass_fallback_div).' id="otp_skip_pass_fallback_div">
                        <input  type="checkbox" '.esc_attr($disabled).'
                                id="otp_skip_pass_fallback"
                                name="mo_customer_validation_wp_login_skip_password_fallback"
                                value="1" '.esc_attr($skip_pass_fallback).' />
                        <strong>'. mo_( "Allow users to fallback to Username and Password" ).'</strong>
                    </div>
                </p>
                <p>
                     <p>
                        <input  type="checkbox" '.esc_attr($disabled).'
                                id="wp_login_delay_otp"
                                class="app_enable"
                                data-toggle="otp_delay_time_interval"
                                name="mo_customer_validation_wp_login_delay_otp"
                                value="1" '.esc_attr($otpd_enabled).' />
                        <strong>'. mo_( "Delay OTP Verification" ).'</strong>
                    </p>
                    <div class="mo_registration_help_desc" '.esc_attr($otpd_enabled_div).' id="otp_delay_time_interval">
                        <p style="margin-left:2%;">
                            <table>
                                <tr>
                                    <td>
                                        <i><b>'.mo_("Enter the interval after which you ".
                                                    "wish for OTP Verification to get invoked for the user").
                                        ' : </b></i>
                                    </td>
                                    <td style="width:50%">
                                        <input  class="mo_registration_table_textbox"
                                                name="mo_customer_validation_wp_login_delay_otp_interval"
                                                type="text"
                                                value="'.esc_attr($otpd_time_interval).'"> <b>( in mins )</b>
                                    </td>
                                </tr>    
                            </table>
                        </p>
                    </div>
                </p>
            </div>
         </div>';


