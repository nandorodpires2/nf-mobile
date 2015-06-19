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
class Form_Admin_Categoria_Cadastro extends Zend_Form {
    
    public function init() {
        
        // descricao_categoria
        $this->addElement('text', 'descricao_categoria', array(
            'label' => 'Categoria',
            'required' => true,
            'class' => 'form-control'
        ));
        
        // submit
        $this->addElement('submit', 'submit', array(
            'label' => 'Cadastrar',
            'class' => 'btn btn-submit'
        ));
        
    }
    
}
