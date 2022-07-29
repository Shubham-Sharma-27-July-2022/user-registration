<?php

echo'		<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
                <div style="color: darkblue;background: lightblue;padding:10px;border-radius:5px"> 
                    <span > This feature is introduced to show that the plugin works even with forms that are not yet integrated. Some of the features of OTP verification will not work with this custom form, hence it is advisable to not compromise with security of your form since errors wont be handled.<br>Please contact us for full integration of your form at <a style="cursor:pointer;" onClick="otpSupportOnClick();" style="color:darkblue"><b><u>otpsupport@xecurify.com</u></b></a></span>                
                </div>
                <br>
		        <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
                        id="custom_form_contact" 
                        class="app_enable" 
                        data-toggle="custom_form_contact_options" 
                        name="mo_customer_validation_custom_form_contact_enable" 
                        value="1" '.mo_esc_string($custom_form_enabled,"attr").' /><strong>'. $form_name . '</strong>';

echo'			<div class="mo_registration_help_desc" '.mo_esc_string($custom_form_hidden,"attr").' id="custom_form_contact_options">
                    <p><input type="radio" '.mo_esc_string($disabled,"attr").' id="custom_form_contact_email" class="app_enable" 
                        data-toggle="custom_form_contact_email_instructions" name="mo_customer_validation_custom_form_enable_type" 
                        value="'.mo_esc_string($custom_form_type_email,"attr").'"
                        '.( mo_esc_string($custom_form_enabled_type,"attr") == mo_esc_string($custom_form_type_email,"attr") ? "checked" : "").' /><strong>
                        '. mo_( "Enable Email verification").'</strong>
                    </p>
                    <div '.(mo_esc_string($custom_form_enabled_type,"attr") != mo_esc_string($custom_form_type_email,"attr") ? "hidden" :"").' class="mo_registration_help_desc" 
                            id="custom_form_contact_email_instructions" >
                            '. mo_( "Follow the following steps to enable Email Verification for").'
                            Your own form: 
                                      <div style="color:black;background: #80808040;padding:10px;border-radius:5px">
            <span ><b>NOTE: Choosing your selector</b><br><li> Element\'s id selector looks like \'#element_id\'</li><li> Element\'s class selector looks like \'.element_class\' </li><li> Element\'s name selector is \'input[name=\'element_name\']\' </li> 
            </span>
            </div>
                            <ol>
                            <li>Find your form\'s submit button selector through browser\'s console.
                            </li>
                                <li>
                                    '. mo_( "Enter the Submit button selector: ").' 
                                    <span class="tooltip">
                        <span class="dashicons dashicons-editor-help"></span>
                        <span class="tooltiptext" style="background-color:lightgrey;color:#606060">
                            <span class="header" style="color:black">Trouble finding your forms Submit button selector?</span><hr>
                            <span class="body">Selector is an unique "id", "name" or "class" of an element. You can find the selector while adding the desired field in your form or by using your browsers inspector.  
                            </span>
                        </span>
                    </span>
                    <input type="hidden" name="custom_form[form][]" value="1"/>
                                    <input type="text" id = "mo_customer_validation_custom_form_submit_id" name="custom_form[email][submit_id]" style="width:30%" placeholder = "Enter your form\'s Submit button selector" value="'.mo_($custom_form_submit_selector).'"/>
                                </li>
                                <li>Find your form\'s Email Field selector through browser\'s console which you want to verify.  </li>
                                <li>
                                    '. mo_( "Enter the Field button selector: ").' 
                                    <span class="tooltip">
                <span class="dashicons dashicons-editor-help"></span>
                <span class="tooltiptext" style="background-color:lightgrey;color:#606060">
                    <span class="header" style="color:black">Trouble finding your forms Email field selector?</span><hr>
                    <span class="body">You need to provide the unique selector of the field you want to verify. You can find the selector while adding the desired field in your form or by using your browsers inspector.  
                    </span>
                </span>
            </span>
                                   <input type="text" name="custom_form[email][field_id]" style="width:30%;" placeholder = "Enter your form\'s Email Field selector"value="'.mo_($custom_form_field_selector).'"/>
                                </li>
                                
                                <li>'. mo_( "Click on the Save Button to save your settings").'</li>
                            </ol>
                    </div>
                    <p><input type="radio" '.$disabled.' id="custom_form_contact_phone" class="app_enable" data-toggle="custom_form_contact_phone_instructions" name="mo_customer_validation_custom_form_enable_type" value="'.$custom_form_type_phone.'"
                        '.( $custom_form_enabled_type == $custom_form_type_phone ? "checked" : "").' /><strong>'.mo_( "Enable Phone verification").'</strong>
                    </p>
                    <div '.($custom_form_enabled_type != $custom_form_type_phone ? "hidden" : "").' class="mo_registration_help_desc" id="custom_form_contact_phone_instructions" >
                            '.mo_( "Follow the following steps to enable Phone Verification for Your Own Form").': 
                                                <div style="color:black;background: #80808040;padding:10px;border-radius:5px">
            <span ><b>NOTE: Choosing your selector</b><br><li> Element\'s id selector looks like \'#element_id\'</li><li> Element\'s class selector looks like \'.element_class\' </li><li> Element\'s name selector is \'input[name=\'element_name\']\' </li> 
            </span>
            </div>
                            <ol>
                                <li>Find your form\'s submit button selector through browser\'s console. </li>
                                <li>
                                    '. mo_( "Enter the Submit button selector: ").' 
                                     <span class="tooltip">
                        <span class="dashicons dashicons-editor-help"></span>
                        <span class="tooltiptext" style="background-color:lightgrey;color:#606060">
                            <span class="header" style="color:black">Trouble finding your forms Submit button selector?</span><hr>
                            <span class="body">Selector is an unique "id", "name" or "class" of an element. You can find the selector while adding the desired field in your form or by using your browsers inspector. 
                            </span>
                        </span>
                    </span>
            <input type="hidden" name="custom_form[form][]" value="1"/>
                                    <input type="text" id = "mo_customer_validation_custom_form_submit_id" name="custom_form[phone][submit_id]" style="width:30%" placeholder = "Enter your form\'s Submit button selector" value="'.mo_($custom_form_submit_selector).'"/>
                                </li>
                                <li>Find your form\'s Phone Field selector through browser\'s console which you want to verify.  </li>
                                <li>
                                    '. mo_( "Enter the Field button selector: ").' 
                                    <span class="tooltip">
                <span class="dashicons dashicons-editor-help"></span>
                <span class="tooltiptext" style="background-color:lightgrey;color:#606060">
                    <span class="header" style="color:black">Trouble finding your forms Phone field selector?</span><hr>
                    <span class="body">You need to provide the unique selector of the field you want to verify. You can find the selector while adding the desired field in your form or by using your browsers inspector.
                    </span>
                </span>
            </span>
                                   <input type="text" name="custom_form[phone][field_id]" style="width:30%;" placeholder = "Enter your form\'s Phone Field selector"value="'.mo_($custom_form_field_selector).'"/>
                                </li>
                                
                                <li>'. mo_( "Click on the Save Button to save your settings").'</li>
                            </ol>
                    </div>
                    <p style="margin-left:2%;">
                    <i><b>'.mo_("Verification Button text").':</b></i>
                    <input class="mo_registration_table_textbox" name="mo_customer_validation_custom_form_button_text" type="text" value="'.$button_text.'">                 
                </p>
                </div>
            </div>';