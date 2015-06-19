<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cartao
 *
 * @author Realter
 */
class Form_Cliente_Cartoes_Cartao extends Zend_Form {

    public function init() {
        
        $formDefault = new Form_Default();
        
        $this->setAttrib('id', 'formConfiguracoesCartao')
                ->setMethod('post');
        
        // id_usuario (hidden)
        $this->addElement("hidden", "id_usuario", array(
            'value' => $formDefault->id_usuario
        ));
        
        // bandeira_cartao
        $this->addElement("select", "bandeira_cartao", array(
            "label" => "Bandeira: ",
            "required" => true,
            "multioptions" => $this->getBandeiras(),
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
        
        // descricao_cartao
        $this->addElement("text", "descricao_cartao", array(
            "label" => "Descrição: ", 
            "required" => true,
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
        
        // limite_cartao
        $this->addElement("text", "limite_cartao", array(
            "label" => "Limite: ", 
            "required" => true,
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
        
        // fechamento_cartao
        $this->addElement("text", "fechamento_cartao", array(
            "label" => "Dia Fechamento: ", 
            "required" => true,
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
        
        // vencimento_cartao        
        $this->addElement("text", "vencimento_cartao", array(
            "label" => "Dia Vencimento: ", 
            "required" => true,
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
        
        // submit
        $this->addElement("submit", "submit", array(
            "label" => "Cadastrar",
            "class" => "btn btn-submit navbar-right"
        ));
        
    }
    
    protected function getBandeiras() {
        
        $multOptions = array(            
            '' => 'Selecione...',
            'visa' => 'Visa',
            'mastercard' => 'MasterCard',
            'american_express' => 'American Express'
        );
        
        return $multOptions;
    }
    
}

