<?php

use OTP\Helper\MoUtility;

echo'	
        <div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
            <input type="checkbox" '.mo_esc_string($disabled,"attr").' id="wpform_basic" class="app_enable" data-toggle="wpform_options" 
                name="mo_customer_validation_wpform_enable" value="1" '.mo_esc_string($is_wpform_enabled,"attr").' />
                <strong>'.mo_esc_string($form_name,"attr").'</strong>';

echo        '<div class="mo_registration_help_desc" '.mo_esc_string($is_wpform_hidden,"attr").' id="wpform_options">
                <b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
                <p>
                    <input type="radio" '.mo_esc_string($disabled,"attr").' id="wp_form_email" class="app_enable" 
                    data-toggle="wpform_email_option" name="mo_customer_validation_wpform_enable_type" 
                    value="'.mo_esc_string($wpform_email_type,"attr").'" '.( mo_esc_string($wpform_enabled_type,"attr") == mo_esc_string($wpform_email_type,"attr") ? "checked" : "").' />
                    <strong>'. mo_( "Enable Email Verification" ).'</strong>
                </p>
                        
                
                <div '.(mo_esc_string($wpform_enabled_type,"attr") != mo_esc_string($wpform_email_type,"attr") ? "hidden" :"").' class="mo_registration_help_desc" id="wpform_email_option"">
                    <ol>
                        <li><a href="'.mo_esc_string($wpform_form_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> 
                            '. mo_( " to see your list of forms" ).'</li>
                        <li>'. mo_( "Click on the <b>Edit</b> option of your WPForm." ).'</li>
                        <li>'. mo_( "Add an Email Field to your form. Note the Field Label of the Email field." ).'</li>
                        <li>'. mo_( "Enter your Form ID, Email Field Label below" ).':<br>
                            <br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. mo_esc_string($disabled,"attr") .' 
                            onclick="add_wpform(\'email\',1);" class="button button-primary" />&nbsp;

                            <input type="button" value="-" '. mo_esc_string($disabled,"attr") .' onclick="remove_wpform(1);" class="button button-primary" />
                            <br/><br/>';

                            $form_results = get_multiple_form_select($wpform_list_of_forms_otp_enabled,FALSE,TRUE,$disabled,1,'wpform','Label');
                            $counter1     =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;

echo '              </ol>
                </div>


                <p>
                    <input type="radio" '.mo_esc_string($disabled,"attr").' id="wp_form_phone" 
                        class="app_enable" data-toggle="wpform_phone_option" name="mo_customer_validation_wpform_enable_type" 
                        value="'.mo_esc_string($wpform_phone_type,"attr").'"'.( mo_esc_string($wpform_enabled_type,"attr") == mo_esc_string($wpform_phone_type,"attr") ? "checked" : "").' />                                                                            
                    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
                </p>
                    
                <div '.(mo_esc_string($wpform_enabled_type,"attr") != mo_esc_string($wpform_phone_type,"attr") ? "hidden" :"").' class="mo_registration_help_desc" 
                    id="wpform_phone_option" '.mo_esc_string($disabled,"attr").'">
                    <ol>
                        <li><a href="'.mo_esc_string($wpform_form_list,"url").'" target="_blank">'. mo_( "Click Here" ).'</a> 
                            '. mo_( " to see your list of forms" ).'</li>
                        <li>'. mo_( "Click on the <b>Edit</b> option of your WPForm." ).'</li>
                        <li>'. mo_( "Add a Phone Field to your form. Note the Field Label of the Phone field." ).'</li>
                        <li>'. mo_( "Set the Format of the Phone Field to international only." ).'</li>
                        <li>'. mo_( "Enter your Form ID, Phone Field Label below" ).':<br>
                            <br/>'. mo_( "Add Form " ).': <input type="button"  value="+" '. mo_esc_string($disabled,"attr") .' onclick="add_wpform(\'phone\',2);
                                " class="button button-primary" />&nbsp; <input type="button" value="-" '. mo_esc_string($disabled,"attr") .' \
                                onclick="remove_wpform(2);" class="button button-primary" /><br/><br/>';

                                $form_results = get_multiple_form_select($wpform_list_of_forms_otp_enabled,FALSE,TRUE,$disabled,2,'wpform','Label');
                                $counter2     =  !MoUtility::isBlank($form_results['counter']) ? max($form_results['counter']-1,0) : 0 ;
echo
    '</ol>
                    </div>  
                    <p style="margin-left:2%;">
                        <i><b>'.mo_("Verification Button text").':</b></i>
                        <input class="mo_registration_table_textbox" name="mo_customer_validation_wpforms_button_text" type="text" value="'.mo_esc_string($button_text,"attr").'">
                    </p>             
                </div>
        </div>';

        multiple_from_select_script_generator(FALSE,TRUE,'wpform','Label',[$counter1,$counter2,0]);
