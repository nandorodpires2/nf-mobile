<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Messages
 *
 * @author Fernando Rodrigues
 */
class View_Helper_Messages extends Zend_View_Helper_Abstract {
    
    public static function render($messages, $alerts = null) {        
        $render = "";
        
        if ($alerts && is_array($alerts)) {            
            foreach ($alerts as $alert) {
                $render .= "<div class='{$alert['class']}'>{$alert['message']}</div>";
            }
        }        
        
        if ($messages) {
            foreach ($messages as $message) {
                $render .= "<div class='{$message['class']}'>{$message['message']}</div>";
            }
        }        
        
        return $render;        
        
    }
    
}
