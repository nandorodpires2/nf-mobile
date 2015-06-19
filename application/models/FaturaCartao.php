<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FaturaCartao
 *
 * @author Fernando
 */
class Model_FaturaCartao extends Zend_Db_Table {
    
    protected $_name = "fatura_cartao";    
    protected $_primary = "id_fatura_cartao";
    
    public function isVencimentoFaturaTabela($vencimento, $id_cartao) {
        
        $select = $this->select()
                ->from($this->_name, array('*'))
                ->where("vencimento_fatura = ?", $vencimento)
                ->where("id_fatura = ?", $id_cartao);
        
        $query = $this->fetchRow($select);
        
        if($query) {
            return true;
        }
        return false;
        
    }
    
    /**
     * 
     */
    public function getSaldoPago($id_cartao, $vencimento_fatura) {
        $select = $this->select()
                ->from(array('fc' => $this->_name), array(''))
                ->setIntegrityCheck(false)
                ->joinInner(array('mov' => 'movimentacao'), 'fc.id_movimentacao = mov.id_movimentacao', array(
                    'saldo' => new Zend_Db_Expr("sum(valor_movimentacao)")
                ))
                ->where("vencimento_fatura = ?", $vencimento_fatura)
                ->where("mov.id_cartao = ?", $id_cartao);
        
        return $this->fetchRow($select);
    }
    
}
