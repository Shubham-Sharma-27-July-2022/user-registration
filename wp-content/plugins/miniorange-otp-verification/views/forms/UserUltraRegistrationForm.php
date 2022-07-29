<?php

use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'"><input type="checkbox" '.mo_esc_string($disabled,"attr").' id="uultra_default" class="app_enable" data-toggle="uultra_default_options" name="mo_customer_validation_uultra_default_enable" value="1"
										'.mo_esc_string($uultra_enabled,"attr").' /><strong>'.mo_esc_string($form_name,"attr").'</strong>';

echo'							<div class="mo_registration_help_desc" '.mo_esc_string($uultra_hidden,"attr").' id="uultra_default_options">
									<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' data-toggle="uultra_phone_instructions" id="uultra_phone" class="form_options app_enable" name="mo_customer_validation_uultra_enable_type" value="'.mo_esc_string($uultra_type_phone,"attr").'"
										'.( mo_esc_string($uultra_enable_type,"attr") == mo_esc_string($uultra_type_phone,"attr") ? "checked" : "").' />
											<strong>'. mo_( "Enable Phone Verification" ).'</strong>';

echo'									<div '.(mo_esc_string($uultra_enable_type,"attr")  != mo_esc_string($uultra_type_phone,"attr") ? "hidden" : "").' id="uultra_phone_instructions" class="mo_registration_help_desc">
											'. mo_( "Follow the following steps to enable Phone Verification" ).':
											<ol>
												<li><a href="'.mo_esc_string($uultra_form_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'</li>
												<li>'. mo_( "Click on <b>Click here to add new field</b> button to add a new phone field." ).'</li>
												<li>'. mo_( "Fill up the details of your new field and click on <b>Submit New Field.</b>" ).'</li>
												<li>'. mo_( "Keep the <b>Meta Key</b> handy as you will need it later on." ).'</li>
												<li>'. mo_( "Enter the Meta Key of the phone field" ).': <input class="mo_registration_table_textbox" id="mo_customer_validation_uultra_phone_field_key" name="mo_customer_validation_uultra_phone_field_key" type="text" value="'.mo_esc_string($uultra_field_key,"attr").'"></li>
											</ol>
										</div>
									</p>
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="uultra_email" class="form_options app_enable" name="mo_customer_validation_uultra_enable_type" value="'.mo_esc_string($uultra_type_email,"attr").'"
										 '.(mo_esc_string($uultra_enable_type,"attr")== mo_esc_string($uultra_type_email,"attr") ? "checked" : "" ).' />
										 <strong>'. mo_( "Enable Email Verification" ).'</strong>
									</p>
									<p><input type="radio" '.mo_esc_string($disabled,"attr").' data-toggle="uultra_both_instructions" id="uultra_both" class="form_options app_enable" name="mo_customer_validation_uultra_enable_type" value="'.mo_esc_string($uultra_type_both,"attr").'"
										'.( mo_esc_string($uultra_enable_type,"attr") == mo_esc_string($uultra_type_both,"attr") ? "checked" : "" ).' />
											<strong>'. mo_( "Let the user choose" ).'</strong>';

										mo_draw_tooltip(
										    MoMessages::showMessage(MoMessages::INFO_HEADER),
                                            MoMessages::showMessage(MoMessages::ENABLE_BOTH_BODY)
                                        );

echo'									<div '.(mo_esc_string($uultra_enable_type,"attr")  != mo_esc_string($uultra_type_both,"attr") ? "hidden" :"").' id="uultra_both_instructions" class="mo_registration_help_desc">
											'. mo_( "Follow the following steps to enable both Email and Phone Verification" ).':
											<ol>
												<li><a href="'.mo_esc_string($uultra_form_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> '. mo_( " to see your list of fields" ).'</li>
												<li>'. mo_( "Click on <b>Click here to add new field</b> button to add a new phone field." ).'</li>
												<li>'. mo_( "Fill up the details of your new field and click on <b>Submit New Field.</b>" ).'</li>
												<li>'. mo_( "Keep the <b>Meta Key</b> handy as you will need it later on." ).'</li>
												<li>'. mo_( "Enter the Meta Key of the phone field" ).': <input class="mo_registration_table_textbox" id="mo_customer_validation_uultra_phone_field_key1" name="mo_customer_validation_uultra_phone_field_key" type="text" value="'.mo_esc_string($uultra_field_key,"attr").'"></li>
											</ol>
										</div>
									</p>
								</div>
							</div>';