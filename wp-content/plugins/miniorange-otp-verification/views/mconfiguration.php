<?php

use OTP\Helper\MoConstants;

echo '<div class="mo_registration_divided_layout mo-otp-full">
		<div class="mo_registration_table_layout mo-otp-center">
		    <table style="width: 100%;">
				<tr>
					<td colspan="3">
						<h3>
							'.mo_("SMS & EMAIL CONFIGURATION").'
							<span class="mo-dashicons dashicons dashicons-arrow-up toggle-div" data-show="false" data-toggle="configuration_instructions"></span>
						</h3>
						<hr>
					</td>
				</tr>
			</table>
			<div id="configuration_instructions">
				<table style="width: 100%;">
					<tr>
						<td>
						    <div class="mo_otp_note">
                                <b>
                                We support custom SMS and Email templates and provide secure setup to prevent spamming, contact us at 
                                 <a style="cursor:pointer; color:black;" onClick="otpSupportOnClick();""><font color="#2271b1"><u>'.mo_esc_string(MoConstants::FEEDBACK_EMAIL,"attr").'</u>.</font></a> to securely setup your SMS/Email Template.
                                </b>
                            </div>
                        </td>
					</tr>
					<tr>
						<td>
							<div class="mo_otp_note" style="color:#942828;">
								<b>
                                    <a href="'.MoConstants::FAQ_BASE_URL.'change-sender-id/" target="_blank">
                                        '.mo_("How can I change the senderid/number of the sms i receive?").'
                                    </a>
								</b>				
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="mo_otp_note" style="color:#942828;">
								<b>
								    <a href="'.MoConstants::FAQ_BASE_URL.'change-email-address/" target="_blank">
									        '.mo_("How can I change the sender email of the email i receive?").'
									</a>
								</b>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>		
	</div>';