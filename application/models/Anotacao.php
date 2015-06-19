<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Anotacao
 *
 * @author Fernando Rodrigues
 */
class Model_Anotacao extends Zend_Db_Table {
    
    protected $_name = "anotacao";
    
    protected $_primary = "id_anotacao";
        
    public function getAnotacoes() {
        $select = $this->select()
                ->from(array('a' => $this->_name), array('*'))
                ->order("a.status_anotacao asc");
                
        return $this->fetchAll($select);
       
    }
    
}
