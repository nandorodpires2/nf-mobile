<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PagseguroController
 *
 * @author Fernando Rodrigues
 */
class Mobile_PagseguroController extends Zend_Controller_Action {
    
    public function init() {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function testAction() {        
        echo Zend_Date::now();        
    }
    
}
