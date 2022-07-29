<?php

use OTP\Helper\MoMessages;

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
	        <input  type="checkbox" 
	                '.mo_esc_string($disabled,"attr").' 
	                id="wp_member_reg" 
	                class="app_enable" 
	                data-toggle="wp_member_reg_options" 
	                name="mo_customer_validation_wp_member_reg_enable" 
	                value="1"
	                '.mo_esc_string($wp_member_reg_enabled,"attr").' />
            <strong>'.mo_esc_string($form_name,"attr"). '</strong>';

echo'	    <div class="mo_registration_help_desc" '.mo_esc_string($wp_member_reg_hidden,"attr").' id="wp_member_reg_options">
				<p>
				    <input  type="radio" 
				            '.mo_esc_string($disabled,"attr").' 
				            id="wpmembers_reg_phone" 
				            class="app_enable" 
				            data-toggle="wpmembers_reg_phone_instructions" 
				            name="mo_customer_validation_wp_member_reg_enable_type" 
				            value="'.mo_esc_string($wpm_type_phone,"attr").'"
					        '.( mo_esc_string($wpmember_enabled_type,"attr") == mo_esc_string($wpm_type_phone,"attr") ? "checked" : "").' />
                    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>								
				<div '.(mo_esc_string($wpmember_enabled_type,"attr") != mo_esc_string($wpm_type_phone,"attr") ? "hidden" :"").' 
				     class="mo_registration_help_desc" 
				     id="wpmembers_reg_phone_instructions">			
					'. mo_( "Follow the following steps to enable Phone Verification for WP Member" ).':
					<ol>
						<li>
						    <a href="'.mo_esc_string($wpm_field_list,"attr").'" target="_blank">'. mo_( "Click Here" ).'</a> '.
                            mo_( "to see your list of the fields." ).'
                        </li>
						<li>'. mo_( "Enable the Phone field for your form and keep it required. Note the Phone Field Meta Key." ).'</li>
						<li>'. mo_( "Create a new text field with meta key <i>validate_otp</i> where users can enter the validation code." ).'</li>
						<li>'. mo_( "Enter the Phone Field Meta Key" );

                                mo_draw_tooltip(
                                    MoMessages::showMessage(MoMessages::META_KEY_HEADER),
                                    MoMessages::showMessage(MoMessages::META_KEY_BODY)
                                );

echo'					        : <input    class="mo_registration_table_textbox"
                                            id="mo_customer_validation_wp_member_reg_phone_field_key"
                                            name="mo_customer_validation_wp_member_reg_phone_field_key"
                                            type="text"
                                            value="'.mo_esc_string($wpmember_field_key,"attr").'">
                        </li>
						<li>'. mo_( "Click on the Save Button to save your settings." ).'</li>						
					</ol>
				</div>
									
				<p>
				    <input  type="radio" 
				            '.mo_esc_string($disabled,"attr").' 
				            id="wpmembers_reg_email" 
				            class="app_enable" 
				            data-toggle="wpmembers_reg_email_instructions" 
				            name="mo_customer_validation_wp_member_reg_enable_type" 
				            value="'.mo_esc_string($wpm_type_email,"attr").'"
					        '.( mo_esc_string($wpmember_enabled_type,"attr") == mo_esc_string($wpm_type_email,"attr") ? "checked" : "").' />
					<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
										
                <div '.(mo_esc_string($wpmember_enabled_type,"attr") != mo_esc_string($wpm_type_email,"attr") ? "hidden" :"").' 
                     class="mo_registration_help_desc" 
                     id="wpmembers_reg_email_instructions">			
                        '. mo_( "Follow the following steps to enable Email Verification for WP Member" ).':
                        <ol>
                            <li>
                                <a href="'.mo_esc_string($wpm_field_list,"attr").'" target="_blank">'. mo_( "Click Here" ).'</a> '.
                                    mo_( "to see your list of fields." ).'
                            </li>
                            <li>'. mo_( "Create a new text field with meta key <i>validate_otp</i> where users can enter the validation code." ).'</li>
                            <li>'. mo_( "Click on the Save Button to save your settings." ).'</li>
                        </ol>
                </div>					
            </div>
        </div>';
