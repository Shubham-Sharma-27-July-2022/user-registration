<?php

use OTP\Helper\MoUtility;

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
	        <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
	                id="frm_form" 
	                class="app_enable" 
	                data-toggle="frm_form_options" 
	                name="mo_customer_validation_frm_form_enable" 
	                value="1"'.mo_esc_string($frm_form_enabled,"attr").' />
	        <strong>'. mo_esc_string($form_name,"attr") .'</strong>';


echo'		<div class="mo_registration_help_desc" 
                 '.mo_esc_string($frm_form_hidden,"attr").' 
                 id="frm_form_options">
                <p>
                    <input  type="radio" '.mo_esc_string($disabled,"attr").' 
                            id="frm_form_email" 
                            class="app_enable" 
                            data-toggle="nfe_instructions" 
                            name="mo_customer_validation_frm_form_enable_type" 
                            value="'.mo_esc_string($frm_form_type_email,"attr").'"
                            '.( mo_esc_string($frm_form_enabled_type,"attr") == mo_esc_string($frm_form_type_email,"attr") ? "checked" : "").' />
                    <strong>'. mo_( "Enable Email Verification" ).'</strong>
                </p>
                <div    '.(mo_esc_string($frm_form_enabled_type,"attr") != mo_esc_string($frm_form_type_email,"attr") ? "hidden" :"").' 
                        class="mo_registration_help_desc" 
                        id="nfe_instructions" >
                        '. mo_( "Follow the following steps to enable Email Verification for Formidable Form" ).': 
                        <ol>
                            <li>
                                <a href="'.mo_esc_string($frm_form_list,"url").'" target="_blank">'.
                                    mo_("Click Here" ).
                                '</a> '. mo_(" to see your list of forms" ).'
                            </li>
                            <li>'. mo_("Note the ID of the form and Click on the <b>Edit</b> option of your Formidable form." ).'</li>
                            <li>'. mo_("Add an Email Field to your form. Note the Field Settings ID of the email field." ).'</li>
                            <li>'.
                                    mo_("Add another Text Field to your form for Entering OTP. ".
                                       "Note the Field Settings ID of the OTP Verification field." ).
                            '</li>
                            <li>'. mo_( "Make both Email Field and Verification Field Required." ).'</li>
                            <li>'. mo_( "Enter your Form ID, Email Field ID and Verification Field ID below" ).':
                                    <br><br/>'. mo_( "Add Form " ).': 
                                    <input  type="button"  
                                            value="+" '. mo_esc_string($disabled,"attr") .' 
                                            onclick="add_frm(\'email\',1);" 
                                            class="button button-primary" />&nbsp;
                                    <input  type="button"    
                                            value="-" '. mo_esc_string($disabled,"attr") .' 
                                            onclick="remove_frm(1);" 
                                            class="button button-primary" />
                                    <br/><br/>';

                                    $form_results = get_multiple_form_select (
                                        $frm_form_otp_enabled,
                                        TRUE,
                                        TRUE,
                                        $disabled,
                                        1,
                                        'frm',
                                        'ID'
                                    );
                                    $counter1 = !MoUtility::isBlank($form_results['counter'])
                                                ? max($form_results['counter']-1,0) : 0 ;

echo'					    </li>
                            <li>'. mo_( "Click on the Save Button to save your settings" ).'</li>
                        </ol>
                </div>
                <p>
                    <input  type="radio" '.mo_esc_string($disabled,"attr").' 
                            id="frm_form_phone" 
                            class="app_enable" 
                            data-toggle="nfp_instructions" 
                            name="mo_customer_validation_frm_form_enable_type" 
                            value="'.mo_esc_string($frm_form_type_phone,"attr").'"
                            '.( mo_esc_string($frm_form_enabled_type,"attr") == mo_esc_string($frm_form_type_phone,"attr") ? "checked" : "").' />
                    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
                </p>
                <div    '.(mo_esc_string($frm_form_enabled_type,"attr") != mo_esc_string($frm_form_type_phone,"attr") ? "hidden" : "").' 
                        class="mo_registration_help_desc" id="nfp_instructions" >
                        '. mo_( "Follow the following steps to enable Phone Verification for Formidable Form" ).': 
                        <ol>
                            <li>
                                <a href="'.mo_esc_string($frm_form_list,"url").'" target="_blank">'.
                                    mo_( "Click Here" ).
                                '</a> '.
                                mo_( " to see your list of forms" ).
                            '</li>
                            <li>'. mo_( "Note the ID of the form and Click on the <b>Edit</b> option of your Formidable form." ).'</li>
                            <li>'. mo_( "Add a Phone Field to your form. Note the Field Settings ID of the phone field." ).'</li>
                            <li>'.
                                    mo_( "Add another Text Field to your form for Entering OTP. ".
                                        "Note the Field Settings ID of the OTP Verification field." ).'
                            </li>
                            <li>'. mo_( "Make both Phone Field and Verification Field Required." ).'</li>
                            <li>'. mo_( "Enter your Form ID, Phone Field ID and Verification Field ID below" ).':<br>
                                <br/>'. mo_( "Add Form " ).': 
                                <input  type="button"  
                                        value="+" '. mo_esc_string($disabled,"attr") .' 
                                        onclick="add_frm(\'phone\',2);" 
                                        class="button button-primary" />&nbsp;
                                <input  type="button" 
                                        value="-" '. mo_esc_string($disabled,"attr") .' 
                                        onclick="remove_frm(2);" 
                                        class="button button-primary" /><br/><br/>';

                                $form_results = get_multiple_form_select (
                                    $frm_form_otp_enabled,
                                    TRUE,
                                    TRUE,
                                    $disabled,
                                    2,
                                    'frm',
                                    'ID'
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
					        name="mo_customer_validation_frm_button_text" 
					        type="text" 
					        value="'.mo_esc_string($button_text,"attr").'">			
				</p>
            </div>
        </div>';

        multiple_from_select_script_generator(
            TRUE,
            TRUE,
            'frm',
            'ID',
            [$counter1,$counter2,0]
        );
