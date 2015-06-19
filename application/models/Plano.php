<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Plano
 *
 * @author Realter
 */
class Model_Plano extends Zend_Db_Table {

    protected $_name = "plano";
    
    protected $_primary = "id_plano";
    
    /**
     * getQueryAll
     */
    protected function getQueryAll() {
        
        $select = $this->select()
                ->from(array('p' => $this->_name), array('*'))
                ->setIntegrityCheck(false);
        
        return $select;
        
    }
    
    /**
     * 
     */
    public function getPlanoById($id_plano) {
        $select = $this->getQueryAll()
                ->where("p.id_plano = ?", $id_plano);
        
        return $this->fetchRow($select);
                
    }

    /**
     * retorna os planos do usuario
     */
    public function getPlanosUsuario() {
        
        $select = $this->select()
                ->from(array('p' => $this->_name), array(
                    'p.id_plano',
                    'p.descricao_plano',
                    'p.tempo_plano',
                    'p.unidade_tempo_plano',                   
                    'p.usuario_plano',
                    'pv.usuario',
                    'valor_mes' => "case
                        when (p.unidade_tempo_plano = 'meses') then
                            pv.valor_plano / p.tempo_plano
                        else 
                            pv.valor_plano / (12 * p.tempo_plano)
                    end"
                ))
                ->setIntegrityCheck(false)
                ->joinLeft(array('pv' => 'plano_valor'), 'p.id_plano = pv.id_plano', array(
                    'pv.valor_plano'
                ))
                ->where('p.usuario_plano = ?', 1)
                ->where('p.ativo_plano = ?', 1)
                ->where('pv.usuario = ?', 1)
                ->where('p.cobranca = ?', 1);
        
        return $this->fetchAll($select);
        
    }
    
    /**
     * retorna o plano de experiencia 
     */
    public function getPlanoExperiencia() {
        $select = $this->select()
            ->from(array('p' => $this->_name), array(
                'p.id_plano',
                'p.descricao_plano',
                'p.tempo_plano',
                'p.unidade_tempo_plano'                                       
            ))                
            ->where('p.usuario_plano = ?', 1)                
            ->where('p.cobranca = ?', 0)
            ->where('p.ativo_plano = ?', 1);
        
        return $this->fetchRow($select);
    }
    
}

