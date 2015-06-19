<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VwLancamentoCartao
 *
 * @author Realter
 */
class Model_VwLancamentoCartao extends Zend_Db_Table {

    protected $_name = 'vw_lancamento_cartao';
    
    protected $_primary = 'id_cartao';
    
    /**
     * fatura(s) atual
     */
    public function getFaturasAtual($id_usuario) {
        
        $select = $this->select()
                ->from(array('vlc' => $this->_name), array(
                    'vlc.id_cartao',
                    'vlc.bandeira_cartao',
                    'vlc.descricao_cartao',
                    'vlc.fim_fatura',
                    'vencimento_fatura' => 'date(vlc.vencimento_fatura)',
                    'valor_fatura' => 'sum(vlc.valor_movimentacao)'
                ))                
                ->where("vlc.id_usuario = ?", $id_usuario)
                ->where("year(vlc.vencimento_fatura) = year(now())")
                ->where("vlc.realizado = ?", 1)
                ->where("month(vlc.vencimento_fatura) = month(now())")
                ->group("vlc.id_cartao")
                ->group("vlc.vencimento_fatura");
        
        return $this->fetchAll($select);
        
    }
    
    /**
     * fatura(s) atual
     */
    public function getProximaFaturas($id_cartao, $id_usuario) {
        
        $select = $this->select()
                ->from(array('vlc' => $this->_name), array(
                    'vlc.vencimento_fatura',
                    'valor_fatura' => 'sum(vlc.valor_movimentacao)'
                ))                
                ->where("vlc.id_usuario = ?", $id_usuario)
                ->where("vlc.id_cartao = ?", $id_cartao)
                ->group("vlc.vencimento_fatura");
        
        return $this->fetchAll($select);
        
    }
    
    /**
     * fatura(s) atual
     */
    public function getFaturas($id_usuario, $id_cartao = null) {
        
        $select = $this->select()
                ->from(array('vlc' => $this->_name), array(
                    "vlc.id_cartao",			
                    "vlc.vencimento_fatura",
                    "saldo_atual" => new Zend_Db_Expr("sum(vlc.valor_movimentacao)"),
                    "valor_pago" => 0
                ))                
                ->where("vlc.id_usuario = ?", $id_usuario)
                ->group("vlc.vencimento_fatura")
                ->group("vlc.id_cartao");
        
        if ($id_cartao) {
            $select->where('vlc.id_cartao = ?', $id_cartao);
        }        
        
        return $this->fetchAll($select);
        
    }
    
    /**
     * fatura(s) atual
     */
    public function getFaturaByVencimento($vencimento, $id_usuario) {
        
        $select = $this->select()
                ->from(array('vlc' => $this->_name), array(
                    "vlc.id_cartao",			
                    "vlc.vencimento_fatura",
                    "saldo_atual" => new Zend_Db_Expr("sum(vlc.valor_movimentacao)"),
                    "valor_pago" => 0
                ))                
                ->where("vlc.vencimento_fatura = ?", $vencimento)
                ->where("vlc.id_usuario = ?", $id_usuario)
                ->group("vlc.vencimento_fatura");
        
        return $this->fetchRow($select);
        
    }
    
    /**
     * retorna os lancamentos da fatura
     */
    public function getLancamentosFatura($id_cartao, $vencimento_fatura, $id_usuario) {
        
        $select = $this->select()
                ->from(array('vlc' => $this->_name), array('*'))
                ->setIntegrityCheck(false)
                ->joinInner(array('mov' => 'movimentacao'), 'vlc.id_movimentacao = mov.id_movimentacao', array())
                ->joinLeft(array('cat' => 'categoria'), 'mov.id_categoria = cat.id_categoria', array('*'))
                ->where('vlc.id_cartao = ?', $id_cartao)
                ->where('vlc.vencimento_fatura = ?', $vencimento_fatura)
                ->where('vlc.id_usuario = ?', $id_usuario)
                ->order('vlc.data_movimentacao asc');
        
        return $this->fetchAll($select);
        
    }
    
    /**
     * retorna total da fatura
     */
    public function getTotalFatura($id_cartao, $vencimento_fatura, $id_usuario) {
        
        $select = $this->select()
                ->from(array('vlc' => $this->_name), array(
                    'descricao_cartao',
                    'bandeira_cartao',
                    'vencimento_fatura',
                    'valor_fatura' => new Zend_Db_Expr('ifnull(sum(vlc.valor_movimentacao), 0)')
                ))           
                ->setIntegrityCheck(false)
                ->where("vlc.id_usuario = ?", $id_usuario)
                ->where("vlc.id_cartao = ?", $id_cartao)
                ->where("vlc.vencimento_fatura = ?", $vencimento_fatura);
        
        return $this->fetchRow($select);
        
    }
    
}

