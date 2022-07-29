<?php

namespace OTP\Objects;

class PluginPageDetails
{
    function __construct($_pageTitle, $_menuSlug, $_menuTitle, $_tabName, $request_uri, $_view, $_id, $_css="", $_showInNav = true)
    {
        $this->_pageTitle =  $_pageTitle;
        $this->_menuSlug = $_menuSlug;
        $this->_menuTitle = $_menuTitle;
        $this->_tabName = $_tabName;
        $this->_url = add_query_arg(['page'=>$this->_menuSlug], $request_uri );
        $this->_url = remove_query_arg(['addon','form','sms','subpage'], $this->_url );
        $this->_view = $_view;
        $this->_id = $_id;
        $this->_showInNav = $_showInNav;
        $this->_css = $_css;
    }

    
    public $_pageTitle;

    
    public $_menuSlug;


    
    public $_menuTitle;


    
    public $_tabName;

    
    public $_url;

    
    public $_view;

    
    public $_id;

    
    public $_showInNav;

    
    public $_css;
}