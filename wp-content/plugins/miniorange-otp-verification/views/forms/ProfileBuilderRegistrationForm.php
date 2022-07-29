<?php

use OTP\Helper\MoMessages;

echo' 	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'"><input type="checkbox" '.mo_esc_string($disabled,"attr").' id="pb_default" class="app_enable" name="mo_customer_validation_pb_default_enable" value="1" data-toggle="pb_default_options"
			'.mo_esc_string($pb_enabled,"attr").' /><strong>'. mo_esc_string($form_name,"attr") .'</strong>';

	echo'	<div class="mo_registration_help_desc" '.mo_esc_string($pb_hidden,"attr").' id="pb_default_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
					<input type="radio" '.mo_esc_string($disabled,"attr").' id="pb_phone" class="app_enable" data-toggle="pb_phone_options" name="mo_customer_validation_pb_enable_type" value="'.mo_esc_string($pb_reg_type_phone,"attr").'"
						'.(mo_esc_string($pb_enable_type,"attr") == mo_esc_string($pb_reg_type_phone,"attr") ? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Phone Verification" ).' <i>'.  mo_( "( Requires Hobbyist Version )" ) . '</i></strong>
				</p>
				<div '.(mo_esc_string($pb_enable_type,"attr") != mo_esc_string($pb_reg_type_phone,"attr") ? "hidden" :"").' id="pb_phone_options" class="pb_form mo_registration_help_desc" >
					<ol>
						<li><a href="'.mo_esc_string($pb_fields,"url").'"  target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'</li>
						<li>'. mo_( "Choose a phone field from the Field Dropdown" ).'</li>
						<li>'. mo_( "Keep track of the Meta Name of the phone field as you will need it later on." ).'</li>
						<li>'. mo_( "Make sure to mark the phone field as required." ).'</li>
						<li>'. mo_( "Enter the meta name of your phone field" ).': <input class="mo_registration_table_textbox" id="mo_customer_validation_pb_phone_field_key" name="mo_customer_validation_pb_phone_field_key" type="text" value="'.mo_esc_string($pb_phone_key,"attr").'"></li>
					</ol>
				</div>
				<p>
					<input type="radio" '.mo_esc_string($disabled,"attr").' id="pb_email" class="app_enable" name="mo_customer_validation_pb_enable_type" value="'.mo_esc_string($pb_reg_type_email,"attr").'"
						'.(mo_esc_string($pb_enable_type,"attr") == mo_esc_string($pb_reg_type_email,"attr")? "checked" : "" ).'/>
						<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
				<p>
					<input type="radio" '.mo_esc_string($disabled,"attr").' id="pb_both" class="app_enable" name="mo_customer_validation_pb_enable_type" data-toggle="pb_both_options"
						value="'.mo_esc_string($pb_reg_type_both,"attr").'" '.(mo_esc_string($pb_enable_type,"attr") == mo_esc_string($pb_reg_type_both,"attr")? "checked" : "" ).'/>
						<strong>'. mo_( "Let the user choose" ).'</strong>';
							mo_draw_tooltip(
							    MoMessages::showMessage(MoMessages::INFO_HEADER),MoMessages::showMessage(MoMessages::ENABLE_BOTH_BODY)
                            );
echo '			</p>
				<div '.(mo_esc_string($pb_enable_type,"attr") != mo_esc_string($pb_reg_type_both,"attr") ? "hidden" :"").' id="pb_both_options" class="pb_form mo_registration_help_desc" >
					<ol>
						<li><a href="'.mo_esc_string($pb_fields,"url").'"  target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'</li>
						<li>'. mo_( "Choose a phone field from the Field Dropdown" ).'</li>
						<li>'. mo_( "Keep track of the Meta Name of the phone field as you will need it later on." ).'</li>
						<li>'. mo_( "Make sure to mark the phone field as required." ).'</li>
						<li>'. mo_( "Enter the meta name of your phone field" ).': <input class="mo_registration_table_textbox" id="mo_customer_validation_pb_phone_field_key1" name="mo_customer_validation_pb_phone_field_key" type="text" value="'.mo_esc_string($pb_phone_key,"attr").'"></li>
					</ol>
				</div>
			</div>';

echo' 	</div>';