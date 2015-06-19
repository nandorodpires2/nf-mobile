<?php

class Cliente_IndexController extends Zend_Controller_Action {

    public function init() {
        
        $this->_redirect("/cliente/movimentacoes/nova-receita");
        
        $translate = Zend_Registry::get('Zend_Translate');
        $this->view->translate = $translate;
        
        $pluginMessage = new Plugin_Messages();
        $pluginMessage->isConta();
        $pluginMessage->isPendencias();
        
        $messages = $this->_helper->FlashMessenger->getMessages();        
        $this->view->messages = $messages;
        
    }

    public function indexAction() {
     
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $session = Zend_Registry::get('session');
        
        $modelMovimentacao = new Model_Movimentacao();
        
        /**
         * faturas dos cartoes de credito
         */          
        $modelVwLancamentoCartao = new Model_VwLancamentoCartao();
        $faturas = $modelVwLancamentoCartao->getFaturasAtual($id_usuario);
        $this->view->faturas = $faturas;               
        
        /**
         * busca as proximas receitas
         */
        $receitas = $modelMovimentacao->getProximasReceitas($id_usuario);
        $this->view->receitas = $receitas;
        
        /**
         * buscas as proximas despesas
         */
        $despesas = $modelMovimentacao->getProximasDespesas($id_usuario);
        $this->view->despesas = $despesas;
        
        /**
         * saldo das contas
         */
       $modelConta = new Model_Conta();
       $saldos = $modelConta->getSaldoContas($id_usuario);       
       $this->view->saldos = $saldos;     
       
       $saldo_total = 0;
        foreach ($saldos as $saldo) {
            $saldo_total += $saldo->saldo;
        }
        
        $this->view->saldo_total = $saldo_total;
        
    }
    
    public function denyAction() {
        
        $module = $this->_getParam("module");
        
        // caso o bloqueio de acesso seja do tipo 1 
        // e pq e o gestor do sistema e nao tem permissao para ver
        // caso 2 e pq o plano nao contempla esta visualizacao
        // envia uma msg especifica para o usurio        
        $this->view->typeDeny = $module;
        
        
    }

}

