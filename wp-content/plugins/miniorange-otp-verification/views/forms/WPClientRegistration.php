<?php

use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;

echo'		<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
		        <input  type="checkbox" 
		                '.mo_esc_string($disabled,"attr").' 
		                id="wp_client" 
		                class="app_enable" 
		                data-toggle="wp_client_options" 
		                name="mo_customer_validation_wp_client_enable" value="1"
		                '.mo_esc_string($wp_client_enabled,"attr").' />
                <strong>'. mo_esc_string($form_name,"attr") .'</strong>
                <div class="mo_registration_help_desc" '.mo_esc_string($wp_client_hidden,"attr").' id="wp_client_options">
					
					<b>'. mo_("Choose between Phone or Email Verification").'</b>
					<p>
					    <input  type="radio" 
					            '.mo_esc_string($disabled,"attr").' 
					            data-toggle="wp_client_phone_instructions" 
					            id="wp_client_phone" 
					            class="form_options app_enable" 
						        name="mo_customer_validation_wp_client_enable_type" 
						        value="'.mo_esc_string($wp_client_type_phone,"attr").'"
							    '.( mo_esc_string($wp_client_enable_type,"attr") == mo_esc_string($wp_client_type_phone,"attr") ? "checked" : "").' />
                        <strong>'. mo_("Enable Phone verification").'</strong>
						
						<div    '.(mo_esc_string($wp_client_enable_type,"attr") != mo_esc_string($wp_client_type_phone,"attr") ? "hidden" : "").' 
						        id="wp_client_phone_instructions" 
						        class="mo_registration_help_desc">
                                <input  type="checkbox" 
                                        '.mo_esc_string($disabled,"attr").' 
                                        id="mo_customer_validation_wp_client_restrict_duplicates" 
                                        name="mo_customer_validation_wp_client_restrict_duplicates" 
                                        value="1"
                                        '.mo_esc_string($restrict_duplicates,"attr").'/>
                                <strong>'. mo_( "Restrict Duplicate phone number to sign up." ).'</strong>
						</div>
					</p>
					<p>
					    <input  type="radio" 
					            '.mo_esc_string($disabled,"attr").' 
					            id="wp_client_email" 
					            class="form_options app_enable" 
						        name="mo_customer_validation_wp_client_enable_type" 
						        value="'.mo_esc_string($wp_client_type_email,"attr").'"
						        '.( mo_esc_string($wp_client_enable_type,"attr") == mo_esc_string($wp_client_type_email,"attr") ? "checked" : "" ).' />
						<strong>'. mo_("Enable Email verification").'</strong>
					</p>
				</div>
			</div>';