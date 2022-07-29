<?php

use OTP\Helper\MoUtility;

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
	        <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
	                id="visual_form" 
	                class="app_enable" 
	                data-toggle="visual_form_options" 
	                name="mo_customer_validation_visual_form_enable" 
	                value="1" '.mo_esc_string($visual_form_enabled,"attr").' />
            <strong>'. mo_esc_string($form_name,"attr") .'</strong>';

echo'		<div    class="mo_registration_help_desc" '.mo_esc_string($visual_form_hidden,"attr").' 
		            id="visual_form_options">
                <p>
                    <input  type="radio" '.mo_esc_string($disabled,"attr").' 
                            id="visual_form_email" 
                            class="app_enable" 
                            data-toggle="vfe_instructions" 
                            name="mo_customer_validation_visual_form_enable_type" 
                            value="'.mo_esc_string($visual_form_type_email,"attr").'"
                            '.( mo_esc_string($visual_form_enabled_type,"attr") == mo_esc_string($visual_form_type_email,"attr") ? "checked" : "").' />
                    <strong>'. mo_( "Enable Email Verification" ).'</strong>
                </p>
                <div    '.(mo_esc_string($visual_form_enabled_type,"attr") != mo_esc_string($visual_form_type_email,"attr") ? "hidden" :"").' 
                        class="mo_registration_help_desc" id="vfe_instructions" >
                        '. mo_( "Follow the following steps to enable Email Verification for visual Form" ).': 
                        <ol>
                            <li>
                                <a href="'.mo_esc_string($visual_form_list,"url").'" target="_blank">
                                    '. mo_( "Click Here" ).
                                '</a> '. mo_( " to see your list of forms" ).'
                            </li>
                            <li>'. mo_( "Note your form's Form ID and Click on the <b>Edit</b> option of your visual form." ).'</li>
                            <li>'. mo_( "Add an Email Field to your form. Note the Field Name/Label of the email field." ).'</li>
                            <li>'. mo_( "Make the Email Field Required." ).'</li>
                            <li>'. mo_( "Enter your Form ID and the Email Field Name/Label below" ).':<br>
                                <br/>'. mo_( "Add Form " ).
                                ': <input   type="button"  
                                            value="+" '. mo_esc_string($disabled,"attr") .' 
                                            onclick="add_visual(\'email\',1);" 
                                            class="button button-primary" />&nbsp;
                                    <input  type="button" 
                                            value="-" 
                                            '. mo_esc_string($disabled,"attr") .' 
                                            onclick="remove_visual(1);" 
                                            class="button button-primary" /><br/><br/>';

                                $form_results = get_multiple_form_select(
                                    $visual_form_otp_enabled,
                                    FALSE,
                                    TRUE,
                                    $disabled,
                                    1,
                                    'visual',
                                    'Label'
                                );
                                $counter1 = !MoUtility::isBlank($form_results['counter'])
                                            ? max($form_results['counter']-1,0) : 0 ;

echo'						</li>
                            <li>'. mo_( "Click on the Save Button to save your settings" ).'</li>
                        </ol>
                </div>
                <p>
                    <input  type="radio" '.mo_esc_string($disabled,"attr").' 
                            id="visual_form_phone" 
                            class="app_enable" 
                            data-toggle="vfp_instructions" 
                            name="mo_customer_validation_visual_form_enable_type" 
                            value="'.mo_esc_string($visual_form_type_phone,"attr").'"
                            '.( mo_esc_string($visual_form_enabled_type,"attr") == mo_esc_string($visual_form_type_phone,"attr") ? "checked" : "").' />
                    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
                </p>
                <div    '.(mo_esc_string($visual_form_enabled_type,"attr") != mo_esc_string($visual_form_type_phone,"attr") ? "hidden" : "").' 
                        class="mo_registration_help_desc" id="vfp_instructions" >
                        '. mo_( "Follow the following steps to enable Phone Verification for visual Form" ).': 
                        <ol>
                            <li>
                                <a href="'.mo_esc_string($visual_form_list,"url").'" target="_blank">'.
                                    mo_( "Click Here" ).
                                '</a> '.
                                mo_( " to see your list of forms" ).'
                            </li>
                            <li>'. mo_( "Note your form's Form ID and Click on the <b>Edit</b> option of your visual form." ).'</li>
                            <li>'. mo_( "Add a Phone Field to your form. Note the Field Name/Label of the phone field." ).'</li>
                            <li>'. mo_( "Make the Phone Field Required." ).'</li>
                            <li>'. mo_( "Enter your Form ID and the Phone Field Name/Label below" ).':<br>
                                <br/>'. mo_( "Add Form " ).
                                ':  <input  type="button"  
                                            value="+" '. mo_esc_string($disabled,"attr") .' 
                                            onclick="add_visual(\'phone\',2);" 
                                            class="button button-primary" />&nbsp;
                                    <input  type="button" 
                                            value="-" 
                                            '. mo_esc_string($disabled,"attr") .' 
                                            onclick="remove_visual(2);" 
                                            class="button button-primary" /><br/><br/>';

                                $form_results = get_multiple_form_select(
                                    $visual_form_otp_enabled,
                                    FALSE,
                                    TRUE,
                                    $disabled,
                                    2,
                                    'visual',
                                    'Label'
                                );
                                $counter2 = !MoUtility::isBlank($form_results['counter'])
                                            ? max($form_results['counter']-1,0) : 0 ;
echo'						</li>
                            <li>'. mo_( "Click on the Save Button to save your settings" ).'</li>
                        </ol>
                </div>
                <p style="margin-left:2%;">
					<i><b>'.mo_("Verification Button text").':</b></i>
					<input  class="mo_registration_table_textbox" 
					        name="mo_customer_validation_visual_form_button_text" 
					        type="text" 
					        value="'.mo_esc_string($button_text,"attr").'"
				</p>
                </div>
                </div>';

                multiple_from_select_script_generator(
                    FALSE,
                    TRUE,
                    'visual',
                    'Label',
                    [$counter1,$counter2,0]
                );
