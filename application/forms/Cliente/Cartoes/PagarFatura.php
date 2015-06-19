<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PagarFatura
 *
 * @author Fernando
 */
class Form_Cliente_Cartoes_PagarFatura extends Zend_Form {
    
    public function init() {
        
        $formDefault = new Form_Default();
        $zendDate = new Zend_Date();
        
        // descricao_movimentacao
        $this->addElement("text", "descricao_movimentacao", array(
            'label' => 'Descrição: ',
            'required' => true,
            'class' => 'form-control',
            'readonly' => true,
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',         
                'Label',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div'))                
            )
        ));
                
        // valor_movimentacao
        $this->addElement('text', 'valor_movimentacao', array(
            'label' => 'Valor a ser pago:',
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
        
        // data_movimentacao
        $this->addElement("text", "data_movimentacao", array(
            'label' => 'Data: ',
            'value' => $zendDate->get(Zend_Date::DATE_MEDIUM),
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
                
        // conta
        $this->addElement("select", "id_conta", array(
            'label' => 'Conta: ',
            'multioptions' => $formDefault->getContasUsuario(),
            'class' => 'form-control',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Label',
                'Errors',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div'))                
            )
        ));
        
        // submit
        $this->addElement("submit", "submit", array(
            'label' => 'Pagar',
            'class' => 'btn btn-success'
        ));
        
        // id_usuario (hidden)
        $this->addElement("hidden", "id_usuario", array(
            'value' => $formDefault->id_usuario
        ));
                
        // id_cartao
        $this->addElement('hidden', 'id_cartao');        
        // id_cartao
        $this->addElement('hidden', 'saldo_atual');                
        // id_cartao
        $this->addElement('hidden', 'vencimento_fatura');                
        
    }
        
}
