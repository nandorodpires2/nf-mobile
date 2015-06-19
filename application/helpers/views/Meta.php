<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Meta
 *
 * @author Realter
 */
class View_Helper_Meta extends Zend_View_Helper_Abstract {

    public static function getProjecaoMeta($porcentagem) {
        
        $zendDate = new Zend_Date();
        $dias_mes = (int)$zendDate->get(Zend_Date::MONTH_DAYS);
        $dia_atual = (int)date('d');
        
        $projecao = ($porcentagem * $dias_mes) / $dia_atual;
        
        return $projecao;
        
    } 
    
    public static function getTotalGastosMetaMes($id_categoria) {
        
        $modelCategoria = new Model_Categoria();
        $totalGastos = $modelCategoria->getTotalGastoCategoriaMes($id_categoria);
        
        return $totalGastos->total;
        
    }

    /**
     * retorna a porcentagem total dos gastos
     */
    public static function getPorcentagemTotalGastos($total_meta, $total_gasto) {        
        
        $total_gasto *= -1;
        
        $porcentagem = ($total_gasto * 100) / $total_meta;
        
        return $porcentagem;        
        
    }
    
    public static function getStatusMeta($porcentagem) {
        
        $porcentagem = number_format($porcentagem, 2, '.', '.');
        
        $class = "";
        
        if ($porcentagem <= 50) {
            $class = "text-success padding-10px bg-success bold";
        } elseif ($porcentagem > 50 && $porcentagem <= 80) {
            $class = "text-warning padding-10px bg-warning bold";
        } elseif ($porcentagem > 80 && $porcentagem < 100) {
            $class = "text-danger padding-10px bg-danger bold";
        } else {
            $class = "text-black padding-10px bg-danger bold";
        }
        
        return "<span class='{$class}'>{$porcentagem}";;
        
    }
    
}

