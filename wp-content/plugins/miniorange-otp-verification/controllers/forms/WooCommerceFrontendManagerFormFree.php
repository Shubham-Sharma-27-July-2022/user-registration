<?php

use OTP\Handler\Forms\WooCommerceFrontendManagerFormFree;

$handler 						        = WooCommerceFrontendManagerFormFree::instance();
$form_name                              = $handler->getFormName();

get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/WooCommerceFrontendManagerFormFree.php';