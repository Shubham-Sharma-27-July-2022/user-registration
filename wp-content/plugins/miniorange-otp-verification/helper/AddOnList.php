<?php

namespace OTP\Helper;

if(! defined( 'ABSPATH' )) exit;

use OTP\Objects\BaseAddOnHandler;
use OTP\Traits\Instance;


final class AddOnList
{
    use Instance;

    
    private $_addOns;

    
    private function __construct() { $this->_addOns = array(); }

    
    public function add($key, $form) { $this->_addOns[$key] = $form; }

    
    public function getList() { return $this->_addOns; }
}