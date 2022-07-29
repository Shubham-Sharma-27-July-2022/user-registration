<?php

namespace OTP\Objects;

use OTP\Helper\MoUtility;

abstract class BaseAddOnHandler extends BaseActionHandler implements AddOnHandlerInterface
{
    
    protected $_addOnKey;

    
    protected $_addOnDesc;

    
    protected $_addOnName;

    
    protected $_settingsUrl;

    
    protected $_addOnDocs;

    
    protected $_addOnVideo;


    
    public function __construct()
    {
        parent::__construct();
        $this->setAddonKey();
        $this->setAddOnDesc();
        $this->setAddOnName();
        $this->setSettingsUrl();
        $this->setAddOnDocs();
        $this->setAddOnVideo();
    }

    
    public function getAddOnKey(){ return $this->_addOnKey; }

    
    public function getAddOnDesc(){ return $this->_addOnDesc; }

    
    public function getAddOnName(){ return $this->_addOnName; }

      
    public function getAddOnDocs(){ return $this->_addOnDocs; }

    
    public function getAddOnVideo(){ return $this->_addOnVideo; }
    
    
    public function getSettingsUrl(){ return $this->_settingsUrl; }

    
    public function moAddOnV() { return MoUtility::micr() && MoUtility::mclv(); }
}