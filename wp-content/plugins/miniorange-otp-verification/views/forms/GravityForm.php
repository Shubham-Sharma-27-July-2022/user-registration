<?php

use OTP\Helper\MoUtility;

echo'			<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
			                    <input  type="checkbox" '.mo_esc_string($disabled,"attr").'
			                            id="gf_contact" class="app_enable"
			                            data-toggle="gf_contact_options"
			                            name="mo_customer_validation_gf_contact_enable"
			                            value="1" '.mo_esc_string($gf_enabled,"attr").' /><strong>'. mo_esc_string($form_name,"attr") . '</strong>';

echo'							<div class="mo_registration_help_desc" '.mo_esc_string($gf_hidden,"attr").' id="gf_contact_options">
									<p><input 	type="radio" '.mo_esc_string($disabled,"attr").' id="gf_contact_email" class="app_enable"
												data-toggle="gf_contact_email_instructions"
												name="mo_customer_validation_gf_contact_type"
												value="'.mo_esc_string($gf_type_email,"attr").'"
												'.( mo_esc_string($gf_enabled_type,"attr") == mo_esc_string($gf_type_email,"attr") ? "checked" : "").' />
										<strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>

										<div '.(mo_esc_string($gf_enabled_type,"attr") != mo_esc_string($gf_type_email,"attr") ? "hidden" :"").'
										     class="mo_registration_help_desc" id="gf_contact_email_instructions" >
											'. mo_( "Follow the following steps to enable Email Verification for" ).' Gravity form:
											<ol>
												<li><a href="'.mo_esc_string($gf_field_list,"url").'" target="_blank">
												    '. mo_( "Click Here" ).'</a> '. mo_( " to see your list of the Gravity Forms." ).'
												</li>
												<li>'. mo_( "Click on the Edit option of your form" ).'</li>
												<li>'. mo_( "Add an email field to your existing form" ).'</li>
												<li>'. mo_( "Add a text field with label \"Enter Validation Code\" in your existing form." ).'</li>
												<li>'. mo_( "Click on the Edit option of your form" ).'
												<li>'. mo_("Add the form id of your form below for which you want to enable Email verification:").'<br>
												<br/>'.mo_( "Add Form" ).' : <input  type="button"  value="+" '. mo_esc_string($disabled,"attr") .'
                                                                                            onclick="add_gravity(\'email\',1);"
                                                                                            class="button button-primary" />&nbsp;
													    <input  type="button" value="-" '. mo_esc_string($disabled,"attr") .'
													            onclick="remove_gravity(1);"
													            class="button button-primary" /><br/><br/>';

                                                $gf_form_results = get_multiple_form_select(
                                                    $gf_otp_enabled,TRUE,TRUE,$disabled,1,'gravity','Label'
                                                );
                                                $gfcounter1= !MoUtility::isBlank($gf_form_results['counter']) ? max($gf_form_results['counter']-1,0) : 0 ;

echo'
												</li>
												<li>'.mo_( "Click on the Save Button to save your settings and keep a track of your Form Ids." ).'</li>
											</ol>
									</div>
									<p><input 	type="radio" '.mo_esc_string($disabled,"attr").' id="gf_contact_phone" class="app_enable"
												data-toggle="gf_contact_phone_instructions"
												name="mo_customer_validation_gf_contact_type"
												value="'.mo_esc_string($gf_type_phone,"attr").'"
										'.( mo_esc_string($gf_enabled_type,"attr") == mo_esc_string($gf_type_phone,"attr")? "checked" : "").' />
										<strong>'. mo_( "Enable Phone Verification" ).'</strong>
									</p>
									<div '.(mo_esc_string($gf_enabled_type,"attr") != mo_esc_string($gf_type_phone,"attr") ? "hidden" : "").' class="mo_registration_help_desc" id="gf_contact_phone_instructions" >
											'. mo_( "Follow the following steps to enable phone Verification for Gravity form" ).':
											<ol>
												<li><a href="'.mo_esc_string($gf_field_list,"url").'" target="_blank">
												    '. mo_( "Click Here" ).'</a> '. mo_( " to see your list of the Gravity Forms." ).'</li>
												<li>'. mo_( "Click on the Edit option of your form" ).'</li>
												<li>'. mo_( "Add an phone field to your existing form" ).'</li>
												<li>'. mo_( "Add a text field with label \"Enter Validation Code\" in your existing form." ).'</li>
												<li>'. mo_( "Add the form id of your form below for which you want to enable Phone verification" ).':<br>
												<br/>'. mo_( "Add Form" ).' : <input type="button"  value="+" '.mo_esc_string($disabled,"attr") .'
												                                            onclick="add_gravity(\'phone\',2);"
												                                            class="button button-primary"/>&nbsp;
                                                    <input  type="button" value="-" '. mo_esc_string($disabled,"attr") .'
                                                            onclick="remove_gravity(2);"
                                                            class="button button-primary" /><br/><br/>';

                                                $gf_form_results = get_multiple_form_select(
                                                    $gf_otp_enabled,TRUE,TRUE,$disabled,2,'gravity','Label'
                                                );
                                                $gfcounter2	 = !MoUtility::isBlank($gf_form_results['counter']) ? max($gf_form_results['counter']-1,0) : 0 ;


echo                        	  				'</li>


												<li>'.mo_( "Click on the Save Button to save your settings and keep a track of your Form Ids." ).'</li>
											</ol>
									</div>
									<p style="margin-left:2%;">
                                        <i><b>'.mo_("Verification Button text").':</b></i>
                                        <input  class="mo_registration_table_textbox"
                                                name="mo_customer_validation_gf_button_text"
                                                type="text" value="'.mo_esc_string($gf_button_text,"attr").'">
                                    </p>

								</div>
							</div>';


                            multiple_from_select_script_generator(TRUE,TRUE,'gravity','Label',[$gfcounter1,$gfcounter2,0]);
