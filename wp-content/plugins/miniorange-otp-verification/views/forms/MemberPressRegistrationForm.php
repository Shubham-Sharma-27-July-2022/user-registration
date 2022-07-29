<?php

use OTP\Helper\MoMessages;

echo'	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
	        <input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
	                id="mrp" 
	                class="app_enable"  
	                data-toggle="mrp_options" 
	                name="mo_customer_validation_mrp_default_enable" 
	                value="1" '.mo_esc_string($mrp_registration,"attr").' />
			<strong>'. mo_esc_string($form_name,"attr") .'</strong>';

echo'		<div class="mo_registration_help_desc" '.mo_esc_string($mrp_default_hidden,"attr").' id="mrp_options">
				<p>
					<b>'. mo_( "Choose between Phone or Email Verification" ).'</b></p>
				<p>
					<input  type="radio" '.mo_esc_string($disabled,"attr").' 
					        id="mrp_phone" 
					        class="app_enable" 
					        data-toggle="mrp_phone_options" 
					        name="mo_customer_validation_mrp_enable_type" 
					        value="'.mo_esc_string($mrpreg_phone_type,"attr").'"
						    '.(mo_esc_string($mrp_default_type,"attr") == mo_esc_string($mrpreg_phone_type,"attr")  ? "checked" : "" ).'/>
				    <strong>'. mo_( "Enable Phone Verification" ).'</strong>
				</p>
				
				<div '.(mo_esc_string($mrp_default_type,"attr") != mo_esc_string($mrpreg_phone_type,"attr")  ? "hidden" :"").' 
				     class="mo_registration_help_desc" 
					 id="mrp_phone_options" >'. mo_("Follow the following steps to enable Phone Verification").':
					<ol>
						<li><a href="'.mo_esc_string($mrp_fields,"url").'" target="_blank">'. mo_("Click here").'</a> '. mo_(" to add your list of fields." ).'</li>
						<li>'. mo_("Add a new Phone Field by clicking the <b>Add New Field</b> button.").'</li>
						<li>'. mo_("Give the <b>Field Name</b> for the new field.").'</li>		
						<li>'. mo_("Select the field <b>type</b> from the select box. Choose <b>Text Field</b>.").'</li>
						<li>'. mo_("Select <b>Show at Signup</b> and <b>Required</b> from the select box to the right.").'</li>
						<li>'. mo_("Remember the <b>Slug Name</b> from the right as you will need it later.").'</li>
						<li>'. mo_("Click on <b>Update Options</b> button to save your new field.").'</li>
						<li>'. mo_( "Enter <b>Slug Name</b> of the phone field" ).'
						:<input class="mo_registration_table_textbox" 
						        id="mo_customer_validation_mrp_phone_field_key_1_1" 
						        name="mo_customer_validation_mrp_phone_field_key" 
						        type="text" 
						        value="'.mo_esc_string($mrp_field_key,"attr").'">
						</li>
					</ol>
				</div>
				
				<p>
					<input  type="radio" '.mo_esc_string($disabled,"attr").' 
					        id="mrp_email" 
					        class="app_enable" 
					        name="mo_customer_validation_mrp_enable_type" 
					        value="'.mo_esc_string($mrpreg_email_type,"attr").'"
						    '.(mo_esc_string($mrp_default_type,"attr") == mo_esc_string($mrpreg_email_type,"attr")? "checked" : "" ).'/>
					<strong>'. mo_( "Enable Email Verification" ).'</strong>
				</p>
                
                <p>
					<input  type="radio" '.mo_esc_string($disabled,"attr").' 
					        id="mrp_both" 
					        class="app_enable" 
					        data-toggle="mrp_both_options" 
					        name="mo_customer_validation_mrp_enable_type" 
					        value="'.mo_esc_string($mrpreg_both_type,"attr").'"
						    '.(mo_esc_string($mrp_default_type,"attr") == mo_esc_string($mrpreg_both_type,"attr")  ? "checked" : "" ).'/>
				    <strong>'. mo_( "Let the user choose" ).'</strong>';
				    mo_draw_tooltip(
				        MoMessages::showmessage(MoMessages::INFO_HEADER),
                        MoMessages::showmessage(MoMessages::ENABLE_BOTH_BODY)
                    );
echo'    		</p>
				
				<div '.(mo_esc_string($mrp_default_type,"attr") != mo_esc_string($mrpreg_both_type,"attr")  ? "hidden" :"").' 
				     class="mo_registration_help_desc" 
					 id="mrp_both_options" >'. mo_("Follow the following steps to allow both Email and Phone Verification").':
					<ol>
						<li><a href="'.mo_esc_string($mrp_fields,"url").'" target="_blank">'. mo_("Click here").'</a> '. mo_(" to add your list of fields." ).'</li>
						<li>'. mo_("Add a new Phone Field by clicking the <b>Add New Field</b> button.").'</li>
						<li>'. mo_("Give the <b>Field Name</b> for the new field.").'</li>		
						<li>'. mo_("Select the field <b>type</b> from the select box. Choose <b>Text Field</b>.").'</li>
						<li>'. mo_("Select <b>Show at Signup</b> and <b>Required</b> from the select box to the right.").'</li>
						<li>'. mo_("Remember the <b>Slug Name</b> from the right as you will need it later.").'</li>
						<li>'. mo_("Click on <b>Update Options</b> button to save your new field.").'</li>
						<li>'. mo_( "Enter <b>Slug Name</b> of the phone field" ).'
						:<input class="mo_registration_table_textbox" 
						        id="mo_customer_validation_mrp_phone_field_key_2_1" 
						        name="mo_customer_validation_mrp_phone_field_key" 
						        type="text" 
						        value="'.mo_esc_string($mrp_field_key,"attr").'">
						</li>
					</ol>
				</div>
				<input  type="checkbox" '.mo_esc_string($disabled,"attr").' 
                        name="mo_customer_validation_mpr_anon_only" 
                        value="1" '.mo_esc_string($mpr_anon_only,"attr").'/>
                <strong>'. mo_( "Apply OTP Verification only for non-logged in users." ).'</strong>
			</div>
		</div>';