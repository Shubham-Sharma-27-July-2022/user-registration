<?php

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
	        <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
	                id="wc_pv_default" 
	                data-toggle="wc_pv_default_options" 
	                class="app_enable" 
	                name="mo_customer_validation_wc_pv_default_enable" 
	                value="1"
		            '.mo_esc_string($wc_pv_registration,"attr").' />
            <strong>'.mo_esc_string($form_name,"attr").'</strong>';

echo'		<div class="mo_registration_help_desc" '.mo_esc_string($wc_pv_hidden,"attr").' id="wc_pv_default_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
					<input  type="radio" '.mo_esc_string($disabled,"attr").' 
					        id="wc_pv_phone" 
					        class="app_enable" 
					        data-toggle="wc_pv_phone_options" 
					        name="mo_customer_validation_wc_pv_enable_type" 
					        value="'.mo_esc_string($wc_pv_reg_type_phone,"attr").'"
						    '.(mo_esc_string($wc_pv_enable_type,"attr") == mo_esc_string($wc_pv_reg_type_phone,"attr") ? "checked" : "" ).'/>
				    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<div '.(mo_esc_string($wc_pv_enable_type,"attr") != mo_esc_string($wc_pv_reg_type_phone,"attr")  ? "hidden" :"").' 
				        class="mo_registration_help_desc" 
						id="wc_pv_phone_options" >
						<input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
						        name="mo_customer_validation_wc_pv_restrict_duplicates" value="1"
								'.mo_esc_string($wc_pv_restrict_duplicates,"attr").' />
                        <strong>'. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'</strong>
				</div>
				<p>
					<input  type="radio" '.mo_esc_string($disabled,"attr").' 
					        id="wc_pv_email" 
					        class="app_enable" 
					        name="mo_customer_validation_wc_pv_enable_type" 
					        value="'.mo_esc_string($wc_pv_reg_type_email,"attr").'"
						    '.(mo_esc_string($wc_pv_enable_type,"attr") == mo_esc_string($wc_pv_reg_type_email,"attr") ? "checked" : "" ).'/>
					<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
			</div>
		</div>';