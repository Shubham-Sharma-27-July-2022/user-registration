<?php

echo'<!--Register with miniOrange-->
	<form name="f" method="post" action="" id="register-form">';
        wp_nonce_field($nonce);
echo'	<input type="hidden" name="option" value="mo_registration_register_customer" />
		<div class="mo_registration_divided_layout mo-otp-full">
			<div class="mo_registration_table_layout mo-otp-center">
				<h2>
				    '.mo_("REGISTER WITH MINIORANGE").'
				    <span style="float:right;margin-top:-10px;">
                        <a href="#goToLoginPage" class="button button-primary button-large">'.mo_("Already Have an Account? Sign In").'</a>
                    </span>
                </h2>
                <hr>
				<p>
				    <div class="mo_idp_help_desc">
                        You are using a third party service for Email and SMS Delivery. In order to make it easy to 
                        manage licenses, download reports, track transactions and generate leads we ask you set up an account 
                        before using the plugin. The plugin ships with 10 free email and 10 free SMS transactions.<br/>
                        We use the personal information you provide for account creation purposes only. It allows us to 
                        reach out to you easily in case of any support.   
                    </div>
                </p>
				<table class="mo_registration_settings_table">
					<tr>
						<td><b><font color="#FF0000">*</font>'.mo_("Email:").'</b></td>
						<td><input class="mo_registration_table_textbox" type="email" name="email"
							required placeholder="person@example.com"
							value="'.mo_esc_string($current_user->user_email,"attr").'" /></td>
					</tr>

					<tr>
						<td><b><font color="#FF0000">*</font>'.mo_("Website/Company Name:").'</b></td>
						<td><input class="mo_registration_table_textbox" type="text" name="company"
							required placeholder="'.mo_("Enter your companyName").'"
							value="'.mo_esc_string($_SERVER["SERVER_NAME"],"attr").'" /></td>
						<td></td>
					</tr>

					<tr>
						<td><b>&nbsp;&nbsp;'.mo_("FirstName:").'</b></td>
						<td><input class="mo_registration_table_textbox" type="text" name="fname"
							placeholder="'.mo_("Enter your First Name").'"
							value="'.mo_esc_string($current_user->user_firstname,"attr").'" /></td>
						<td></td>
					</tr>

					<tr>
						<td><b>&nbsp;&nbsp;'.mo_("LastName:").'</b></td>
						<td><input class="mo_registration_table_textbox" type="text" name="lname"
							placeholder="'.mo_("Enter your Last Name").'"
							value="'.mo_esc_string($current_user->user_lastname,"attr").'" /></td>
						<td></td>
					</tr>
					
					<tr>
						<td><b><font color="#FF0000">*</font>'.mo_("Password:").'</b></td>
						<td><input class="mo_registration_table_textbox" required type="password"
							name="password" placeholder="'.mo_("Choose your password (Min. length 6)").'" /></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>'.mo_("Confirm Password:").'</b></td>
						<td><input class="mo_registration_table_textbox" required type="password"
							name="confirmPassword" placeholder="'.mo_("Confirm your password").'" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
						    <br /><input type="submit" name="submit" value="'.mo_("Register").'" style="width:100px;"
							class="button button-primary button-large" />
						</td>
					</tr>
				</table>
			</div>
		</div>
	</form>
	<form id="goToLoginPageForm" method="post" action="">';
        wp_nonce_field($nonce);
echo'	<input type="hidden" name="option" value="mo_go_to_login_page" />
	</form>
	<script>
		jQuery(document).ready(function(){
			$mo(\'a[href="#forgot_password"]\').click(function(){
				$mo("#forgotpasswordform").submit();
			});

			$mo(\'a[href="#goToLoginPage"]\').click(function(){
				$mo("#goToLoginPageForm").submit();
			});
		});
	</script>';