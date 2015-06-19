<?php

class Mobile_AjaxController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function cadastraNewsletterAction() {
        
        $this->_helper->layout->disableLayout();
        
        $nome = $this->_getParam('nome');
        $email = $this->_getParam('email');
        
        $dadosInsert = array(
            'nome' => $nome,
            'email' => $email
        );
        
        $modelNewsletter = new Model_Newsletter();
        $this->view->nome = $nome;
        
        try {
            $modelNewsletter->insert($dadosInsert);            
            $this->view->message_type = 'success';
            $this->view->message_text = 'Cadastro realizado com sucesso';
        } catch (Exception $ex) {
            $this->view->message_type = 'error';
            $this->view->message_code = $ex->getCode();
            $this->view->message_text = $ex->getMessage();
        }
        
    }
    
}

