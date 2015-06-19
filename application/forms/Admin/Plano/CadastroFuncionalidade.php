<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cadastro
 *
 * @author Fernando
 */
class Form_Admin_Plano_CadastroFuncionalidade extends Zend_Form {
    
    public function init() {
        
        // busca os modulos
        $modelFuncionalidade = new Model_Funcionalidade();
        $modulos = $modelFuncionalidade->getModulesPlano();
                
        foreach ($modulos as $modulo) {
            
            // busca as funcionalidade
            $funcionalidades = $modelFuncionalidade->getFuncionalidadesByModule($modulo->module);            
            $multioptions = array();
            foreach ($funcionalidades as $funcionalidade) {
                $multioptions[$funcionalidade->id_funcionalidade] = '  ' . $funcionalidade->descricao_permissao;
            }            
            
            $this->addElement('MultiCheckbox', $modulo->module, array(
                'label' => strtoupper($modulo->module),
                'registerInArray' => true,
                'multioptions' => $multioptions
            ));
        }
        
        // submit
        $this->addElement('submit', 'submit', array(
            'label' => 'Salvar',
            'class' => 'btn btn-submit'
        ));
        
        // id_plano (hidden)
        $this->addElement('hidden', 'id_plano');
        
        
    }
    
}
