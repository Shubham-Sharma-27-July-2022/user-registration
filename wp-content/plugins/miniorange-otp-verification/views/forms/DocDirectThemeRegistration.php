<?php

echo'	<div class="mo_otp_form" id="'.get_mo_class($handler).'">'.
            '<input type="checkbox" 
                    '.mo_esc_string($disabled,"attr").' 
                    id="docdirect_default" 
                    class="app_enable" 
                    data-toggle="docdirect_options" 
                    name="mo_customer_validation_docdirect_enable" 
                    value="1"
			        '.mo_esc_string($docdirect_enabled,"attr").' />
            <strong>'. mo_esc_string($form_name,"attr") .'</strong>';

echo'		<div class="mo_registration_help_desc" '.mo_esc_string($docdirect_hidden,"attr").' id="docdirect_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
					<input  type="radio" 
					        '.mo_esc_string($disabled,"attr").' 
					        id="docdirect_phone" 
					        class="app_enable" 
					        data-toggle="docdirect_phone_options" 
					        name="mo_customer_validation_docdirect_enable_type" 
					        value="'.mo_esc_string($docdirect_type_phone,"attr").'"
						    '.(mo_esc_string($docdirect_enabled_type,"attr") == mo_esc_string($docdirect_type_phone,"attr")  ? "checked" : "" ).'/>
                    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<p>
					<input  type="radio" '.mo_esc_string($disabled,"attr").' 
					        id="docdirect_email" 
					        class="app_enable" 
					        name="mo_customer_validation_docdirect_enable_type" 
					        value="'.mo_esc_string($docdirect_type_email,"attr").'"
						    '.(mo_esc_string($docdirect_enabled_type,"attr") == mo_esc_string($docdirect_type_email,"attr")? "checked" : "" ).'/>
                    <strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
			</div>
		</div>';