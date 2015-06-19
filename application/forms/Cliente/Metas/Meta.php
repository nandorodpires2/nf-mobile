<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Meta
 *
 * @author Realter
 */
class Form_Cliente_Metas_Meta extends Zend_Form {

    public function init() {
        
        $formDefault = new Form_Default();
        
        $this->setAttribs(array(
            'id' => 'formMetasMeta',
            'class' => 'form'
        ));
        $this->setMethod('post');        
        
        // id_categoria
        $this->addElement("select", "id_categoria", array(
            'label' => 'Categoria: ',
            'multioptions' => $formDefault->getCategoriasMeta(),
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
        
        // valor_meta
        $this->addElement("text", "valor_meta", array(
            'label' => 'Meta: ',
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
        
        // repetir mes
        $this->addElement("checkbox", "repetir", array(
            'label' => 'Repetir esse orçameto para os próximos meses',
            'required' => true,
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
            'label' => 'Cadastrar',
            'class' => 'btn btn-submit navbar-right'
        ));
        
        // id_usuario
        $this->addElement("hidden", "id_usuario", array('value' => $formDefault->id_usuario));
        
        // mes
        $this->addElement("hidden", "mes_meta", array('value' => date('m')));
        
        // ano
        $this->addElement("hidden", "ano_meta", array('value' => date('Y')));
        
    }
    
}

