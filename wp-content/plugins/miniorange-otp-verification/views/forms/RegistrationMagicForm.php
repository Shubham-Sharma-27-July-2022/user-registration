<?php

use OTP\Helper\MoMessages;
use OTP\Helper\MoUtility;

echo' 	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'"><input type="checkbox" '.mo_esc_string($disabled,"attr").' id="crf_default" class="app_enable" data-toggle="crf_default_options" name="mo_customer_validation_crf_default_enable" value="1"
				'.mo_esc_string($crf_enabled,"attr").' /><strong>'. mo_esc_string($form_name,"attr") .'</strong>';

echo'			<div class="mo_registration_help_desc" '.mo_esc_string($crf_hidden,"attr").' id="crf_default_options">
					<b>'. mo_( "Choose between Phone or Email Verification").'</b>
					<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="crf_phone" data-toggle="crf_phone_instructions" class="form_options app_enable" name="mo_customer_validation_crf_enable_type" value="'.mo_esc_string($crf_type_phone,"attr").'"
						'.( mo_esc_string($crf_enable_type,"attr") == mo_esc_string($crf_type_phone,"attr") ? "checked" : "" ).' />
							<strong>'. mo_( "Enable Phone Verification").'</strong>';

echo'					<div '.(mo_esc_string($crf_enable_type,"attr") != mo_esc_string($crf_type_phone,"attr") ? "hidden" :"").' id="crf_phone_instructions" class="mo_registration_help_desc">
							'. mo_( "Follow the following steps to enable Phone Verification").':
							<ol>
								<li><a href="'.mo_esc_string($crf_form_list,"url").'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see your list of forms").'</li>
								<li>'. mo_( "Click on <b>fields</b> link of your form to see list of fields.").'</li>
								<li>'. mo_( "Choose <b>Text</b> field from the list. Please do not select Phone/Mobile Number.").'</li>
								<li>'. mo_( "Enter the <b>Label</b> of your new field. Keep this handy as you will need it later.").'</li>
								<li>'. mo_( "Navigate to Advanced settings.").'</li>
								<li>'. mo_( "Under RULES section check the box which says <b>Is Required</b>.").'</li>
								<li>'. mo_( "Enable <b>Define New User Meta Key</b> under <b>Add Field to WordPress User Profile</b> section.").'</li>
								<li>'. mo_( "Enter the meta key as <b>rm_phone_number</b>.").'</li>
								<li>'. mo_( "Click on <b>Save</b> button to save your new field.").'<br/>
								<br/>'. mo_( "Add Form" ).' : <input type="button"  value="+" '. mo_esc_string($disabled,"attr") .' onclick="add_crf(\'phone\',2);" class="button button-primary" />&nbsp;
								<input type="button" value="-" '. mo_esc_string($disabled,"attr") .' onclick="remove_crf(2);" class="button button-primary" /><br/><br/>';

								$form_results = get_multiple_form_select($crf_form_otp_enabled,FALSE,TRUE,$disabled,2,'crf','Label');
								$crfcounter2 = !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;
