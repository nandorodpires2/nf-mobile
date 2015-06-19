<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author Fernando Rodrigues
 */
class Form_Site_Login extends Zend_Form {

    public function init() {
        
        $this->setAttribs(
            array(
                'id' => 'form_usuarios_login',
                'role' => 'form'
            )
        );
        
        $this->setMethod('post');
        
        // email
        $this->addElement('text', 'email_usuario', array(
            'label' => 'E-mail: ',
            'size' => '40px',
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
        
        // senha
        $this->addElement('password', 'senha_usuario', array(
            'label' => 'Senha: ',
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
        
        //submit
        $this->addElement('submit', 'submit', array(
            'label' => 'Entrar',
            'class' => 'btn btn-submit navbar-right'
        ));
        
    }
    
}

