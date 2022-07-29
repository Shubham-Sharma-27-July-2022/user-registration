<?php

echo'			<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'"><input type="checkbox" '.mo_esc_string($disabled,"attr").' id="ultimatepro" class="app_enable" data-toggle="ultipro_options" name="mo_customer_validation_ultipro_enable" value="1"
										'.mo_esc_string($ultipro_enabled,"attr").' /><strong>'. mo_esc_string($form_name,"attr") . '</strong>';


echo'							<div class="mo_registration_help_desc" '.mo_esc_string($ultipro_hidden,"attr").' id="ultipro_options">
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="ultipro_email" class="app_enable" data-toggle="ultipro_email_instructions" name="mo_customer_validation_ultipro_type" value="'.mo_esc_string($umpro_type_email,"attr").'"
										'.( mo_esc_string($ultipro_enabled_type,"attr") == mo_esc_string($umpro_type_email,"attr") ? "checked" : "").' />
											<strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>
									
										<div '.(mo_esc_string($ultipro_enabled_type,"attr") != mo_esc_string($umpro_type_email,"attr") ? "hidden" :"").' class="mo_registration_help_desc" id="ultipro_email_instructions" >
											'. mo_( "Follow the following steps to enable Email Verification for Ultimate membership Pro Form" ).': 
											<ol>
												<li><a href="'.mo_esc_string($page_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of pages" ).'</li>
												<li>'. mo_( "Click on the <b>Edit</b> option of the page which has your Ultimate membership Pro registration form" ).'</li>
												<li>'. mo_( "Add the following short code just below the given registration shortcode" ).': <code>[mo_email]</code> </li>
												<li><a href="'.mo_esc_string($umpro_custom_field_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( "to see the list of your custom fields." ).'</li>
												<li>'. mo_( "Add a custom text field with slug \"validate\" and label \"Enter Validation Code\" in your registration page.Use this text field to enter the OTP received. Make sure it's a required field." ).'</li>								
												<li>'. mo_( "Click on the Save Button to save your settings." ).'</li>
											</ol>
									</div>
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="ultipro_phone" class="app_enable" data-toggle="ultipro_phone_instructions" name="mo_customer_validation_ultipro_type" value="'.mo_esc_string($umpro_type_phone,"attr").'"
										'.( mo_esc_string($ultipro_enabled_type,"attr") == mo_esc_string($umpro_type_phone,"attr" )? "checked" : "").' />
											<strong>'. mo_( "Enable Phone Verification" ).'</strong>
									</p>
									
										<div '.(mo_esc_string($ultipro_enabled_type,"attr") != mo_esc_string($umpro_type_phone,"attr") ? "hidden" :"").' class="mo_registration_help_desc" id="ultipro_phone_instructions" >
											'. mo_( "Follow the following steps to enable Phone Verification for Ultimate membership Pro Form" ).': 
											<ol>
												<li><a href="'.mo_esc_string($page_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of pages" ).'</li>
												<li>'. mo_( "Click on the <b>Edit</b> option of the page which has your Ultimate membership Pro registration form" ).'.</li>
												<li>'. mo_( "Add the following short code just below the given registration shortcode" ).': <code>[mo_phone]</code> </li>
												<li><a href="'.mo_esc_string($umpro_custom_field_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( "to see the list of your custom fields." ).'</li>
												<li>'. mo_( "Click on the edit option for the phone field and change the field type to text. Click on save to save your settings." ).'</li>
												<li>'. mo_( "Enable the phone field for your registration form and make sure it is a required field." ).'</li>  
												<li>'. mo_( "Add a custom text field with slug \"validate\" and label \"Enter Validation Code\" in your registration page. Use this text field to enter the OTP received. Make sure it's a required field." ).'</li>								
												<li>'. mo_( "Click on the Save Button to save your settings." ).'</li>
											</ol>
									</div>

									
								</div>
							</div>';