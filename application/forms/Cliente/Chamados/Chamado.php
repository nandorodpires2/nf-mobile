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
class Form_Cliente_Chamados_Chamado extends Zend_Form {

    public function init() {
        
        $formDefault = new Form_Default();
        
        $this->setAttrib('id', 'formChamadosChamado')
                ->setMethod('post');
        
        // id_usari (hidden)
        $this->addElement('hidden', 'id_usuario', array(
            'value' => $formDefault->id_usuario
        ));
        
        // id_tipo_chamado
        $this->addElement('select', 'id_tipo_chamado', array(
            'label' => 'Tipo de Chamado: ',
            'multioptions' => $formDefault->getTiposChamados(),
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
        
        // titulo
        $this->addElement('text', 'assunto', array(
            'label' => 'Assunto: ',
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
        
        // mensagem
        $this->addElement('textarea', 'mensagem', array(
            'label' => 'Mensagem: ',
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
        
        // anexo
        /*
        $this->addElement('file', 'anexo', array(
            'label' => 'Anexo: ',
            'required' => true
        ));
        */
        
        // submit
        $this->addElement('submit', 'submit', array(
            'label' => 'Abrir Chamado',
            "class" => "btn btn-submit navbar-right"
        ));
        
    }
    
}

