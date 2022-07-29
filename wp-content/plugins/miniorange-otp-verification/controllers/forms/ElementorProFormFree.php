<?php

use OTP\Handler\Forms\ElementorProFormFree;

$handler 						        = ElementorProFormFree::instance();
$form_name                              = $handler->getFormName();
get_plugin_form_link($handler->getFormDocuments());
include MOV_DIR . 'views/forms/ElementorProFormFree.php';