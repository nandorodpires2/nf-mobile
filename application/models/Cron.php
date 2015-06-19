<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cron
 *
 * @author Fernando Rodrigues
 */
class Model_Cron extends Zend_Db_Table {
    
    protected $_name = "movimentacao";
    protected $_primary = "id_movimentacao";
        
    /**
     * Query All
     * @return type
     */
    public function getQueryAll() {
        
        $select = $this->select()
            ->from(array('mov' => $this->_name), array(
                'pendencias' => new Zend_Db_Expr("count(*)"),
                '*'
            ))
            ->setIntegrityCheck(false)
            ->joinInner(array('usu' => 'usuario'), 'mov.id_usuario = usu.id_usuario', array('*'))
            ->joinInner(array('cat' => 'categoria'), 'mov.id_categoria = cat.id_categoria', array('*'))
            ->joinLeft(array('cta' => 'conta'), 'mov.id_conta = cta.id_conta', array('*'))
            ->joinLeft(array('ctr' => 'cartao'), 'mov.id_cartao = ctr.id_cartao', array('*'));
                    
        return $select;
        
    }
    
    /**
     * 
     * @return type
     */
    public function getCountPendencias() {
        
        $select = $this->getQueryAll()
            ->where("mov.realizado = ?", 0)
            ->where("mov.id_tipo_movimentacao <> ?", 3)
            ->where("mov.data_movimentacao < DATE_SUB(NOW(), INTERVAL 1 DAY)")
            ->group("mov.id_usuario");
        
        return $this->fetchAll($select);
        
    }
    
    public function getLastLoginUsuarios() {
        
        $select = $this->getQueryAll()
            ->joinInner(array('usl' => 'usuario_login'), 'usu.id_usuario = usl.id_usuario', array(
                'ultimo_login' => new Zend_Db_Expr("max(usl.data)")
            ))
            ->group("usu.id_usuario");
        
        return $this->fetchAll($select);
        
    }
    
}
