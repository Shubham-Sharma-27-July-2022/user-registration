<?php

use OTP\Helper\MoMessages;

echo' 	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
 	        <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
 	                id="wc_checkout" 
 	                data-toggle="wc_checkout_options" 
 	                class="app_enable" 
 	                name="mo_customer_validation_wc_checkout_enable" 
 	                value="1" 
 	                '.mo_esc_string($wc_checkout,"attr").' />
            <strong>'.mo_esc_string($form_name,"attr"). '</strong>';

echo'		<div class="mo_registration_help_desc" '.mo_esc_string($wc_checkout_hidden,"attr").' id="wc_checkout_options">
				<b>'. mo_( "Choose between Phone or Email Verification" ).'</b>
				<p>
				    <input  type="radio" '.mo_esc_string($disabled,"attr").' 
				            id="wc_checkout_phone" 
				            class="app_enable" 
				            data-toggle="wc_checkout_phone_options"
				            name="mo_customer_validation_wc_checkout_type" 
				            value="'.mo_esc_string($wc_type_phone,"attr").'"
						    '.(mo_esc_string($wc_checkout_enable_type,"attr") == mo_esc_string($wc_type_phone,"attr") ? "checked" : "" ).' />
                    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				<div    '.(mo_esc_string($wc_checkout_enable_type,"attr") != mo_esc_string($wc_type_phone,"attr")  ? "hidden" :"").' 
                        class="mo_registration_help_desc" 
						id="wc_checkout_phone_options" >
                    <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
                            name="mo_customer_validation_wc_checkout_restrict_duplicates" 
                            value="1"
                            '.mo_esc_string($restrict_duplicates,"attr").' />
                    <strong>'. mo_( "Do not allow users to use the same phone number for multiple accounts." ).'</strong>
				</div>
				<p>
				    <input  type="radio" '.mo_esc_string($disabled,"attr").' 
				            id="wc_checkout_email" 
				            class="app_enable" 
				            name="mo_customer_validation_wc_checkout_type" 
				            value="'.mo_esc_string($wc_type_email,"attr").'"
						    '.(mo_esc_string($wc_checkout_enable_type,"attr") == mo_esc_string($wc_type_email,"attr") ? "checked" : "" ).' />
                    <strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
				<p style="margin-top:3%;">
					<input  type="checkbox" 
					        '.mo_esc_string($disabled,"attr").' 
					        '.mo_esc_string($guest_checkout,"attr").' 
					        class="app_enable" 
					        name="mo_customer_validation_wc_checkout_guest" 
					        value="1" >
                    <b>'. mo_( "Enable Verification only for Guest Users." ).'</b>';

                mo_draw_tooltip(
                    MoMessages::showMessage(MoMessages::WC_GUEST_CHECKOUT_HEAD),
                    MoMessages::showMessage(MoMessages::WC_GUEST_CHECKOUT_BODY)
                );

echo'
				</p>
				<p>
					<input  type="checkbox" 
					        '.mo_esc_string($disabled,"attr").' 
					        '.mo_esc_string($disable_autologin,"attr") .' 
					        class="app_enable" 
					        name="mo_customer_validation_wc_checkout_disable_auto_login" 
					        value="1" 
					        type="checkbox">
                    <b>'. mo_( "Disable Auto Login after checkout." ).'</b>
                    <br/>
				</p>
				<p>
					<input  type="checkbox" 
					        '.mo_esc_string($disabled,"attr").' 
					        '.mo_esc_string($checkout_button,"attr") .' 
					        class="app_enable" 
					        name="mo_customer_validation_wc_checkout_button" 
					        value="1" 
					        type="checkbox">
                    <b>'. mo_( "Show a verification button instead of a link on the WooCommerce Checkout Page." ).'</b>
                    <br/>
				</p>
				<p>
					<input  type="checkbox" 
					        '.mo_esc_string($disabled,"attr").' 
					        '.mo_esc_string($checkout_popup,"attr").' 
					        class="app_enable" 
					        name="mo_customer_validation_wc_checkout_popup" 
					        value="1" 
					        type="checkbox">
                    <b>'. mo_( "Show a popup for validating OTP." ).'</b>
                    <br/>
				</p>
				<p>
					<input  type="checkbox" 
					        '.mo_esc_string($disabled,"attr").'
					        '.mo_esc_string($checkout_selection,"attr").' 
					        class="app_enable" 
					        data-toggle="selective_payment" 
					        name="mo_customer_validation_wc_checkout_selective_payment" 
					        value="1" 
					        type="checkbox">
                    <b>'. mo_( "Validate OTP for selective Payment Methods." ).'</b>
                    <br/>
				</p>
				<div id="selective_payment" class="mo_registration_help_desc" 
				     '.mo_esc_string($checkout_selection_hidden,"attr").' style="padding-left:3%;">
					<b>
					    <label for="wc_payment" style="vertical-align:top;">'.
                            mo_("Select Payment Methods (Hold Ctrl Key to Select multiple):").
                        '</label> 
                    </b>
				';

                get_wc_payment_dropdown($disabled,$checkout_payment_plans);

echo			'
				</div>
				<p>
					<i><b>'.mo_("Verification Button text").':</b></i>
					<input  class="mo_registration_table_textbox" 
					        name="mo_customer_validation_wc_checkout_button_link_text" 
					        type="text" 
					        value="'.mo_esc_string($button_text,"attr").'">					
				</p>
			</div>
		</div>';