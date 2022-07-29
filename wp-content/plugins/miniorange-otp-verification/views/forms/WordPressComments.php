<?php

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
	        <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
	                id="wpcomment" 
	                class="app_enable" 
	                data-toggle="wpcomment_options" 
	                name="mo_customer_validation_wpcomment_enable" 
	                value="1"
			        '.mo_esc_string($wpcomment_enabled,"attr").' />
            <strong>'.mo_esc_string($form_name,"attr").'</strong>';

echo'		<div class="mo_registration_help_desc" '.mo_esc_string($wpcomment_hidden,"attr").' id="wpcomment_options">
				<p>
					<input  type="checkbox" 
					        class="form_options" '.mo_esc_string($wpComment_skip_verify,"attr").' 
					        id="mo_customer_validation_wpcomment_enable_for_loggedin_users" 
					        name="mo_customer_validation_wpcomment_enable_for_loggedin_users" 
					        value="1"> 
                    <strong>'. mo_('Skip OTP Verification for Logged In users.' ).'</strong><br>
                    <i>( '.mo_('Enabling this feature, logged in users are not required to verify.' ). ')</i>
				</p>
				
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				
				<p>
					<input  type="radio" '.mo_esc_string($disabled,"attr").' 
					        id="wpcomment_phone" 
					        class="app_enable" 
					        name="mo_customer_validation_wpcomment_enable_type" 
					        value="'.mo_esc_string($wpcomment_type_phone,"attr").'"
						    '.mo_esc_string(($wpcomment_type == $wpcomment_type_phone  ? "checked" : "" ),"attr").'/>
                    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				
				<p>
					<input  type="radio" '.mo_esc_string($disabled,"attr").' 
					        id="wpcomment_email" 
					        class="app_enable" 
					        name="mo_customer_validation_wpcomment_enable_type" 
					        value="'.mo_esc_string($wpcomment_type_email,"attr").'"
						    '.mo_esc_string(($wpcomment_type == $wpcomment_type_email? "checked" : "" ),"attr").'/>
                    <strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
			</div>
		</div>';