<?php

use OTP\Helper\MoConstants;
use OTP\Helper\MoMessages;

echo'	<div class="mo_registration_divided_layout mo-otp-full">
			<form name="f" method="post" action="" id="mo_otp_verification_settings">
			    <input type="hidden" name="option" value="mo_otp_extra_settings" />';

                wp_nonce_field( $nonce );

echo'			<div class="mo_registration_table_layout mo-otp-half">
					<table style="width:100%">
						<tr>
							<td>
								<h3>
									'.mo_("COUNTRY CODE: ").'
									<span style="float:right;margin-top:-10px;">
                                        <input  type="submit" '.mo_esc_string($disabled,"attr").'
                                                name="save"
                                                class="button button-primary button-large"
                                                value="'.mo_("Save Settings").'"/>
                                            <span   class="dashicons dashicons-arrow-up toggle-div"
                                                    data-show="false"
                                                    data-toggle="country_code_settings"></span>
                                    </span>
								</h3><hr>
							</td>
						</tr>
					</table>
					<div id="country_code_settings">
						<table style="width:100%">
							<tr>
							    <td><strong>'.mo_("Select Default Country Code").': </strong></td>
								<td>';
									get_country_code_dropdown();
echo							'</td>
                                <td>';
                                    mo_draw_tooltip(
                                        MoMessages::showMessage(MoMessages::COUNTRY_CODE_HEAD),
                                        MoMessages::showMessage(MoMessages::COUNTRY_CODE_BODY)
                                    );
echo                            '</td>
							</tr>
							<tr>
							    <td></td>
							    <td><style="margin-left:1%">'.mo_("Country Code").': <span id="country_code"></span></td>
							    <td></td>
							</tr>
							<tr>
								<td colspan="3">
								    <input  type="checkbox" '.mo_esc_string($disabled,"attr").'
								            name="show_dropdown_on_form"
								            id="dropdownEnable"
								            value="1"'.mo_esc_string($show_dropdown_on_form,"attr").' />
								    '.mo_("Show a country code dropdown on the phone field.").'
                                </td>
							</tr>
							<tr><td colspan="3"></td></tr>
							<tr><td colspan="3"></td></tr>
						</table>
					</div>
				</div>
				<div id="otpLengthValidity" class="mo_registration_table_layout mo-otp-half">';

echo'	            <table style="width:100%">
                        <tr>
                            <td colspan="2">
                                <h3>
                                    '.mo_("OTP PROPERTIES: ").'
                                    <span style="float:right;margin-top:-10px;">';
                                            if(!$showTransactionOptions)
                                            {
echo'                                           <input  type="submit" '.mo_esc_string($disabled,"attr").'
                                                        name="save"
                                                        class="button button-primary button-large"
                                                        value="'.mo_("Save Settings").'"/>';
                                            }
echo'                                       <span  class="dashicons dashicons-arrow-up toggle-div"
                                                    data-show="false"
                                                    data-toggle="otp_settings">
                                            </span>
                                    </span>
                                </h3>
                                <hr>
                            </td>
                        </tr>
                    </table>
                    <div id="otp_settings">';

                if($showTransactionOptions)
                {
                    echo '<table>
                            <tr>
                                <td><strong>'.mo_("OTP LENGTH: ").'</strong></td>
                                <td><strong>'.mo_("OTP VALIDITY (in mins): ").'</strong></td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <div class="mo_otp_note" style="padding:10px;">
                                        <div class="mo_otp_dropdown_note">
                                            <a href="'.MoConstants::FAQ_BASE_URL.'change-length-otp/" target="_blank">
                                                '.mo_("Click here to see how you can change OTP Length").'
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td width="50%">
                                    <div class="mo_otp_note" style="padding:10px;">
                                        <div class="mo_otp_dropdown_note">
                                            <a href="'.MoConstants::FAQ_BASE_URL.'change-time-otp-stays-valid/" target="_blank">
                                                '.mo_("Click here to see how you can change OTP Validity").'</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>';
                }
                else
                {

					echo'<table>
							<tr>
                                <td><strong><i>'.mo_("OTP LENGTH: ").'</i></strong></td>
                                <td><strong><i>'.mo_("OTP VALIDITY (in mins): ").'</i></strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <input  type="text"
                                            class="mo_registration_table_textbox"
                                            value="'.mo_esc_string($mo_otp_length,"attr").'"
                                            name="mo_otp_length"/>
                                    <div class="mo_otp_note" style="color:#942828;">
                                        <i>'.mo_("Enter the length that you want the OTP to be. Default is 5").'</i>
                                    </div>
                                </td>
                                <td>
                                    <input  type="text"
                                            class="mo_registration_table_textbox"
                                            value="'.mo_esc_string($mo_otp_validity,"attr").'"
                                            name="mo_otp_validity"/>
                                    <div class="mo_otp_note" style="color:#942828;">
                                        <i>'.mo_("Enter the time in minutes an OTP will stay valid for. Default is 5 mins").'</i>
                                    </div>
                                </td>
                            </tr>';
                }

    echo'    		    </table>
    		        </div>
                </div>
				<div id="blockedEmailList" class="mo_registration_table_layout mo-otp-half">
					<table style="width:100%">
						<tr>
							<td colspan="2">
								<h3>
									'.mo_("BLOCKED EMAIL DOMAINS: ").'
									<span style="float:right;margin-top:-10px;">
									    <input  type="submit" '.mo_esc_string($disabled,"attr").'
									            name="save"
                                                class="button button-primary button-large"
                                                value="'.mo_("Save Settings").'"/>
                                        <span   class="dashicons dashicons-arrow-up toggle-div"
                                                data-show="false"
                                                data-toggle="blocked_email_settings">
                                        </span>
                                    </span>
								</h3><hr>
							</td>
						</tr>
					</table>
					<div id="blocked_email_settings">
						<table style="width:100%">
							<tr>
								<td colspan="2">
								    <textarea   name="mo_otp_blocked_email_domains"
								                rows="5"
									            placeholder="'.mo_(" Enter semicolon separated domains that
                                                you want to block. Eg. gmail.com ").'">'.
                                        mo_esc_string($otp_blocked_email_domains,"attr").
                                    '</textarea>
                                </td>
							</tr>
						</table>
					</div>
				</div>
				<div id="blockedPhoneList" class="mo_registration_table_layout mo-otp-half">
					<table style="width:100%">
						<tr>
							<td colspan="2">
								<h3>
									'.mo_("BLOCKED PHONE NUMBERS: ").'
									<span style="float:right;margin-top:-10px;">
									    <input  type="submit" '.mo_esc_string($disabled,"attr").'
									            name="save"
                                                class="button button-primary button-large"
                                                value="'.mo_("Save Settings").'"/>
									    <span   class="dashicons dashicons-arrow-up toggle-div"
									            data-show="false"
									            data-toggle="blocked_sms_settings"></span>
									</span>
								</h3><hr>
							</td>
						</tr>
					</table>
					<div id="blocked_sms_settings">
						<table style="width:100%">
							<tr>
								<td colspan="2">
								    <textarea   name="mo_otp_blocked_phone_numbers"
								                rows="5"
									            placeholder="'.mo_(" Enter semicolon separated phone numbers
									            (with country code) that you want to block. Eg. +1XXXXXXXX ").'">'.
                                    mo_esc_string($otp_blocked_phones,"attr").
                                    '</textarea>
                                </td>
							</tr>
						</table>


						</div>

                </div>';
    echo '
	        <div id="chosenOtpType" class="mo_registration_table_layout mo-otp-half">
                    <table style="width:100%">
                        <tr>
                            <td>
                                <h3>
                                    '.mo_("CUSTOMIZE THE OTP FORMAT:").'
										<span style="float:right;margin-top:-10px;">
                                        <input  type="submit" '.mo_esc_string($alphanumeric_disabled,"attr").'
                                                name="save"
                                                class="button button-primary button-large"
                                                value="'.mo_("Save Settings").'"/>
                                    </span>
                                </h3><hr>
                            </td>

                             <tr>
                             <td> 
                                 <div>
                                        <i><b>Note</b>: This feature enables admins to customize the OTP Format, including lowercase, uppercase, and numeric characters.<b> For eg: aB23Fm</b></i>
                                    </div>
                         </td>
                    </tr>



                        </tr>';

