<?php

namespace OTP;

if(! defined( 'ABSPATH' )) exit;

final class SplClassLoader
{
    
    private $_fileExtension = '.php';
    
    private $_namespace;
    
    private $_includePath;
    
    private $_namespaceSeparator = '\\';

    public function __construct($ns = null, $includePath = null)
    {
        $this->_namespace = $ns;
        $this->_includePath = $includePath;
    }

    
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    public function loadClass($className)
    {
        if (null === $this->_namespace || $this->isSameNamespace($className)) {
            $fileName = '';
            $namespace = '';
            if (false !== ($lastNsPos = strripos($className, $this->_namespaceSeparator))) {
                $namespace = strtolower(substr($className, 0, $lastNsPos));
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->_fileExtension;
                        $fileName = str_replace("otp",MOV_NAME,$fileName);
            require ($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '') . $fileName;
        }
    }

    private function isSameNamespace($className)
    {
        return $this->_namespace . $this->_namespaceSeparator ===
            substr($className, 0, strlen($this->_namespace . $this->_namespaceSeparator));
    }
}