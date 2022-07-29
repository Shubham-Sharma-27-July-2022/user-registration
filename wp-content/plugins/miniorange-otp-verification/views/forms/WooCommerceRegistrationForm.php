<?php

use OTP\Helper\MoMessages;

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
	        <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
	                id="wc_default" 
	                data-toggle="wc_default_options" 
	                class="app_enable" 
	                name="mo_customer_validation_wc_default_enable" 
	                value="1"
		            '.mo_esc_string($woocommerce_registration,"attr").' />
            <strong>'.mo_esc_string($form_name,"attr").'</strong>';

echo'		<div class="mo_registration_help_desc" '.mo_esc_string($wc_hidden,"attr").' id="wc_default_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
				     <input type ="checkbox" '.mo_esc_string($disabled,"attr").' 
				            id ="wcreg_mo_view" 
				            data-toggle = "wcreg_mo_ajax_view_option" 
				            class="app_enable" 
                            name = "mo_customer_validation_wc_is_ajax_form" 
                            value= "1" '.mo_esc_string($is_ajax_mode_enabled,"attr").'/>
                     <Strong>'. mo_( "Do not show a popup. Validate user on the form itself." ).'</strong>
                     <div   '. (mo_esc_string($is_ajax_form,"attr") ? "" : "hidden") .' 
                            id="wcreg_mo_ajax_view_option" 
                            class="mo_registration_help_desc">
                        <div class="mo_otp_note" style="color:red">
                            '. mo_( "This mode does not work with Let the user choose option. ".
                                    "Please use either phone or email only." ).'
                        </div>                           
						<p>
						    <i><b>'.mo_("Verification Button text").':</b></i>
						    <input  class="mo_registration_table_textbox" 
						            name="mo_customer_validation_wc_button_text" 
						            type="text" value="'.mo_esc_string($wc_button_text,"attr").'">					
					    </p>
                     </div>
                </p>
				<p>
					<input  type="radio" '.mo_esc_string($disabled,"attr").' 
					        id="wc_phone" 
					        class="app_enable" 
					        data-toggle="wc_phone_options" 
					        name="mo_customer_validation_wc_enable_type" 
					        value="'.mo_esc_string($wc_reg_type_phone,"attr").'"
						    '.(mo_esc_string($wc_enable_type,"attr") == mo_esc_string($wc_reg_type_phone,"attr") ? "checked" : "" ).'/>
                    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<div    '.(mo_esc_string($wc_enable_type,"attr") != mo_esc_string($wc_reg_type_phone,"attr")  ? "hidden" :"").' 
                        class="mo_registration_help_desc" 
						id="wc_phone_options" >
                    <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
                            name="mo_customer_validation_wc_restrict_duplicates" value="1"
                            '.mo_esc_string($wc_restrict_duplicates,"attr").' />
                    <strong>'. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'</strong>
				</div>
				<p>
					<input  type="radio" '.mo_esc_string($disabled,"attr").' 
					        id="wc_email" 
					        class="app_enable" 
					        name="mo_customer_validation_wc_enable_type" 
					        value="'.mo_esc_string($wc_reg_type_email,"attr").'"
						    '.(mo_esc_string($wc_enable_type,"attr") == mo_esc_string($wc_reg_type_email,"attr") ? "checked" : "" ).'/>
                    <strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
				<p>
					<input  type="radio" 
					        '.mo_esc_string($disabled,"attr").' 
					        id="wc_both" 
					        class="app_enable" 
					        data-toggle="wc_both_options" 
					        name="mo_customer_validation_wc_enable_type" 
					        value="'.mo_esc_string($wc_reg_type_both,"attr").'"
						    '.(mo_esc_string($wc_enable_type,"attr") == mo_esc_string($wc_reg_type_both,"attr") ? "checked" : "" ).'/>
                    <strong>'. mo_( "Let the user choose" ).'</strong>';
                    mo_draw_tooltip(
                        MoMessages::showMessage(MoMessages::INFO_HEADER),
                        MoMessages::showMessage(MoMessages::ENABLE_BOTH_BODY)
                    );
echo '			</p>

				<div '.(mo_esc_string($wc_enable_type,"attr") != mo_esc_string($wc_reg_type_both,"attr") ? "hidden" :"").' class="mo_registration_help_desc" 
						id="wc_both_options" >
                    <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
                            name="mo_customer_validation_wc_restrict_duplicates" value="1"
                            '.mo_esc_string($wc_restrict_duplicates,"attr").' />
                    <strong>'. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'</strong>
				</div>
				<div >
					<input type ="checkbox" '.mo_esc_string($disabled,"attr").' 
				            id ="wcreg_mo_redirect_after_registration" 
				            data-toggle = "wcreg_mo_rediect_page" 
				            class="app_enable" 
                            name = "mo_customer_validation_wcreg_redirect_after_registration" 
                            value= "1" '.mo_esc_string($is_redirect_after_registration_enabled,"attr").'/>
                     <Strong>'. mo_( "Redirect User to a specific page after registration." ).'</strong>
                    <p class="mo_registration_help_desc" '.mo_esc_string($wc_hidden,"attr").' id="wcreg_mo_rediect_page">
					<b>'. mo_( "Select page to redirect to after registration" ).': </b>';
                	wp_dropdown_pages(array("selected" => $redirect_page_id));
echo '          	</p>	
				</div>';
echo'		</div>
		</div>';