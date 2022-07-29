<?php

use OTP\Helper\MoMessages;

echo' 	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'"><input type="checkbox" '.mo_esc_string($disabled,"attr").' id="emember_reg" class="app_enable" data-toggle="emember_default_options" name="mo_customer_validation_emember_default_enable" value="1"
										'.mo_esc_string($emember_enabled,"attr").' /><strong>'.mo_esc_string($form_name,"attr").'</strong>';

echo'								<div class="mo_registration_help_desc" '.mo_esc_string($emember_hidden,"attr").' id="emember_default_options">
									<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="emember_phone" class="app_enable" name="mo_customer_validation_emember_enable_type" 
											value="'.mo_esc_string($emember_type_phone,"attr").'" data-toggle="emember_phone_instructions"
										'.( mo_esc_string($emember_enable_type,"attr") == mo_esc_string($emember_type_phone,"attr") ? "checked" : "").' />
										<strong>'. mo_( "Enable Phone Verification" ).'</strong>
									</p>
									<div '.( mo_esc_string($emember_enable_type,"attr") != mo_esc_string($emember_type_phone,"attr") ? "hidden" :"").' class="mo_registration_help_desc" 
											id="emember_phone_instructions" >
											'. mo_( "Follow the following steps to enable Phone Verification for" ).'
											eMember Form: 
											<ol>
												<li><a href="'.mo_esc_string($form_settings_link,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( "to see your form settings." ).'</li>
												<li>'. mo_( "Go to the <b>Registration Form Fields</b> section." ).'</li>
												<li>'. mo_( "Check the \"Show phone field on registration page\" option to show Phone field on your form." ).'</li>
												<li>'. mo_( "Click on the Save Button to save your settings" ).'</li>
											</ol>
									</div>
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="emember_email" class="app_enable" name="mo_customer_validation_emember_enable_type" value="'.mo_esc_string($emember_type_email,"attr").'"
										'.( mo_esc_string($emember_enable_type,"attr") == mo_esc_string($emember_type_email,"attr") ? "checked" : "").' />
										<strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="emember_both" class="app_enable" name="mo_customer_validation_emember_enable_type" 
										value="'.mo_esc_string($emember_type_both,"attr").'" data-toggle="emember_both_instructions"
										'.( mo_esc_string($emember_enable_type,"attr") == mo_esc_string($emember_type_both,"attr") ? "checked" : "").' />
											<strong>'. mo_( "Let the user choose" ).'</strong>';

											mo_draw_tooltip(
											    MoMessages::showMessage(MoMessages::INFO_HEADER),
                                                MoMessages::showMessage(MoMessages::ENABLE_BOTH_BODY)
                                            );

echo'										
									</p>
									<div '.( mo_esc_string($emember_enable_type,"attr") != mo_esc_string($emember_type_both,"attr") ? "hidden" :"").' class="mo_registration_help_desc" 
											id="emember_both_instructions" >
											'. mo_( "Follow the following steps to enable Phone Verification for" ).'
											eMember Form: 
											<ol>
												<li><a href="'.mo_esc_string($form_settings_link,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( "to see your form settings." ).'</li>
												<li>'. mo_( "Go to the <b>Registration Form Fields</b> section." ).'</li>
												<li>'. mo_( "Check the \"Show phone field on registration page\" option to show Phone field on your form." ).'</li>
												<li>'. mo_( "Click on the Save Button to save your settings" ).'</li>
											</ol>
									</div>
								</div>
							</div>';