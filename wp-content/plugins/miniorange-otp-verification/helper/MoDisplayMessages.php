<?php

namespace OTP\Helper;

if(! defined( 'ABSPATH' )) exit;


class MoDisplayMessages
{
    private $_message;
    private $_type;

    function __construct( $message,$type )
    {
        $this->_message = $message;
        $this->_type = $type;

        
        
        
        add_action( 'admin_notices', array( $this, 'render' ) );
    }

    function render()
    {
        switch ($this->_type)
        {
            case 'CUSTOM_MESSAGE':
                echo  mo_($this->_message);
                break;
            case 'NOTICE':
                echo '<div style="margin-top:1%;"'.
                    'class="is-dismissible notice notice-warning mo-admin-notif">'.
                    '<p>'.mo_($this->_message).'</p>'.
                    '</div>';
                break;
            case 'ERROR':
                echo '<div style="margin-top:1%;"'.
                    'class="notice notice-error is-dismissible mo-admin-notif">'.
                    '<p>'.mo_($this->_message).'</p>'.
                    '</div>';
                break;
            case 'SUCCESS':
                echo '<div  style="margin-top:1%;"'.
                    'class="notice notice-success is-dismissible mo-admin-notif">'.
                    '<p>'.mo_($this->_message).'</p>'.
                    '</div>';
                break;
        }
    }

    function showMessageDivAddons(){
        switch ($this->_type) {
            case 'MO_ADDON_MESSAGE_CUSTOM_MESSAGE_SUCCESS':
                echo '<div  style="margin-top:1%;"'.
                    'class="notice notice-success is-dismissible mo-admin-notif">'.
                    '<p>'.mo_($this->_message).'</p>'.
                    '</div>';
                                break;
            case 'MO_ADDON_MESSAGE_CUSTOM_MESSAGE_ERROR':
                echo '<div style="margin-top:1%;"'.
                    'class="notice notice-error is-dismissible mo-admin-notif">'.
                    '<p>'.mo_($this->_message).'</p>'.
                    '</div>';
                break;
        }
    }
}