echo'											
								</li>								
								<li>'.mo_( "Click on the Save Button to save your settings and keep a track of your Form Ids." ).'</li>
							</ol>
							<input  type="checkbox" 
							        '.$disabled.' 
							        id="mo_customer_validation_crf_restrict_duplicates" 
							        name="mo_customer_validation_crf_restrict_duplicates" 
							        value="1"
							        '.$restrict_duplicates.'/>
				            <strong>'. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'</strong>

						</div>
					</p>
					<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="crf_email" data-toggle="crf_email_instructions" class="form_options app_enable" name="mo_customer_validation_crf_enable_type" value="'.mo_esc_string($crf_type_email,"attr").'"
						'.( mo_esc_string($crf_enable_type,"attr") == mo_esc_string($crf_type_email,"attr") ? "checked" : "").' />
						<strong>'. mo_( "Enable Email Verification").'</strong>
					</p>
					<div '.(mo_esc_string($crf_enable_type,"attr") != mo_esc_string($crf_type_email,"attr") ? "hidden" :"").' id="crf_email_instructions" class="crf_form mo_registration_help_desc">
						<ol>
							<li><a href="'.mo_esc_string($crf_form_list,"url").'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see your list of forms").'</li>
							<li>'. mo_( "Click on <b>fields</b> link of your form to see  list of fields.").'</li>
							<li>'. mo_( "Choose <b>email</b> field from the list.").'</li>
							<li>'. mo_( "Enter the <b>Label</b> of your new field. Keep this handy as you will need it later.").'</li>
							<li>'. mo_( "Under RULES section check the box which says <b>Is Required</b>.").'</li>
							<li>'. mo_( "Click on <b>Save</b> button to save your new field.").'<br/>
							<br/>'. mo_( "Add Form" ).' : <input type="button"  value="+" '. mo_esc_string($disabled,"attr") .' onclick="add_crf(\'email\',1);" class="button button-primary"/>&nbsp;
								<input type="button" value="-" '. mo_esc_string($disabled,"attr") .' onclick="remove_crf(1);" class="button button-primary" /><br/><br/>';

								$form_results = get_multiple_form_select($crf_form_otp_enabled,FALSE,TRUE,$disabled,1,'crf','Label');
								$crfcounter1 	  =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo                        '</li>
						
							
							<li>'.mo_( "Click on the Save Button to save your settings and keep a track of your Form Ids." ).'</li>
						</ol>
					</div>
					<p><input type="radio" '.mo_esc_string($disabled,"attr").' id="crf_both" data-toggle="crf_both_instructions"  class="form_options app_enable" name="mo_customer_validation_crf_enable_type" value="'.mo_esc_string($crf_type_both,"attr").'"
						'.( mo_esc_string($crf_enable_type,"attr") == mo_esc_string($crf_type_both,"attr")? "checked" : "" ).' />
						<strong>'. mo_( "Let the user choose").'</strong>';

						mo_draw_tooltip(
						    MoMessages::showMessage(MoMessages::INFO_HEADER),MoMessages::showMessage(MoMessages::ENABLE_BOTH_BODY)
                        );

echo'				<div '.(mo_esc_string($crf_enable_type,"attr") != mo_esc_string($crf_type_both,"attr") ? "hidden" :"").' id="crf_both_instructions" class="mo_registration_help_desc">
						'. mo_( "Follow the following steps to enable both Email and Phone Verification").':
						<ol>
							<li><a href="'.mo_esc_string($crf_form_list,"url").'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see your list of forms").'</li>
							<li>'. mo_( "Click on <b>fields</b> link of your form to see list of fields.").'</li>
							<li>'. mo_( "Choose <b>Text</b> field from the list. Please do not select Phone/Mobile Number.").'</li>
								<li>'. mo_( "Enter the <b>Label</b> of your new field. Keep this handy as you will need it later.").'</li>
								<li>'. mo_( "Navigate to Advanced settings.").'</li>
								<li>'. mo_( "Under RULES section check the box which says <b>Is Required</b>.").'</li>
								<li>'. mo_( "Enable <b>Associate with Existing User Meta Keys</b> under <b>Add Field to WordPress User Profile</b> section.").'</li>
								<li>'. mo_( "Select your user meta key as <b>pmpro_bphone</b>.").'</li>
							<li>'. mo_( "Click on <b>Save</b> button to save your new field.").'<br/>
							<br/>'. mo_( "Add Form" ).' : <input type="button"  value="+" '. mo_esc_string($disabled,"attr") .' onclick="add_crf(\'both\',3);" class="button button-primary"/>&nbsp;
								<input type="button" value="-" '. mo_esc_string($disabled,"attr") .' onclick="remove_crf(3);" class="button button-primary" /><br/><br/>';

								$form_results = get_multiple_form_select($crf_form_otp_enabled,FALSE,TRUE,$disabled,3,'crf','Label');
                                $crfcounter3	  =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo                        '</li>
						
							
							<li>'.mo_( "Click on the Save Button to save your settings and keep a track of your Form Ids." ).'</li>
						</ol>
					</div>
				</p>
			</div>
		</div>';

        multiple_from_select_script_generator(FALSE,TRUE,'crf','Label',[$crfcounter1,$crfcounter2,$crfcounter3]);