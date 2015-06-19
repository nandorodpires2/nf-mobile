<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AlterarSenha
 *
 * @author Fernando Rodrigues
 */
class Form_Site_AlterarSenha extends Zend_Form {
    
    public function init() {
        
        // nova_senha
        $this->addElement('password', 'nova_senha', array(
            'label' => 'Digite a nova senha',
            'required' => true,
            'class' => 'form-control'
        ));
        
        $this->addElement('password', 'confirma_nova_senha', array(
            'label' => 'Confirme a nova senha',
            'required' => true,
            'class' => 'form-control'
        ));
        
        //submit
        $this->addElement('submit', 'submit', array(
            'label' => 'Enviar',
            'class' => 'btn btn-submit navbar-right'
        ));
        
    }
    
}
