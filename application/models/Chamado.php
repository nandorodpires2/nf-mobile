<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Chamado
 *
 * @author Fernando Rodrigues
 */
class Model_Chamado extends Zend_Db_Table {

    protected $_name = "chamado";
    
    protected $_primary = "id_chamado";
    
    /**
     * retorna os chamados
     */
    public function getChamados() {
            $select = $this->select()
                ->from(array('c' => $this->_name), array('*'))
                ->setIntegrityCheck(false)
                ->joinInner(array('tc' => 'tipo_chamado'), 'c.id_tipo_chamado = tc.id_tipo_chamado', array('*'))
                ->joinInner(array('u' => 'usuario'), 'c.id_usuario = u.id_usuario', array(
                    'u.nome_completo',
                    'u.email_usuario',
                    'u.cpf_usuario'
                ))
                ->order("c.status asc")
                ->order("c.data_abertura asc");
        
        return $this->fetchAll($select);
    }

    /**
     * retorna os chamados do usuario
     */
    public function getChamadosUsuario($id_usuario, $status = null) {
        $select = $this->select()
                ->from(array('c' => $this->_name), array('*'))
                ->setIntegrityCheck(false)
                ->joinInner(array('tc' => 'tipo_chamado'), 'c.id_tipo_chamado = tc.id_tipo_chamado', array('*'))
                ->where("c.id_usuario = ?", $id_usuario)
                ->order("c.status asc");
        
        if (null !== $status) {
            $select->where('c.status = ?', $status);
        }
        
        return $this->fetchAll($select);
    }

    /**
     * busca dados dos chamado
     */
    public function getDadosChamado($id_chamado, $id_usuario = null) {
        
        $select = $this->select()
                ->from(array('c' => $this->_name), array('*'))
                ->setIntegrityCheck(false)
                ->joinInner(array('tc' => 'tipo_chamado'), 'c.id_tipo_chamado = tc.id_tipo_chamado', array('*'))
                ->joinInner(array('u' => 'usuario'), 'c.id_usuario = u.id_usuario', array(
                    'u.nome_completo',
                    'u.email_usuario',
                    'u.cpf_usuario'
                ))
                ->where("c.id_chamado = ?", $id_chamado);
        if ($id_usuario) {
            $select->where("c.id_usuario = ?", $id_usuario);
        }                
        
        return $this->fetchRow($select);
        
    }
    
}

