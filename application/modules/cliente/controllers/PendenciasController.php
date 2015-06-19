<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PendenciasController
 *
 * @author Fernando Rodrigues
 */
class Cliente_PendenciasController extends Zend_Controller_Action {
    
    public function init() {
        $messages = $this->_helper->FlashMessenger->getMessages();
        $this->view->messages = $messages;
    }
    
    public function indexAction() {
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $modelMovimentacao = new Model_Movimentacao();
        $pendencias = $modelMovimentacao->getPendencias($id_usuario);
        $this->view->pendencias = $pendencias;
        
        if ($this->_request->isPost()) {
            
            $lancamentos = $this->_request->getPost();
            
            foreach ($lancamentos['lancamentos_excluir'] as $lancamento) {                
                $where = "id_movimentacao = " . $lancamento;
                $modelMovimentacao->delete($where);
            }            
            
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-success text-success padding-10px margin-10px-0px',
                'message' => 'Movimentações excluídas com sucesso!'
            ));

            $this->_redirect("cliente/index/index");
            
        }        
        
    }
    
}
