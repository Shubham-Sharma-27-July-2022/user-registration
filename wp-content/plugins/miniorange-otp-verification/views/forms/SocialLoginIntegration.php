<?php


echo' 	<div class="mo_otp_form"><input type="checkbox" '.mo_esc_string($disabled,"attr").' id="mo_social_login_plugin" class="app_enable" name="mo_customer_validation_mo_social_login_enable" value="1"
			 '.mo_esc_string($mo_social_login_enabled,"attr").'/><strong>'.mo_esc_string( mo_( "miniOrange Social Login Integration" ),"attr").'<i>'.mo_esc_string( mo_( " (SMS Verification only) " ),"attr").'</i></strong>

		</div>';
