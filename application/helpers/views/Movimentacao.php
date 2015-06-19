<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Movimentacao
 *
 * @author Fernando Rodrigues
 */
class View_Helper_Movimentacao extends Zend_View_Helper_Abstract {
    
    public static function getClassRowMovimentacao($id_tipo_movimentacao) {
        
        $class = "";
        
        if ($id_tipo_movimentacao == 1) {
            $class = "text-green";
        } elseif ($id_tipo_movimentacao == 2) {
            $class = "text-red";
        } elseif ($id_tipo_movimentacao == 3) {
            $class = "text-orange";
        } elseif ($id_tipo_movimentacao == 4) {
            $class = "text-blue";
        } elseif ($id_tipo_movimentacao == 5) {
            $class = "";
        } else {
            $class = "";
        }
        
        return $class;
        
    }
    
    public static function getTotalGastoCategoriaMes($id_categoria) {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $modelMovimentacao = new Model_Movimentacao();
        $total = $modelMovimentacao->getTotalRealizadoCategoriaMes($id_categoria, $id_usuario);
        
        return $total->total_categoria;
        
    }
    
}
