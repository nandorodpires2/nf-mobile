<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsuarioAtivar
 *
 * @author Fernando Rodrigues
 */
class Model_UsuarioAtivar extends Zend_Db_Table {
    
    protected $_name = "usuario_ativar";
    
    protected $_primary = "id_usuario";
    
    public function getDadosUsuarioHash($hash) {
        $select = $this->select()
                ->from(array('ua' => $this->_name), array('*'))
                ->setIntegrityCheck(false)
                ->joinInner(array('u' => 'usuario'), 'ua.id_usuario = u.id_usuario', array('*'))
                ->where('hash = ?', $hash);
        
        return $this->fetchRow($select);
    }
    
}
