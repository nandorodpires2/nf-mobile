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
class Model_Meta extends Zend_Db_Table {

    protected $_name = "meta";
    
    protected $_primary = "id_meta";
 
    /**
     * 
     */
    public function getMetasUsuario($id_usuario) {
        
        $select = $this->select()
                ->from(array('met' => $this->_name), array(                    
                    'met.id_meta',
                    'met.valor_meta',
                    'met.id_categoria'
                ))
                ->setIntegrityCheck(false)
                ->joinInner(array('cat' => 'categoria'), 'met.id_categoria = cat.id_categoria', array(
                    'cat.descricao_categoria'
                ))                
                ->where("met.id_usuario = ?", $id_usuario)  
                ->where("met.mes_meta = month(now()) and met.ano_meta = year(now())")                  
                ->order("cat.descricao_categoria asc");
                        
        return $this->fetchAll($select);        
        
    }    
    
    public function getMetaByIdMeta($id_meta, $id_usuario) {
        
        $select = $this->select()
                ->from(array('met' => $this->_name), array(                    
                    'met.id_meta',
                    'met.valor_meta',
                    'met.id_categoria',
                    'met.mes_meta',
                    'met.ano_meta'
                ))
                ->setIntegrityCheck(false)
                ->joinInner(array('cat' => 'categoria'), 'met.id_categoria = cat.id_categoria', array(
                    'cat.descricao_categoria'
                ))                
                ->where("met.id_usuario = ?", $id_usuario)  
                ->where("met.id_meta = ?", $id_meta);
                        
        return $this->fetchRow($select);        
        
    }
    
    /**
     * 
     */
    public function getGastosMetasUsuario($id_usuario) {
        
        $select = $this->select()
                ->from(array('met' => $this->_name), array(
                    'met.id_meta',
                    'met.valor_meta'
                ))
                ->setIntegrityCheck(false)
                ->joinInner(array('cat' => 'categoria'), 'met.id_categoria = cat.id_categoria', array(
                    'cat.id_categoria',
                    'cat.descricao_categoria'
                ))                
                ->where("met.id_usuario = ?", $id_usuario)
                ->where("met.mes_meta = month(now())")
                ->where("met.ano_meta = year(now())")                
                ->group("cat.id_categoria")
                ->order("cat.descricao_categoria asc");
        
        return $this->fetchAll($select);        
        
    }
    
    /**
     * busca o total orçado de metas para o mes
     */
    public function getTotalMetaMes($id_usuario, $mes, $ano) {
        
        $select = $this->select()
                ->from($this->_name, array(
                    'total' => 'sum(valor_meta)'
                ))
                ->where("id_usuario = ?", $id_usuario)
                ->where("mes_meta = ?", $mes)
                ->where("ano_meta = ?", $ano);
        
        $query = $this->fetchRow($select);
        return $query->total;
        
    }
    
    /**
     * get Total gasto meta
     */
    public function getTotalGastoMetas($id_usuario, $mes, $ano) {
        $select = $this->select()
                ->from(array('met' =>$this->_name), array(
                    
                ))
                ->setIntegrityCheck(false)
                ->joinInner(array('mov' => 'movimentacao'), 'met.id_categoria = mov.id_categoria and met.id_usuario = mov.id_usuario', array(
                    "total" => new Zend_Db_Expr("sum(mov.valor_movimentacao) * -1")
                ))
                ->where("met.id_usuario = ?", $id_usuario)
                ->where("mov.realizado = ?", 1)
                ->where("mov.id_tipo_movimentacao in (2,3)")
                ->where("met.mes_meta = ?", $mes)
                ->where("met.ano_meta = ?", $ano)
                ->where("month(mov.data_movimentacao) = met.mes_meta")
                ->where("year(mov.data_movimentacao) = met.ano_meta");
        
        $query = $this->fetchRow($select);
        return $query->total;
    }    
    
}

