<?php

use OTP\Addons\WcSMSNotification\Helper\MoWcAddOnUtility;
use OTP\Helper\MoUtility;

echo'	<div class="mo_registration_divided_layout mo-otp-full">
				<div class="mo_registration_table_layout mo-otp-center">';

					MoWcAddOnUtility::is_addon_activated();

	echo'			<table id="wc_admin_sms_template" style="width:100%">
						<form name="f" method="post" action="" id="'.esc_attr($formOptions).'">
							<input type="hidden" name="option" value="'.esc_attr($formOptions).'" />
							<tr>
								<td colspan="2">
									<h2>'.esc_attr($smsSettings->pageHeader).'
									<span style="float:right;margin-top:-10px;">
										<a href="'.esc_url($goBackURL).'" id="goBack" class="button button-primary button-large">
											'.mo_("Go Back").'
										</a>
										<input type="submit" name="save" id="save" '.esc_attr($disabled).' class="button button-primary button-large" 
										    value="'.mo_('Save Settings').'">
									</span>
									</h2>
									<hr>
								</td>
							</tr>
							<tr>
								<td colspan="2">'.esc_attr($smsSettings->pageDescription).'</td>
							</tr>
							<tr>
								<td style="width:160px" ><h4>'.mo_("Enable/Disable").'</h4></td>
								<td>
									<input class="" '.esc_attr($disabled).' type="checkbox" name="'.esc_attr($enableDisableTag).'" 
										id="'.esc_attr($enableDisableTag).'" style="" value="1" '.esc_attr($enableDisable).'>
									'.mo_("Enable this SMS Notification").'
								</td>
							</tr>
							<tr>
								<td><h4>'.mo_("Recipients");

									mo_draw_tooltip(mo_('MULTIPLE RECIPIENTS?'),
                                        mo_('Yes. You can enter semi-colon (;) separated mutiple phone numbers to send the notification to.'));

	echo'							</h4></td>
								<td>
									<input style="width:100%" '.esc_attr($disabled).' type="text" name="'.esc_attr($recipientTag).'" 
										id="'.esc_attr($recipientTag).'" style="" value="'.esc_attr($recipientValue).'"/>
								</td>
							</tr>
							<tr>
								<td>
									<h4>'.mo_("SMS Body");
									mo_draw_tooltip(mo_('AVAILABLE TAGS'),$smsSettings->availableTags);
	echo'
									</h4>
								</td>
								<td>
									<textarea '.esc_attr($disabled).' id="'.esc_attr($textareaTag).'" class="mo_registration_table_textbox" 
										name="'.esc_attr($textareaTag).'" placeholder="'.esc_attr($smsSettings->defaultSmsBody).'" />'.esc_attr($smsSettings->smsBody).'</textarea>
									<span id="characters">Remaining Characters : <span id="remaining">160</span> </span>
								</td>
							</tr>';
echo 				'<tr>
								<td colspan="2">
									<div class="mo_otp_note">
										'.mo_('<b><u>For Indian Traffic Only</u> : The above templates are the default registered templates. Do not make edits in the templates since it may lead to failure in delivery.<br>If you wish to customize the SMS Template, kindly contact us at <a style="cursor:pointer;" onClick="otpSupportOnClick();"><u> otpsupport@xecurify.com</u></a>.</b>').'
									</div>';

$html2    =   				'<tr>
								<td colspan="2">
									<div class="mo_otp_note">
										'.mo_('You can have new line characters in your sms text body. To enter a new line character use the <b><i>%0a</i></b> symbol. To enter a "#" character you can use the <b><i>%23</i></b> symbol. To see a complete list of special characters that you can send in a SMS check with your gateway provider.').'
									</div>';




echo '            
										<div class="mo_otp_note">
										'.mo_('If you are looking for the extra <b>Tags</b> in SMS Body, Please kindly contact us at <a style="cursor:pointer;" onClick="otpSupportOnClick();"><u> otpsupport@xecurify.com</u></a>.').'
									</div>
								</td>
							</tr>
						 </form>	
					</table>
				</div>
			</div>';