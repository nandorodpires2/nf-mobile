<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cartao
 *
 * @author Realter
 */
class Model_Cartao extends Zend_Db_Table {
    
    protected $_name = "cartao";
    
    protected $_primary = "id_cartao";
    
    public function getCartoesUsuario($id_usuario, $status = null) {
        
        $select = $this->select()
                ->from(array('ct' => $this->_name), array(
                    '*'
                ))
                ->where("ct.id_usuario = ?", $id_usuario);                
                
        if (null !== $status) {
            $select->where("ct.ativo_cartao = ?", $status);
        }
        
        return $this->fetchAll($select);
        
    }
    
    public function getCartaoById($id_cartao, $id_usuario) {
        
        $select = $this->select()
                ->from(array('ct' => $this->_name), array('*'))
                ->where("ct.id_usuario = ?", $id_usuario)                
                ->where("ct.id_cartao = ?", $id_cartao);                
                
        return $this->fetchRow($select);
        
    }
    
    /**
     * retorna a qtde de cartoes ativos
     */
    public function getQtdeCartoesAtivos($id_usuario) {
        $select = $this->select()
                ->from($this->_name, array(
                    'cartoes' => "count(distinct id_cartao)"
                ))
                ->where("id_usuario = ?", $id_usuario)
                ->where("ativo_cartao = ?", 1);
        
        $query = $this->fetchRow($select);
        
        return $query->cartoes;
    }
}

