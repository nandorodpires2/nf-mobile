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
class Form_Admin_Anotacoes_Anotacao extends Zend_Form {
    
    public function init() {
        
        $this->setAttrib('id', 'formAnotacao')
                ->setMethod('post');
        
        // modulo_anotacao
        $this->addElement('text', 'modulo_anotacao', array(
            'label' => 'Módulo:',
            'class' => 'form-control',
            'required' => true
        ));
        
        // titulo_anotacao
        $this->addElement('text', 'titulo_anotacao', array(
            'label' => 'Título:',
            'class' => 'form-control',
            'required' => true
        ));
        
        // descricao_anotacao
        $this->addElement('textarea', 'descricao_anotacao', array(
            'label' => 'Descrição:',
            'class' => 'form-control',
            'rows' => 10,
            'required' => true
        ));
        
        // prioridade_anotacao
        $this->addElement('select', 'prioridade_anotacao', array(
            'label' => 'Prioridade:',
            'class' => 'form-control',
            'required' => true,
            'multioptions' => array(
                'alta' => 'Alta',
                'media' => 'Média',
                'baixa' => 'Baixa'
            )
        ));
        
        // submit
        $this->addElement('submit', 'submit', array(
            'label' => 'Cadastrar',
            'class' => 'form-control',
            'class' => 'btn btn-success pull-right'
        ));
        
    }
    
}
