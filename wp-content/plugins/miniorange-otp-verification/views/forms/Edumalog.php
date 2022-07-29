<?php

echo'	<div class="mo_otp_form" id="'.get_mo_class($handler).'">'.
        '<input type="checkbox" 
                '.mo_esc_string($disabled,"attr").' 
                id="edumalog_default" 
                class="app_enable" 
                data-toggle="edumalog_options" 
                name="mo_customer_validation_edumalog_enable" 
                value="1"
	        '.mo_esc_string($edumalog_enabled,"attr").' />
        <strong>'. mo_esc_string($form_name,"attr") .'</strong>';

echo'		<div class="mo_registration_help_desc" '.mo_esc_string($edumalog_hidden,"attr").'
          id="edumalog_options">
				  <b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				  <p>
					 <input  type="radio" '.mo_esc_string($disabled,"attr").' id="edumalog_phone" class="app_enable" 
            data-toggle="edumalog_phone_options" name="mo_customer_validation_edumalog_enable_type" value="'.mo_esc_string($edumalog_type_phone,"attr").'" '.(mo_esc_string($edumalog_enabled_type,"attr") == mo_esc_string($edumalog_type_phone,"attr")  ? "checked" : "" ).'/>
          
            <strong>'. mo_( "Enable Phone Verification" ).'</strong>
          </p>
                  
          <div '.(mo_esc_string($edumalog_enabled_type,"attr") != mo_esc_string($edumalog_type_phone,"attr") ? "hidden" :"").' class="mo_registration_help_desc" id="edumalog_phone_options"">
            <p>
              '. mo_( "Enter the phone User Meta Key" );
echo '        <input    class="mo_registration_table_textbox"
                id="mo_customer_validation_wpf_login_phone_field_key"
                name="mo_customer_validation_edumalog_phone_field_key"
                type="text"
                value="'.mo_esc_string($edumalog_phone_field_key,"attr").'"> 

              <div class="mo_otp_note" style="margin-top:1%">
                '.mo_( "If you don't know the metaKey against which the phone number ".
                        "is stored for all your users then put the default value as telephone." ).'
              </div>                 
            </p>
          </div>
				  <p>
  				  <input  type="radio" '.mo_esc_string($disabled,"attr").' id="edumalog_email" class="app_enable" 
  					 name="mo_customer_validation_edumalog_enable_type" value="'.mo_esc_string($edumalog_type_email,"attr").'" '.(mo_esc_string($edumalog_enabled_type,"attr") == mo_esc_string($edumalog_type_email,"attr")? "checked" : "" ).'/>
            <strong>'. mo_( "Enable Email Verification" ).'</strong>
				  </p>
				  <p>
            <input  type="checkbox" '.mo_esc_string($disabled,"attr").' class="app_enable"
              data-toggle="mo_send_bypss_password"
              name="mo_customer_validation_edumalog_bypass_admin"
              value="1" '.mo_esc_string($edumalog_log_bypass,"attr").' />
            <strong>'. mo_( "Allow the administrator to bypass OTP verification during login." ).'</strong>
          </p>           
			  </div>
		  </div>';