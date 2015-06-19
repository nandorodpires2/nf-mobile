<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Auth
 *
 * @author rosousas
 */
class Plugin_Auth extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        
        $moduleName = $request->getModuleName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
                 
        $auth = Zend_Auth::getInstance();      
        
        if (($moduleName !== 'mobile') && $actionName !== 'login') {               
            if (!$auth->hasIdentity()) {
                $request->setModuleName('mobile')
                        ->setControllerName('usuarios')
                        ->setActionName('login')
                        ->setDispatched();
            }
        }
        
    }
        
    public function getDevice() {
        return $this->_device;
    }
    
    private function isMobile() {
        
        return true;
        
        $iphone = strpos($this->_device,"iPhone");
        $ipad = strpos($this->_device,"iPad");
        $android = strpos($this->_device,"Android");
        $palmpre = strpos($this->_device,"webOS");
        $berry = strpos($this->_device,"BlackBerry");
        $ipod = strpos($this->_device,"iPod");
        $symbian =  strpos($this->_device,"Symbian");
        
        if ($iphone || $ipad || $android || $palmpre || $berry || $ipod || $symbian) {
            return true;
        }
        
        return false;
        
    }
    
}


