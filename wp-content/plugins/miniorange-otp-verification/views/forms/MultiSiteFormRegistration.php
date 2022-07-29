<?php

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'"><input type="checkbox" '.mo_esc_string($disabled,"attr").' id="multisite_reg" class="app_enable" data-toggle="multisite_options" name="mo_customer_validation_multisite_enable" value="1"
			'.mo_esc_string($multisite_enabled,"attr").' /><strong>'. mo_esc_string($form_name,"attr") .'</strong>';


echo'		<div class="mo_registration_help_desc" '.mo_esc_string($multisite_hidden,"attr").' id="multisite_options">
				<b>Choose between Phone or Email Verification</b>
				<p>
					<input type="radio" '.mo_esc_string($disabled,"attr").' id="multisite_phone" class="app_enable" name="mo_customer_validation_multisite_contact_type" value="'.mo_esc_string($multisite_type_phone,"attr").'"
						'.(mo_esc_string($multisite_enabled_type,"attr") == mo_esc_string($multisite_type_phone,"attr") ? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Phone Verification" ).'</strong>


				</p>
				<p>
					<input type="radio" '.mo_esc_string($disabled,"attr").' id="multisite_email" class="app_enable" name="mo_customer_validation_multisite_contact_type" value="'.mo_esc_string($multisite_type_email,"attr").'"
						'.(mo_esc_string($multisite_enabled_type,"attr") == mo_esc_string($multisite_type_email,"attr")? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
			</div>
		</div>';
