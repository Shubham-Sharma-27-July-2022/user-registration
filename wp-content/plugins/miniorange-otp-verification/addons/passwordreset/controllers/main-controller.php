<?php

use OTP\Addons\PasswordReset\Handler\UMPasswordResetAddOnHandler;


$handler        = UMPasswordResetAddOnHandler::instance();
$registered 	= $handler->moAddOnV();
$disabled  	 	= !$registered ? "disabled" : "";
$current_user 	= wp_get_current_user();
$controller 	= UMPR_DIR . 'controllers/';
$addon          = add_query_arg( array('page' => 'addon'), remove_query_arg('addon',$_SERVER['REQUEST_URI']));

if(isset( $_GET[ 'addon' ]))
{
    switch($_GET['addon'])
    {
        case 'umpr_notif':
            include $controller . 'UMPasswordReset.php'; break;
    }
}