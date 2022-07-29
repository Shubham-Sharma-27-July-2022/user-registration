<?php

use OTP\Addons\WcSMSNotification\Helper\MoWcAddOnUtility;

echo'	<div class="mo_registration_divided_layout mo-otp-full">
				<div class="mo_registration_table_layout mo-otp-center">';

					MoWcAddOnUtility::is_addon_activated();

	echo'			<table style="width:100%">
						<form name="f" method="post" action="" id="mo_wc_sms_notif_settings">
							<input type="hidden" name="option" value="mo_wc_sms_notif_settings" />
							<tr>
								<td>
									<h2>'.mo_("WOOCOMMERCE NOTIFICATION SETTINGS").'
                                        <span style="float:right;margin-top:-10px;">
                                            <a href="'.esc_url($addon).'" id="goBack" class="button button-primary button-large">'.mo_("Go Back").'</a>
                                        </span>
									</h2>
									<hr>
								</td>
							</tr>
							<tr>
								<td>'.mo_("SMS notifications sent from WooCommerce are listed below. Click on one to configure it.").'</td>
							</tr>
							<tr>
								<table class="msn-table-list" cellspacing="0">
									<thead>
										<tr>
											<th class="msn-table-list-status" style="width:15px;">Enabled</th>
											<th class="msn-table-list-name">SMS Type</th>
											<th class="msn-table-list-recipient">Recipient</th>
											<th class="msn-table-list-actions"></th>						
										</tr>
									</thead>
									<tbody>';

										show_notifications_table($notification_settings);

	echo '							</tbody>
								</table>
							</tr>
						</form>	
					</table>
				</div>
			</div>';