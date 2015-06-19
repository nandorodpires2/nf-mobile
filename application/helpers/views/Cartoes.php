<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cartoes
 *
 * @author Fernando Rodrigues
 */
class View_Helper_Cartoes extends Zend_View_Helper_Abstract {
    
    public static function getBandeiraCartao($bandeira) {
        
        $view = Zend_Layout::getMvcInstance()->getView();
        $img_path = $view->baseUrl() . '/views/img/cartoes/';
        
        switch ($bandeira) {
            case 'visa':
                $img_path .= "icon_visa.png";
                break;
            case 'mastercard':
                $img_path .= "icon_mastercard.png";
                break;
            default:
                return 'Nunhuma';
                break;
        }
        
        $return = "<img class='img' src='{$img_path}' />";
        
        return $return;
        
    }
    
    public static function getSaldoCartao($id_cartao) {
        
    }
    
    /**
     * verifica se uma fatura ja esta fechado
     */
    public static function fechadaFatura($data) {
        
        $zendDateNow = new Zend_Date();
        $zendDate = new Zend_Date($data);
        
        if (!$zendDate->isLater($zendDateNow->now()->subDay(1))) {
            return true;
        }
        return false;
        
    }
    
}
