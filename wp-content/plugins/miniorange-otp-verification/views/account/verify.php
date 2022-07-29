<?php		
	
echo'	<!-- Enter otp -->
	<div class="mo_registration_divided_layout mo-otp-full">			
		<div class="mo_registration_divided_layout mo-otp-full">
			<div class="mo_registration_table_layout mo-otp-center">
                <table class="mo_registration_settings_table">
                    <h3>'.mo_("Verify OTP").'</h3>
                        <form name="f" method="post" id="otp_form" action="">
                            <input type="hidden" name="option" value="mo_registration_validate_otp" />';
                            wp_nonce_field($nonce);
echo'                       <tr>
                                <td><b><font color="#FF0000">*</font>'.mo_("Enter OTP:").'</b></td>
                                <td colspan="3">
                                    <input  class="mo_registration_table_textbox" 
                                            autofocus="true" 
                                            type="text" 
                                            name="otp_token" 
                                            required placeholder="'.mo_("Enter OTP").'" 
                                            style="width:40%;" 
                                            title="Only 6 digit numbers are allowed"/>
                                 &nbsp;&nbsp; <a  style="cursor:pointer;" 
                                        onclick="document.getElementById(\'resend_otp_form\').submit();">
                                        '.mo_("Resend OTP ?").'
                                    </a>
                                </td>
                            </tr>
                            <tr><td colspan="3"></td></tr>
                            <tr>
                                <td>&nbsp</td>
                                <td style="width:17%">
                                    <input  type="submit" 
                                            name="submit" 
                                            value="'.mo_("Validate OTP").'" 
                                            class="button button-primary button-large" />
                                </td>
                        </form>
                        <form name="f" method="post">';
                                wp_nonce_field($nonce);
echo'					        <td style="width:18%">
                                    <input  type="hidden" name="option" value="mo_registration_go_back"/>
                                    <input  type="submit" 
                                            name="submit"  
                                            value="'.mo_("Back").'" 
                                            class="button button-primary button-large" />
                                </td>
                        </form>                            
                        <form name="f" id="resend_otp_form" method="post" action="">
                                <td>';
                                    wp_nonce_field($nonce);
echo'                               <input type="hidden" name="option" value="mo_registration_resend_otp"/>
                                 </td>
                        </form>
                            </tr>
                </table>
                <br>
                <hr>
        
            <h3>'.mo_("I did not recieve any email with OTP . What should I do ?").'</h3>
            <form id="phone_verification" method="post" action="">';
                wp_nonce_field($nonce);
echo'		    
		        <div class="mo_registration_help_desc">
		            <input type="hidden" name="option" value="mo_registration_phone_verification" />
                '.mo_("If you cannot see an email from miniOrange in your mails, please check your <b>SPAM Folder</b>. If you don\'t see an email even in SPAM folder, verify your identity with our alternate method.").'
                    <br><br>
                    <b>'.mo_("Enter your valid phone number here and verify your identity using one time passcode sent to your phone.").'</b>
                </div>
                <br/>
                <table class="mo_registration_settings_table">
                    <tr>
                        <td>
                        <input  class="mo_registration_table_textbox" 
                                required  
                                pattern="[0-9\+]{12,18}" 
                                autofocus="true" 
                                style="width:100%;" 
                                type="tel" 
                                name="phone_number" 
                                id="phone" 
                                placeholder="'.mo_("Enter Phone Number").'" 
                                value="'.mo_esc_string($admin_phone,"attr").'" 
                                title="'.mo_("Enter phone number(at least 10 digits) without any space or dashes.").'"/>
                        </td>
                        <td>&nbsp;&nbsp;
                             <input type="submit" value="'.mo_("Send OTP").'" 
                                    class="button button-primary button-large" />
                        </td>
                    </tr>
                </table>
            </form>
            <br>           
            <hr/>
            <h3>'.mo_("What is an OTP ?").'</h3>
            <div class="mo_registration_help_desc">
                '.mo_("OTP is a one time passcode ( a series of numbers) that is sent to your email or phone number to verify that you have access to your email account or phone.").'
            </div>
        </div>
    </div>	
</div>';
