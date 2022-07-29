<?php

use OTP\Helper\MoConstants;

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'"><input type="checkbox" '.mo_esc_string($disabled,"attr").' id="userpro_registration" class="app_enable" data-toggle="userpro_registration_options" name="mo_customer_validation_userpro_registration_enable" value="1"
										'.mo_esc_string($userpro_enabled,"attr").' /><strong>'. mo_esc_string($form_name,"attr") . '</strong>';

echo'							<div class="mo_registration_help_desc" '.mo_esc_string($userpro_hidden,"attr").' id="userpro_registration_options">
									<p><input type="checkbox" '.mo_esc_string($disabled,"attr").' class="form_options" '.mo_esc_string($automatic_verification,"attr").' id="mo_customer_validation_userpro_verify" name="mo_customer_validation_userpro_verify" value="1"/> &nbsp;<strong>'. mo_("Verify users after registration" ).'</strong><br/></p>
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="userpro_registration_email" class="app_enable" data-toggle="userpro_registration_email_instructions" name="mo_customer_validation_userpro_registration_type" value="'.mo_esc_string($userpro_type_email,"attr").'"
										'.( mo_esc_string($userpro_enabled_type,"attr") == mo_esc_string($userpro_type_email,"attr") ? "checked" : "").' />
											<strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>
									<div '.(mo_esc_string($userpro_enabled_type,"attr") != mo_esc_string($userpro_type_email,"attr") ? "hidden" :"").' class="mo_registration_help_desc" id="userpro_registration_email_instructions" >
											'. mo_( "Follow the following steps to enable Email Verification for UserPro Form" ).': 
											<ol>
												<li><a href="'.mo_esc_string($page_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of pages" ).'</li>
												<li>'. mo_( "Click on the <b>Edit</b> option of the page which has your UserPro form" ).'.</li>
												<li>'. mo_( "Add the following short code just below your" ).mo_( "UserPro Form shortcode on the profile and registration pages" ).': <code>[mo_verify_email_userpro]</code> </li>
												<li>
													'. mo_( "Add a New Custom Field to your Form. Give the following parameters to the new field" ).': 
													<ol>
														<li>'. mo_( "Give the <i>Field Title</i> as " ).'<code>Verify Email</code></li>
														<li>'. mo_( "Give the <i>Field Type</i> as " ).'<code>Text Input</code></li>
														<li>'. mo_( "Give the <i>Unique Field Key</i> as " ).'<code>'.MoConstants::USERPRO_VER_FIELD_META.'</code></li>
														<li>'. mo_( "Make the Field as a required field." ).'</li>
													</ol>
												</li>
												<li>'. mo_( "Click on the Save Button to save your settings" ).'</li>
											</ol>
									</div>
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="userpro_registration_phone" class="app_enable" data-toggle="userpro_registration_phone_instructions" name="mo_customer_validation_userpro_registration_type" value="'.mo_esc_string($userpro_type_phone,"attr").'"
										'.( mo_esc_string($userpro_enabled_type,"attr") == mo_esc_string($userpro_type_phone,"attr") ? "checked" : "").' />
											<strong>'. mo_( "Enable Phone Verification" ).'</strong>
									</p>
									<div '.(mo_esc_string($userpro_enabled_type,"attr") != mo_esc_string($userpro_type_phone,"attr") ? "hidden" : "").' class="mo_registration_help_desc" id="userpro_registration_phone_instructions" >
											'. mo_( "Follow the following steps to enable Phone Verification for UserPro Form" ).': 
											<ol>
												<li><a href="'.mo_esc_string($page_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of pages" ).'</li>
												<li>'. mo_( "Click on the <b>Edit</b> option of the page which has your UserPro form." ).'</li>
												<li>'. mo_( "Add the following short code just below your UserPro Form shortcode on the profile and registration pages" ).': <code>[mo_verify_phone_userpro]</code> </li>
												<li><a href="'.mo_esc_string($userpro_field_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( "to see your list of UserPro fields." ).'</li>
												<li>'. mo_( "Add a Phone Number Field to your Form from the available fields list" ).'.</li>
												<li>'. mo_( "Add Ajax Call Check for your Phone Number field" ).': <code>mo_phone_validation</code></li>
												<li>
													'. mo_( "Add a New Custom Field to your Form. Give the following parameters to the new field" ).': 
													<ol>
														<li>'. mo_( "Give the <i>Field Title</i> as " ).'<code>Verify Phone</code></li>
														<li>'. mo_( "Give the <i>Field Type</i> as " ).'<code>Text Input</code></li>
														<li>'. mo_( "Give the <i>Unique Field Key</i> as " ).'<code>'.MoConstants::USERPRO_VER_FIELD_META.'</code></li>
														<li>'. mo_( "Make the Field as a required field." ).'</li>
													</ol>
												</li>
												<li>'. mo_( "Click on the Save Button to save your settings" ).'</li>
											</ol>
									</div>
								</div>
							</div>';