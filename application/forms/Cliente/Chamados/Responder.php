<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Responder
 *
 * @author Fernando Rodrigues
 */
class Form_Cliente_Chamados_Responder extends Zend_Form {

    public function init() {
        
        $formDefault = new Form_Default();
        
        $this->setAttrib('id', 'formChamadosResponder')
                ->setMethod('post');
        
        // id_chamado (hidden)
        $this->addElement('hidden', 'id_chamado');
        
        // id_usuario (hidden)
        $this->addElement('hidden', 'id_usuario', array(
            'value' => $formDefault->id_usuario
        ));
        
        // resposta
        $this->addElement('textarea', 'resposta', array(
            'label' => 'Mensagem:',
            'rows' => 10,
            'cols' => 40,
            'required' => true,
            'class' => 'form-control',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',         
                'Label',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div'))                
            )
        ));
        
        // option parcelar
        $this->addElement("checkbox", "opt_fechar", array(
            'label' => 'Finalizar este chamado?',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',
                'Label',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),                
                array('HtmlTag', array('tag' => 'div'))                
            )
        ));   
        
        $this->addElement('submit', 'submit', array(
            'label' => 'Responder',
            'class' => 'btn btn-submit navbar-right'
        ));
        
        
    }
    
}

