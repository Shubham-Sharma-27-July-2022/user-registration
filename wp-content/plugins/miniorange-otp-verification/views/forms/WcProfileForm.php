<?php

use OTP\Helper\MoMessages;

echo'       <div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
                    <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
                            id="wc_ac_default" 
                            data-toggle="wc_ac_default_options" 
                            class="app_enable" 
                            name="mo_customer_validation_wc_profile_enable" 
                            value="1" '.mo_esc_string($wc_acc_enabled,"attr").' />
                    <strong>'. mo_esc_string($form_name,"attr") . '</strong>';

echo'           <div class="mo_registration_help_desc" 
                     '.mo_esc_string($wc_acc_hidden,"attr").' 
                     id="wc_ac_default_options">        
                                <p>
                                <input  type ="radio" '.mo_esc_string($disabled,"attr").' 
                                id ="wc_profile_page" 
                                class="app_enable" 
                                name = "mo_customer_validation_wc_profile_enable_type" 
                                value= "'.mo_esc_string($wc_acc_type_email,"attr").'"
                                data-toggle="wc_profile_email_instructions" 
                                '.(mo_esc_string($wc_acc_enabled_type,"attr") === mo_esc_string($wc_acc_type_email,"attr")  ? "checked" : "" ).'/>
                            <strong>'. mo_('Email Verification') . '</strong>
                            <i>'.mo_("( On change of Email Address )").'</i>
                        </p>
                        <p>
                            <input  type ="radio" '.mo_esc_string($disabled,"attr").' 
                                    id ="wc_profile_page" 
                                    class="app_enable" 
                                    name = "mo_customer_validation_wc_profile_enable_type" 
                                    value= "'.mo_esc_string($wc_acc_type_phone,"attr").'"
                                    data-toggle="wc_profile_phone_instructions" 
                                    '.(mo_esc_string($wc_acc_enabled_type,"attr") === mo_esc_string($wc_acc_type_phone,"attr")  ? "checked" : "" ).'/>
                            <strong>'. mo_('Phone Verification') . '</strong> 
                            <i>'.mo_("(On change of mobile number)").'</i>                           
                            <div    '.(mo_esc_string($wc_acc_enabled_type,"attr") != mo_esc_string($wc_acc_type_phone,"attr") ? "hidden" : "").' 
                                    id="wc_profile_phone_instructions" 
                                    class="mo_registration_help_desc">
                                '. mo_( "Follow the following steps to enable OTP Verification and save the user phone number in the database" ).':
                                
                                <ol>
                                    <li>'. mo_( "Enter the phone User Meta Key" );

                                    mo_draw_tooltip(
                                        MoMessages::showMessage(MoMessages::META_KEY_HEADER),
                                        MoMessages::showMessage(MoMessages::META_KEY_BODY)
                                    );

    echo'                           : <input    class="mo_registration_table_textbox"
                                                id="mo_customer_validation_wc_profile_phone_key_1_0"
                                                name="mo_customer_validation_wc_profile_phone_key"
                                                type="text"
                                                value="'.mo_esc_string($wc_profile_field_key,"attr").'">
                                    <div class="mo_otp_note">
                                        '.mo_( "If you don't know the metaKey against which the phone ".
                                                "number is stored for all your users then put the default value as phone." ).'
                                    </div>
                                    <li>'. mo_( "Click on the Save Button to save your settings." ).'</li>
                                </ol>
                            
                                <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
                                        id="wc_profile_admin" 
                                        name="mo_customer_validation_wc_profile_restrict_duplicates"    
                                        value="1"
                                        '.mo_esc_string($wc_acc_restrict_duplicates,"attr").' />
                                <strong>
                                    '. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'
                                </strong>
                            </div>                                
                        </p>
                        <p>
                             <i><b>'.mo_("Verification Button text").':</b></i>
                             <input class="mo_registration_table_textbox" 
                                    name="mo_customer_validation_wc_profile_button_text" 
                                    data-toggle="wc_both_instructions"
                                    type="text" 
                                    value="'.mo_esc_string($wc_acc_button_text,"attr").'">
                        </p>
                            
                </div>
            </div>';