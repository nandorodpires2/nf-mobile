<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Conta
 *
 * @author Realter
 */
class Form_Cliente_Contas_Conta extends Zend_Form {

    public function init() {
        
        $formDefault = new Form_Default();
        
        $this->setAttrib("id", "formNovaConta")
                ->setMethod("post");
        
        // id_usuario (hidden)
        $this->addElement("hidden", "id_usuario", array(
            'value' => $formDefault->id_usuario
        ));
        
        // descricao
        $this->addElement("text", "descricao_conta", array(
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
        
        // tipo
        $this->addElement("select", "id_tipo_conta", array(
            "label" => "Tipo de Conta: ",
            "multioptions" => $formDefault->getTipoContas(),
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
        
        // saldo inicial
        $this->addElement("text", "saldo_inicial", array(
            "label" => "Saldo Inicial: ",
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
        
        // banco
        $this->addElement("select", "id_banco", array(
            "label" => "Banco: ",
            "multioptions" => $formDefault->getBancos(),
            'class' => 'form-control',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',         
                'Label',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div')),
                array('Label', array('tag' => 'div'))
            )
        ));
        
        // submit        
        $this->addElement("submit", "submit", array(
            "label" => "Cadastrar",
            "class" => "btn btn-submit navbar-right"
        ));
    }
    
}

