<?php

echo' 	<div class="mo_otp_form" id="'.mo_esc_string(get_mo_class($handler),"attr").'">
 	        <input type="checkbox" '.mo_esc_string($disabled,"attr").' 
                id="wc_social" 
                class="app_enable" 
                name="mo_customer_validation_wc_social_login_enable" 
                value="1"
			    '.mo_esc_string($wc_social_login,"attr").' /><strong>'.mo_esc_string($form_name,"attr").'</strong>';

echo' </div>';

