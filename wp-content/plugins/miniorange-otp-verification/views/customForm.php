<?php 

echo'

	 <div class="mo_registration_divided_layout mo-otp-full">
		<form name="f" method="post" action="" id="mo_customer_customization_form">
		    <input type="hidden" name="option" value="mo_customer_customization_form" />';

				wp_nonce_field( $nonce ); 

echo'
			<div class="mo_registration_table_layout mo-otp-center">
				<div style="width:100% ">
					<h3>
								'.mo_("Do It Yourself - Try OTP verification on your form").'
								<span style="float:right;margin-top:-10px;">
									<input type="submit" '.mo_esc_string($disabled,"attr").' name="save" id="cf_settings_button"
										class="button button-primary button-large" 
										value="'.mo_("Save Settings").'"/>
								</span>
					</h3><hr>
				</div >
				<div style="color: darkblue;background: lightblue;padding:10px;border-radius:5px"> 
					<span > This feature is introduced to show that the plugin works even with forms that are not yet integrated. Some of the features of OTP verification will not work with this custom form, hence it is advisable to not compromise with security of your form since errors wont be handled.<br>Please contact us for full integration of your form at <a style="cursor:pointer;" onClick="otpSupportOnClick();"><u> otpsupport@xecurify.com</u></a></span>				
				</div>
				<div>
				<p>
				<b>'. mo_("Choose between Phone or Email Verification").' <br><br> </b>
								<input  type="radio" 
					            '.mo_esc_string($disabled,"attr").'
					            id="cf_phone" 
					            class="form_options app_enable" 
						        name="cf_enable_type" 
						        value="'.mo_esc_string($custom_form_type_phone,"attr").'" checked/>
                        <strong>'. mo_("Enable Phone verification").'</strong>	&nbsp;	
                        <input  type="radio" 
					            '.mo_esc_string($disabled,"attr").'
					            id="cf_phone" 
					            class="form_options app_enable" 
						        name="cf_enable_type" 
						        value="'.mo_esc_string($custom_form_type_email,"attr").'"
							    '.( mo_esc_string($custom_form_otp_type,"attr") == mo_esc_string($custom_form_type_email,"attr") ? "checked" : "").' />
                        <strong>'. mo_("Enable Email verification").'</strong>	
                 </p> 
                 </div>
                  <p>
                <div id="submit_selector" style="margin-left:1%;">
					<div style="margin-bottom:1%;"><strong>'.mo_("Submit Button Selector").': </strong>
					<span class="tooltip">
            			<span class="dashicons dashicons-editor-help"></span>
            			<span class="tooltiptext" style="background-color:lightgrey;color:#606060">
                			<span class="header" style="color:black">Trouble finding your forms Submit button selector?</span><hr>
                			<span class="body">Selector is an unique "id", "name" or "class" of an element. You can find the selector while adding the desired field in your form or by using your browsers inspector. 
                			</span>
            			</span>
          			</span>
          <br/><input type="text" name="cf_submit_id" style="width:100%;padding:0.5%;" placeholder = "Enter your form\'s Submit button selector" value="'.mo_(mo_esc_string($custom_form_submit_selector,"attr")).'"/>
          </p>
       <p>   		
	<div id="field_selector" >
		<div style="margin-bottom:1%;"><strong>'.mo_("Field Selector").': </strong>
			<span class="tooltip">
            	<span class="dashicons dashicons-editor-help"></span>
            	<span class="tooltiptext" style="background-color:lightgrey;color:#606060">
                	<span class="header" style="color:black">Trouble finding your forms Phone/Email field selector?</span><hr>
                	<span class="body">You need to provide the unique selector of the field you want to verify. You can find the selector while adding the desired field in your form or by using your browsers inspector. 
                	</span>
            	</span>
          	</span>
          	<input type="text" name="cf_field_id" style="width:100%;padding:0.5%;" placeholder = "Enter your form\'s Phone/Email Field selector"value="'.mo_($custom_form_field_selector).'"/>
          	</div>
          	</p>

          	
						             
	</div>
				';



echo '<p >
                    <strong style="font-size:16px">'.mo_("Verification Button text").':</strong>
                    <input style="width:100%;padding:0.5%;" name="cf_button_text" type="text" value="'.mo_esc_string($button_text,"attr").'">
                </p>

                <div style="color: darkblue;background: lightblue;padding:10px;border-radius:5px">
			<span ><b>NOTE: Choosing your selector</b><br><li> Element\'s id selector looks like \'#element_id\'</li><li> Element\'s class selector looks like \'.element_class\' </li><li> Element\'s name selector is \'input[name=\'element_name\']\' </li> 
			</span>
			</div>

		</div></form></div>
	

		';

