<?php

use OTP\Helper\MoUtility;



echo  '<strong><center>'.$form_name.'</center></strong>';
echo  '<div class="mo_otp_note">
                            '.mo_esc_string(mo_('The '),"attr").'<b>'.mo_esc_string(mo_('Elementor PRO'),"attr").'</b>'.mo_esc_string(mo_(' plugin has been separately integated to provide users with Phone verification or Email Verification via OTP on the forms created by it.'),"attr").'<br>'.mo_esc_string(mo_('To get access to this premium feature, please kindly contact us at '),"attr").'<a style="cursor:pointer;" onClick="otpSupportOnClick();"><u> otpsupport@xecurify.com</u></a></div>';