<?php

use \OTP\Addons\CustomMessage\Handler\CustomMessages;

$content 		   = '';
$editorId 		   = 'customEmailMsgEditor';
$templateSettings  = [
    'media_buttons'=>false,
    'textarea_name'=>'content',
    'editor_height' => '170px',
    'wpautop'=>false
];

$handler           = CustomMessages::instance();
$nonce 			   = $handler->getNonceValue();
$postUrl           = admin_post_url();

include MCM_DIR . 'views/custom.php';