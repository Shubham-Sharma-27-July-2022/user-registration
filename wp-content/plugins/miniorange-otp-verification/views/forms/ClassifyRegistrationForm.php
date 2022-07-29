<?php

echo'			<div class="mo_otp_form" id="'.get_mo_class($handler).'">
                    <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
                            id="classify_theme" 
                            class="app_enable" 
                            data-toggle="classify_options" 
                            name="mo_customer_validation_classify_enable" 
                            value="1"
                            '.mo_esc_string($classify_enabled,"attr").' />
                        <strong>'.$form_name.'</strong>
                    <div class="mo_registration_help_desc" '.mo_esc_string($classify_hidden,"attr").' id="classify_options">			
                        <p>
                            <input  type="radio" 
                                    '.mo_esc_string($disabled,"attr").' 
                                    id="classify_email" 
                                    class="app_enable" 
                                    data-toggle="classify_email_instructions" 
                                    name="mo_customer_validation_classify_type" 
                                    value="'.mo_esc_string($classify_type_email,"attr").'"
                                    '.( mo_esc_string($classify_enabled_type,"attr") == mo_esc_string($classify_type_email,"attr") ? "checked" : "").' />
                                <strong>'. mo_( "Enable Email Verification").'</strong>
                        </p>							
                        <div    '.(mo_esc_string($classify_enabled_type,"attr") != mo_esc_string($classify_type_email,"attr") ? "hidden" :"").' 
                                class="mo_registration_help_desc" id="classify_email_instructions" >
                            '. mo_( "Follow the following to configure your Registration form").': 
                            <ol>
                                <li><a href="'.mo_esc_string($page_list,"url").'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see the list of pages.").'</li>
                                <li>'. mo_( "Click on the Edit option of the \"Register\" page").'</li>
                                <li>'. mo_( "From the page Attributes section ,set \"Register Page\" from your template dropdown menu.").'</li>
                                <li>'. mo_( "Click on the Update button to save your settings.").'</li>
                            </ol>
                            '. mo_( "Follow the following to configure your Profile form").': 
                            <ol>
                                <li>'.
                                    '<a href="'.mo_esc_string($page_list,"url").'" target="_blank">'. mo_( "Click Here").'</a> '.
                                    mo_( " to see the list of pages.").
                                '</li>
                                <li>'.
                                    ( mo_esc_string($classify_enabled_type,"attr") == "classify_email_enable" ? "checked" : "").
                                    mo_( "Click on the Edit option of the \"Profile\" page").
                                '</li>
                                <li>'.
                                    ( mo_esc_string($classify_enabled_type,"attr") == "classify_email_enable" ? "checked" : "").
                                    mo_( "From the page Attributes section ,set \"Profile Page\" from your template dropdown menu.").
                                '</li>
                                <li>'.
                                    ( mo_esc_string($classify_enabled_type,"attr") == "classify_email_enable" ? "checked" : "").
                                    mo_( "Click on the Update button to save your settings.").'
                                </li>
                            </ol>
                            '. mo_( "Click on the Save Button to save your settings").'
                        </div>
																															
                        <p>
                            <input  type="radio" '.mo_esc_string($disabled,"attr").' 
                                    id="classify_phone" 
                                    class="app_enable" 
                                    data-toggle="classify_phone_instructions" 	
                                    name="mo_customer_validation_classify_type" 
                                    value="'.mo_esc_string($classify_type_phone,"attr").'"
                                    '.( mo_esc_string($classify_enabled_type,"attr") == mo_esc_string($classify_type_phone,"attr") ? "checked" : "").' />
                                <strong>'. mo_( "Enable Phone Verification").'</strong>
                        </p>
                    
                        <div    '.(mo_esc_string($classify_enabled_type,"attr") != mo_esc_string($classify_type_phone,"attr") ? "hidden" :"").' 
                                class="mo_registration_help_desc" 
                                id="classify_phone_instructions" >
                            '. mo_( "Follow the following to configure your Registration form ").': 
                            <ol>
                                <li><a href="'.mo_esc_string($page_list,"url").'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see the list of pages.").'</li>
                                <li>'. mo_( "Click on the Edit option of the \"Register\" page").'</li>
                                <li>'. mo_( "From the page Attributes section ,set \"Register Page\" from your template dropdown menu.").'</li>
                                <li>'. mo_( "Click on the Update button to save your settings.").'</li>
                            </ol>
                            '. mo_( "Follow the following to configure your Profile form ").': 
                            <ol>
                                <li><a href="'.mo_esc_string($page_list,"url").'" target="_blank">'. mo_( "Click Here").'</a> '. mo_( " to see the list of pages.").'</li>
                                <li>'. mo_( "Click on the Edit option of the \"Profile\" page").'</li>
                                <li>'. mo_( "From the page Attributes section ,set \"Profile\" Page from your template dropdown menu.").'</li>
                                <li>'. mo_( "Click on the Update button to save your settings.").'</li>
                            </ol>
                            '. mo_( "Click on the Save Button to save your settings").'
                        </div>                    
                    </div>
                </div>';
