<?php

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
	        <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
	                id="wc_billing" 
	                class="app_enable"  
					data-toggle="wc_billing_options" 
					name="mo_customer_validation_wc_billing_enable" 
					value="1"
					'.mo_esc_string($wc_billing_enable,"attr").' />
			<strong>'. mo_( "Woocommerce Billing Form" ).'</strong>';

echo'		<div class="mo_registration_help_desc" '.mo_esc_string($wc_billing_hidden,"attr").' id="wc_billing_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
				    <input  type="radio" '.mo_esc_string($disabled,"attr").' 
				            id="wc_billing_phone" 
				            class="app_enable" 
				            name="mo_customer_validation_wc_billing_type_enabled" 
				            value="'.mo_esc_string($wc_billing_type_phone,"attr").'"
						    '.(mo_esc_string($wc_billing_type_enabled,"attr") == mo_esc_string($wc_billing_type_phone,"attr") ? "checked" : "" ).' />
                    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<p>
				    <input  type="radio" '.mo_esc_string($disabled,"attr").' 
				            id="wc_billing_email" 
				            class="app_enable" 
				            name="mo_customer_validation_wc_billing_type_enabled" 
				            value="'.mo_esc_string($wc_billing_type_email,"attr").'"
						    '.(mo_esc_string($wc_billing_type_enabled,"attr") == mo_esc_string($wc_billing_type_email,"attr") ? "checked" : "" ).' />
                    <strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
				<p>
				    <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
				            name="mo_customer_validation_wc_billing_restrict_duplicates" 
				            value="1"
				            '.mo_esc_string($wc_restrict_duplicates,"attr").' />
                    <strong>'.
                        mo_( "Do not allow users to use the same Phone number or Email for multiple accounts." ).
                    '</strong>
				</p>
		</div></div>';