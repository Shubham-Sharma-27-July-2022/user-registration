<?php

use OTP\Helper\MoUtility;

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'"><input type="checkbox" '.mo_esc_string($disabled,"attr").' id="formcraft_premium" class="app_enable" data-toggle="fcpremium_options" name="mo_customer_validation_fcpremium_enable" value="1"
										'.mo_esc_string($fcpremium_enabled,"attr").' /><strong>'.mo_esc_string($form_name,"attr").'</strong>';

echo'							<div class="mo_registration_help_desc" '.mo_esc_string($fcpremium_hidden,"attr").' id="fcpremium_options">
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="fcpremium_email" class="app_enable" data-toggle="fcpe_instructions" name="mo_customer_validation_fcpremium_enable_type" value="'.mo_esc_string($fcpremium_type_email,"attr").'"
										'.( mo_esc_string($fcpremium_enabled_type,"attr") == mo_esc_string($fcpremium_type_email,"attr") ? "checked" : "").' />
										<strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>
									<div '.(mo_esc_string($fcpremium_enabled_type,"attr") != mo_esc_string($fcpremium_type_email,"attr") ? "hidden" :"").' class="mo_registration_help_desc" id="fcpe_instructions" >
											'. mo_( "Follow the following steps to enable Email Verification for FormCraft" ).': 
											<ol>
												<li><a href="'.mo_esc_string($fcpremium_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
												<li>'. mo_( "Click on the form to edit it." ).'</li>
												<li>'. mo_( "Add an Email Field to your form. Note the Label of the email field." ).'</li>
												<li>'. mo_( "Add an Verification Field to your form where users will enter the OTP received. Note the Label of the verification field." ).'</li>
												<li>'. mo_( "Enter your Form ID, the label of the Email Field and Verification Field below" ).':<br>
													<br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. mo_esc_string($disabled,"attr") .' onclick="add_fcpremium(\'email\',1);" class="button button-primary" />&nbsp;
													<input type="button" value="-" '. mo_esc_string($disabled,"attr") .' onclick="remove_fcpremium(1);" class="button button-primary" /><br/><br/>';

													$form_results = get_multiple_form_select($fcpremium_otp_enabled,TRUE,TRUE,$disabled,1,'fcpremium','Label');
													$counter1 	  = !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo'											</li>
												<li>'. mo_( "Click on the Save Button to save your settings" ).'</li>
											</ol>
									</div>
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="fcpremium_phone" class="app_enable" data-toggle="fcpp_instructions" name="mo_customer_validation_fcpremium_enable_type" value="'.mo_esc_string($fcpremium_type_phone,"attr").'"
										'.( mo_esc_string($fcpremium_enabled_type,"attr") == mo_esc_string($fcpremium_type_phone,"attr") ? "checked" : "").' />
										<strong>'. mo_( "Enable Phone Verification" ).'</strong>
									</p>
									<div '.(mo_esc_string($fcpremium_enabled_type,"attr") != mo_esc_string($fcpremium_type_phone,"attr") ? "hidden" : "").' class="mo_registration_help_desc" id="fcpp_instructions" >
											'. mo_( "Follow the following steps to enable Phone Verification for FormCraft" ).': 
											<ol>
												<li><a href="'.mo_esc_string($fcpremium_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of forms" ).'</li>
												<li>'. mo_( "Click on the form to edit it." ).'</li>
												<li>'. mo_( "Add a Phone Field to your form. Note the Label of the phone field." ).'</li>
												<li>'. mo_( "Add an Verification Field to your form where users will enter the OTP received. Note the Label of the verification field." ).'</li>
												<li>'. mo_( "Enter your Form ID, the label of the Email Field and Verification Field below" ).':<br>
													<br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. mo_esc_string($disabled,"attr") .' onclick="add_fcpremium(\'phone\',2);" class="button button-primary" />&nbsp;
													<input type="button" value="-" '. mo_esc_string($disabled,"attr") .' onclick="remove_fcpremium(2);" class="button button-primary" /><br/><br/>';

                                                    $form_results = get_multiple_form_select($fcpremium_otp_enabled,TRUE,TRUE,$disabled,2,'fcpremium','Label');
													$counter2 	  = !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo'											</li>
												<li>'. mo_( "Click on the Save Button to save your settings" ).'</li>
											</ol>
									</div>
								</div>
							</div>';

                            multiple_from_select_script_generator(TRUE,TRUE,'fcpremium','Label',[$counter1,$counter2,0]);


