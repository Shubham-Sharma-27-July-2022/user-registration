<?php

echo'	<div class="mo_registration_divided_layout mo-otp-full">
				<div id="whatsappTable" class="mo_registration_table_layout mo-otp-center">
				<div style="display:flex">
											<img class = "mo_support_form_new_feature mo_otp_new_feature_class" style="height:50px;width:50px;margin-top:10px"src="'.MOV_URL.'includes/images/mowhatsapp.png">
									<h2 style="margin-top:30px">&nbsp&nbsp'.mo_("WHATSAPP FOR OTP VERIFICATION AND NOTIFICATIONS").'</h2>
									</div>
									<hr>

				    <table style="width:100%">
						<form name="f" method="post" action="" id="mo_whatsapp_settings">
							<tr>
								<td class="mo_otp_note" style="background-color: #bffc6b">'.mo_("This feature allows you to configure WhatsApp for OTP Verification as well as sending notifications and alerts via WhatsApp.").'
                                </td>
							</tr>';

			$html =         '<tr>
							 <td style="padding-left:6%">
							 <ul style="list-style:disc; border">
							 <li> This is a monthly subscription module with <b>1000 Free sms over WhatsApp every month</b>.</li>
							 <li> Use your own WhatsApp Business account for sending OTP and Notifications. </li>
							 <li> Instant Notifications and OTP codes sent via WhatsApp.</li>
							 <li> No Coding required, easy and seamless set up process.</li>
							 </ul>
							 </td>

							</tr>
							 <td><hr><b>'.mo_('Please reach out to us for enabling WhatsApp on your wordpress site : <a style="cursor:pointer;" onClick="otpSupportOnClick(\'Hi! I am interested in using WhatsApp for my website, can you please help me with more information?\');"><u>Contact for WhatsApp</u></a>').'
                                </b>
                             </td>';
            $html = apply_filters('mo_whatsapp_view', $html);
            echo $html;

            echo '
						</form>	
					</table>
				</div>
			</div>';