<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Receita
 *
 * @author Realter
 */
class Form_Cliente_Movimentacoes_Receita extends Zend_Form {

    public function init() {
        
        $formDefault = new Form_Default();
        $zendDate = new Zend_Date();
        
        $this->setAttrib('id', 'form_movimentacoes_receita')
            ->setMethod('post');
        
        // id_usuario (hidden)
        $this->addElement("hidden", "id_usuario", array(
            'value' => $formDefault->id_usuario
        ));
        
        // descricao
        $this->addElement("text", "descricao_movimentacao", array(
            'label' => 'Descrição: ',
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
        
        // valor
        $this->addElement("text", "valor_movimentacao", array(
            'label' => 'Valor: ',
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
        
        // data
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
        
        $contas['conta'] = 'Conta';
        $contas['cartao'] = 'Cartão de Crédito';
        
        // tipo pagamento
        $this->addElement("radio", "tipo_pgto", array(
            'label' => 'Pagamento: ',
            'multioptions' => $contas,
            'required' => true,
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div'))                
            )
        ));
        
        // cartao credito
        $this->addElement("select", "id_cartao", array(
            'label' => 'Cartão: ',
            'multioptions' => $formDefault->getCartoesUsuario(),
            'class' => 'form-control',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',
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
                'Errors',         
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div'))                
            )
        ));
                
        // categoria
        $this->addElement("select", "id_categoria", array(
            'label' => 'Categoria',
            'multioptions' => $formDefault->getCategorias(),
            'value' => 9,
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
        
        // observaocao
        $this->addElement($formDefault->_observacao);
        
        // option parcelar
        $this->addElement("checkbox", "opt_repetir", array(
            'label' => 'Repetir essa movimentação',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',
                'Label',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),                
                array('HtmlTag', array('tag' => 'div'))                
            )
        ));        
        
        // modo repeticao
        $this->addElement("radio", "modo_repeticao", array(
            'label' => 'Tipo: ',
            'multioptions' => array(
                'fixo' => 'Receita fixa',
                'parcelado' => 'Receita parcelada'                            
            )
        ));        
        
        // parcelas
        $this->addElement("select", "parcelas", array(
            'label' => 'Parcelas: ',
            'multioptions' => array(                
                2 => '2X',
                3 => '3X',
                4 => '4X',
                5 => '5X',
                6 => '6X',
                7 => '7X',
                8 => '8X',
                9 => '9X',
                10 => '10X',
                11 => '11X',
                12 => '12X',
            ),
            'class' => 'form-control',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div'))                
            )
        ));
        
        // repetir                
        $this->addElement("select", "repetir", array(
            'label' => 'Modo: ',
            'multioptions' => array(
                'day' => 'Diário',
                'week' => 'Semanal',
                'month' => 'Mensal',
                'year' => 'Anual'
            ),
            'class' => 'form-control',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div'))                
            )
        ));
        
        // submit
        $this->addElement("submit", "submit", array(
            'label' => 'Salvar',            
             'class' => 'btn btn-submit navbar-right'
        ));  
        
    }
    
}

