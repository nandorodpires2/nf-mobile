<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cartao
 *
 * @author Fernando
 */
class Plugin_Cartao extends Zend_Controller_Plugin_Abstract {
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            
        }
        
    }
    
}
