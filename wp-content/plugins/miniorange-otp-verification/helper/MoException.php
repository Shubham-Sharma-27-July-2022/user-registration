<?php

namespace OTP\Helper;

if(! defined( 'ABSPATH' )) exit;

class MoException extends \Exception
{
    private $moCode;

    public function __construct($moCode,$message,$code)
    {
        $this->moCode = $moCode;
        parent::__construct($message, $code, NULL);
    }

    
    public function getMoCode(){ return $this->moCode; }
}