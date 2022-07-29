<?php

use OTP\Helper\MoConstants;

$current_user 	= wp_get_current_user();
$email 			= get_mo_option("admin_email");
$phone 			= get_mo_option("admin_phone");
$phone          = $phone ? $phone : '';
$support        = MoConstants::FEEDBACK_EMAIL;

include MOV_DIR . 'views/support.php';