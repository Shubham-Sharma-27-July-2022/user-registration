<?php

use OTP\Helper\MoMessages;

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'"><input type="checkbox" '.mo_esc_string($disabled,"attr").' id="simplr_default" data-toggle="simplr_default_options" class="app_enable" name="mo_customer_validation_simplr_default_enable" value="1"
				'.mo_esc_string($simplr_enabled,"attr").' /><strong>'.mo_esc_string($form_name,"attr").'</strong>';

echo'			<div class="mo_registration_help_desc" '.mo_esc_string($simplr_hidden,"attr").' id="simplr_default_options">
					<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
						<p><input type="radio" '.mo_esc_string($disabled,"attr").' data-toggle="simplr_phone_instruction" id="simplr_phone" class="form_options app_enable" name="mo_customer_validation_simplr_enable_type" value="'.mo_esc_string($simplr_type_phone,"attr").'"
							'.( mo_esc_string($simplr_enabled_type,"attr") == mo_esc_string($simplr_type_phone,"attr") ? "checked" : "" ).' />
								<strong>'. mo_( "Enable Phone Verification" ).'</strong>';

echo'						<div '.(mo_esc_string($simplr_enabled_type,"attr")!= mo_esc_string($simplr_type_phone,"attr") ? "hidden" : "").' id="simplr_phone_instruction" class="mo_registration_help_desc">
								'. mo_( "Follow the following steps to enable Phone Verification" ).':
								<ol>
									<li><a href="'.mo_esc_string($simplr_fields_page,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'</li>
									<li>'. mo_( "Add a new Phone Field by clicking the <b>Add Field</b> button." ).'</li>
									<li>'. mo_( "Give the <b>Field Name</b> and <b>Field Key</b> for the new field. Remember the Field Key as you will need it later." ).'</li>
									<li>'. mo_( "Click on <b>Add Field</b> button at the bottom of the page to save your new field." ).'</li>
									<li><a href="'.mo_esc_string($page_list,"url").'" target="_blank	">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of pages" ).'</li>
									<li>'. mo_( "Click on the <b>Edit</b> link of your page to modify it." ).'</li>
									<li>'. mo_( "In the ShortCode add the following attribute" ).': <b>fields="{Field Key you provided in Step 2}"</b>. '. mo_( "If you already have the fields attribute defined then just add the new field key to the list." ).'</li>
									<li>'. mo_( "Click on <b>update</b> to save your page." ).'</li>
									<li>'. mo_( "Enter the Field Key of the phone field" ).':<input class="mo_registration_table_textbox" id="mo_customer_validation_simplr_phone_field_key1" name="mo_customer_validation_simplr_phone_field_key" type="text" value="'.mo_esc_string($simplr_field_key,"attr").'"></li>
								</ol>
							</div>
							</p>
							<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="simplr_email" class="form_options app_enable" name="mo_customer_validation_simplr_enable_type" value="'.mo_esc_string($simplr_type_email,"attr").'"
									'.( mo_esc_string($simplr_enabled_type,"attr") == mo_esc_string($simplr_type_email,"attr") ? "checked" : "").' />
									<strong>'. mo_( "Enable Email Verification" ).'</strong>
							</p>
							<p><input type="radio" '.mo_esc_string($disabled,"attr").' data-toggle="simplr_both_instruction" id="simplr_both" class="form_options app_enable" name="mo_customer_validation_simplr_enable_type" value="'.mo_esc_string($simplr_type_both,"attr").'"
									'.( mo_esc_string($simplr_enabled_type,"attr") == mo_esc_string($simplr_type_both,"attr") ? "checked" : "").' />
									<strong>'. mo_( "Let the user choose" ).'</strong>';

									mo_draw_tooltip(
									    MoMessages::showMessage(MoMessages::INFO_HEADER),
                                        MoMessages::showMessage(MoMessages::ENABLE_BOTH_BODY)
                                    );

echo'							<div '.(mo_esc_string($simplr_enabled_type,"attr") != mo_esc_string($simplr_type_both,"attr") ? "hidden" : "").' id="simplr_both_instruction" class="mo_registration_help_desc">
									'. mo_( "Follow the following steps to enable Email and Phone Verification" ).':
									<ol>
										<li><a href="'.mo_esc_string($simplr_fields_page,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'
										<li>'. mo_( "Add a new Phone Field by clicking the <b>Add Field</b> button." ).'</li>
										<li>'. mo_( "Give the <b>Field Name</b> and <b>Field Key</b> for the new field. Remember the Field Key as you will need it later." ).'</li>
										<li>'. mo_( "Click on <b>Add Field</b> button at the bottom of the page to save your new field." ).'</li>
										<li><a href="'.mo_esc_string($page_list,"url").'" target="_blank	">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of pages" ).'</li>
										<li>'. mo_( "Click on the <b>Edit</b> link of your page to modify it." ).'</li>
										<li>'. mo_( "In the ShortCode add the following attribute" ).': <b>fields="{Field Key you provided in Step 2}"</b>. '. mo_( "If you already have the fields attribute defined then just add the new field key to the list." ).'</li>
										<li>'. mo_( "Click on <b>update</b> to save your page." ).'</li>
										<li>'. mo_( "Enter the Field Key of the phone field" ).': <input class="mo_registration_table_textbox" id="mo_customer_validation_simplr_phone_field_key2" name="mo_customer_validation_simplr_phone_field_key" type="text" value="'.mo_esc_string($simplr_field_key,"attr").'"></li>
									</ol>
								</div>
							</p>
						</div>
					</div>';