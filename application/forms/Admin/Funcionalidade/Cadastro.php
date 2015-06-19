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
class Form_Admin_Funcionalidade_Cadastro extends Zend_Form {
    
    public function init() {
        
        // titulo
        $this->addElement('text', 'titulo', array(
            'label' => 'Título',
            'required' => true,
            'class' => 'form-control'
        ));
        
        // descricao
        $this->addElement('text', 'descricao_permissao', array(
            'label' => 'Descrição',
            'required' => true,
            'class' => 'form-control'
        ));
        
        // module
        $this->addElement('select', 'module', array(
            'label' => 'Module',
            'required' => true,
            'multioptions' => array(
                '' => 'Selecione...',
                'cliente' => 'Cliente',
                'admin' => 'Admin'
            ),
            'class' => 'form-control'
        ));
        
        // controller
        $this->addElement('text', 'controller', array(
            'label' => 'Controller',
            'required' => true,
            'class' => 'form-control'
        ));
        
        // action
        $this->addElement('text', 'action', array(
            'label' => 'Action',
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
