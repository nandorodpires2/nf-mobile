<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author Fernando
 */
class Model_Log extends Zend_Db_Table {
    
    protected $_name = "log";
    protected $_primary = "id_log";
    
    public function getQueryAll() {
        $select = $this->select()
                ->from(array('l' => $this->_name), array('*'))
                ->setIntegrityCheck(false)
                ->joinLeft(array('u' => 'usuario'), 'l.id_usuario = u.id_usuario', array('*'));
        
        return $select;
    }
    
    public function getLogById($id_log) {
        $select = $this->getQueryAll()
                ->where("id_log = ?", $id_log);
        return $this->fetchRow($select);
    }
    
    public function getLogs() {
        $select = $this->getQueryAll()
                ->order(array("data_log desc"))
                ->limit(100);                
        return $this->fetchAll($select);
    }
    
}
