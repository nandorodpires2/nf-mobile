<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cadastro
 *
 * @author Fernando Rodrigues
 */
class Form_Site_Cadastro extends Zend_Form {
    
    public function init() {
        
        $this->setAttribs(array(
            'id' => 'formSiteCadastro',
            'role' => 'form'
        ));
        
        // nome_completo
        $this->addElement('text', 'nome_completo', array(
            'label' => 'Nome Completo: ',
            'required' => true,
            'class' => 'form-control has-error',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',         
                'Label',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))                
            ),
            'order' => 1
        ));
        
        // cidade
        $this->addElement('text', 'cidade', array(
            'label' => 'Cidade: ',
            'required' => true,
            'class' => 'form-control',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',         
                'Label',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))                
            ),
            'order' => 5
        ));
        
        // estado
        $this->addElement('select', 'id_estado', array(
            'label' => 'Estado: ',
            'required' => true,
            'class' => 'form-control',
            'multioptions' => $this->getEstados(),
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',         
                'Label',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))                         
            ),
            'order' => 6
        ));
        
        // cpf_usuario
        $this->addElement('text', 'cpf_usuario', array(
            'label' => 'CPF: ',
            'required' => true,
            'class' => 'form-control',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',         
                'Label',                
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))                         
            ),
            'order' => 2
        ));
        
        // data_nascimento
        $this->addElement('text', 'data_nascimento', array(
            'label' => 'Data Nascimento: ',
            'required' => true,
            'class' => 'form-control',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',         
                'Label',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))                         
            ),
            'order' => 4
        ));
        
        // id_estado
        
        // email_usuario
        $this->addElement('text', 'email_usuario', array(
            'label' => 'E-mail: ',
            'required' => true,
            'class' => 'form-control',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',         
                'Label',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))                    
            ),
            'order' => 3
        ));
        
        // senha_usuario
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
                array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))                          
            ),
            'order' => 7
        ));
        
        // senha_usuario
        $this->addElement('password', 'confirma_senha', array(
            'label' => 'Confirme a Senha: ',
            'required' => true,
            'class' => 'form-control',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',         
                'Label',
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'))                    
            ),
            'order' => 8
        ));
        
        // politica
        $this->addElement('checkbox', 'politica', array(            
            'required' => true,
            'value' => '1',
            'class' => 'checkbox',
            'decorators' => array(
                'ViewHelper',
                'Description',
                'Errors',                
                array(
                    'Label', array(
                        'tag' => 'span',
                        'class' => 'control-label margin-top-10px'
                    )
                ),
                array(
                    'HtmlTag', array(
                        'tag' => 'span',
                        'class' => 'checkbox'
                    )
                ),
                array('Errors', array('class' => 'error padding-10px bg-danger text-danger'))
            ),
            'label' => "Li e concordo com a Política de Privacidade e o Termo de Uso",
            'order' => 9
        ));
        
        // submit
        $this->addElement('submit', 'submit', array(
            'label' => 'Cadastrar',
            'class' => 'btn btn-submit navbar-right',
            'order' => 10
        ));
        
    }
    
    protected function getEstados() {
        
        $multioptions = array('' => 'Selecione o estado');
        
        $modelEstado = new Model_Estado();
        $estados = $modelEstado->fetchAll(null, 'descricao_estado asc');
        
        foreach ($estados as $estado) {
            $multioptions[$estado->id_estado] = $estado->sigla_estado . '  -  ' . $estado->descricao_estado;
        }
        
        return $multioptions;
        
    }
    
}