$html   =           '<tr>
						 <td class="generated-otp-type-card mo-plan-ui">
										 <div class="mo_premium_option_text">
											<span style="color:red;">*</span>
												This is a Enterprise Plan feature. Check <a href="'.mo_esc_string($license_url,"url").'">Licensing Tab</a> to learn more.
												 </a>
										 </div>
                    </td>
                          </tr>';

	$html = apply_filters('mo_alphanumeric_card_ui', $html);

echo  $html;

echo '       </table>
            </div>';


    echo '
            <div id="globallyBannedPhone" class="mo_registration_table_layout mo-otp-half">
                    <table style="width:100%">
                        <tr>
                            <td>
                                <h3>
                                    '.mo_("GLOBALLY BANNED PHONE NUMBERS:").'
                                        <span style="float:right;margin-top:-10px;">
                                        <input  type="submit" '.mo_esc_string($globallybanned_disabled,"attr").'
                                                name="save"
                                                class="button button-primary button-large"
                                                value="'.mo_("Save Settings").'"/>
                                    </span>
                                </h3><hr>
                            </td>


                        <tr>
                             <td>
                                 <div >
                                        <i><b>Note</b>: This feature enables admins to block the use of globally banned phone number formats, hence increases security.<b> For eg: +1111111111 will get blocked.</b></i>
                                    </div>
                             </td>
                        </tr>


                        </tr>';

    $html1   =           '<tr>
                             <td class="globally-banned-phone-card mo-plan-ui">
                                 <div class="mo_premium_option_text">
                                    <span style="color:red;">*</span>
                                        This is a Enterprise Plan feature. Check <a href="'.mo_esc_string($license_url,"url").'">Licensing Tab</a> to learn more.
                                         </a>
                                 </div>
                                  </td>
                          </tr>';

    $html1 = apply_filters('mo_globally_banned_phone_view', $html1);

    echo $html1;

    echo '       </table>
               </div>';

    $master_otp_view_for_non_enterprise_plan  =           '<tr>
                                 <td  colspan="2" class="generated-otp-type-card">
                                                 <div class="mo_premium_option_card">
                                                    <span style="color:red;">*</span>
                                                        This is a Enterprise Plan feature. Check <a href="'.mo_esc_string($license_url,"url").'">Licensing Tab</a> to learn more.
                                                         </a>
                                                 </div>
                                 </td>
                         </tr>';

    $master_otp_ui_filter = apply_filters('mo_masterotp_card_ui', $master_otp_view_for_non_enterprise_plan, $disabled);

    echo '
            <div id="masterotp" class="mo_registration_table_layout mo-otp-half" style="width:750px;">
                    <table style="width:100%">
                        <tr>
                            <td colspan="2">
                                <h3>
                                    '.mo_("MASTER OTP").'
                                        <span style="float:right;margin-top:-10px;">
                                        <input  type="submit" '.$disabled.'
                                                name="save"
                                                class="button button-primary button-large"
                                                value="'.mo_("Save Settings").'"/>
                                    </span>
                                </h3><hr>
                            </td>
                        </tr>
                    '.$master_otp_ui_filter.'
            </table>
            </div>
            </form>
        </div>';
