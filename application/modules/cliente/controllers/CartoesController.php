<?php

class Cliente_CartoesController extends Zend_Controller_Action {

    const TIPO_MOVIMENTACAO_DESPESA = 2;
    const TIPO_MOVIMENTACAO_PGTO_FATURA = 6;
    
    public function init() {
        $messages = $this->_helper->FlashMessenger->getMessages();
        $this->view->messages = $messages;
    }

    public function indexAction() {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $modelCartao = new Model_Cartao();
        // ativos
        $cartoes_ativos = $modelCartao->getCartoesUsuario($id_usuario, 1);
        $this->view->cartoes_ativos = $cartoes_ativos;
        // inativos
        $cartoes_inativos = $modelCartao->getCartoesUsuario($id_usuario, 0);
        $this->view->cartoes_inativos = $cartoes_inativos;
        
    }

    public function novoCartaoAction() {
        
        $formClienteCartao = new Form_Cliente_Cartoes_Cartao();
        $this->view->formClienteCartao = $formClienteCartao;
        
        $modelCartao = new Model_Cartao();
        
        if ($this->_request->isPost()) {
            $dadosCartao = $this->_request->getPost();
            if ($formClienteCartao->isValid($dadosCartao)) {
                $dadosCartao = $formClienteCartao->getValues();
                               
                $dadosCartao['ativo_cartao'] = 1;
                $dadosCartao['limite_cartao'] = View_Helper_Currency::setCurrencyDb($dadosCartao['limite_cartao'], 'positivo');
                
                try {
                    $modelCartao->insert($dadosCartao);
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'bg-success text-success padding-10px margin-10px-0px',
                        'message' => "Cartão Cadastado com sucesso!"
                    ));
                    
                } catch (Zend_Db_Exception $db_exception) {
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                        'message' => "Houve um erro ao cadastrar o cartão!"
                    ));
                    
                }
                $this->_redirect("cliente/cartoes");
            }
        }
    }

    public function editarCartaoAction() {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $id_cartao = $this->_getParam('id_cartao');
        
        $modelCartao = new Model_Cartao();
        $cartao = $modelCartao->getCartaoById($id_cartao, $id_usuario);
        
        if (!$cartao) {            
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-warning text-warning padding-10px margin-10px-0px',
                'message' => "Cartão não encontrado!"
            ));
            
            $this->_redirect("cliente/cartoes");
            
        }
        
        $cartao->limite_cartao = number_format($cartao->limite_cartao, 2, ',', '.');
        
        $formCartao = new Form_Cliente_Cartoes_Cartao();
        $formCartao->populate($cartao->toArray());
        $formCartao->submit->setLabel("Editar");
        $this->view->formCartao = $formCartao;
        
        if ($this->_request->isPost()) {
            $dadosUpdate = $this->_request->getPost();
            if ($formCartao->isValid($dadosUpdate)) {
                
                $dadosUpdate = $formCartao->getValues();
                
                $dadosUpdate['limite_cartao'] = View_Helper_Currency::setCurrencyDb($dadosUpdate['limite_cartao'], 'positivo');
                
                $where = "id_cartao = " . $id_cartao;                
                
                try {
                    
                    $modelCartao->update($dadosUpdate, $where);                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'bg-success text-success padding-10px margin-10px-0px',
                        'message' => "Dados atualizados com sucesso!"
                    ));
                    
                } catch (Exception $ex) {
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                        'message' => "Houve um erro ao atualizar os dados!"
                    ));
                    
                }
                
                $this->_redirect("cliente/cartoes");
                
            }
        }        
        
    }

    public function excluirCartaoAction() {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $id_cartao = $this->_getParam('id_cartao');
        
        $modelCartao = new Model_Cartao();
        $cartao = $modelCartao->getCartaoById($id_cartao, $id_usuario);
        $this->view->cartao = $cartao;
        
        if ($this->_request->isPost()) {
            
            $dados = $this->_request->getPost();
            
            if ($dados['btn-opt'] == 'Cancelar') {                
                $this->_helper->flashMessenger->addMessage(array(
                    'class' => 'bg-warning text-warning padding-10px margin-10px-0px',
                    'message' => 'Exclusão cancelada!'
                ));
                $this->_redirect("cliente/cartoes");
            } else {
                $dadosUpdate['ativo_cartao'] = 0;
                $whereUpdate = "id_cartao = " . $id_cartao;
                $modelCartao->update($dadosUpdate, $whereUpdate);
                
                $this->_helper->flashMessenger->addMessage(array(
                    'class' => 'bg-success text-success padding-10px margin-10px-0px',
                    'message' => 'Cartão excluído com sucesso!'
                ));
                $this->_redirect("cliente/cartoes");
                
            }
            
        }
        
    }
    
    public function reativarCartaoAction() {
     
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $id_cartao = $this->_getParam('id_cartao');
        
        $modelCartao = new Model_Cartao();
        $cartao = $modelCartao->getCartaoById($id_cartao, $id_usuario);
        
        if (!$cartao) {            
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-warning text-warning padding-10px margin-10px-0px',
                'message' => "Cartão não encontrado!"
            ));
            
            $this->_redirect("cliente/cartoes");            
        }
        
        $dadosUpdate['ativo_cartao'] = 1;
        $where = "id_cartao = " . $id_cartao;
        
        try {
            $modelCartao->update($dadosUpdate, $where);
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-success text-success padding-10px margin-10px-0px',
                'message' => 'Cartão reativado com sucesso!'
            ));
            $this->_redirect("cliente/cartoes");
        } catch (Exception $ex) {
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                'message' => 'Houve um erro ao reativar o cartão!'
            ));
            $this->_redirect("cliente/cartoes");
        }
        
    }
    
    /**
     * lançamentos fatura
     */
    public function lancamentosAction() {
        
        $id_cartao = $this->_getParam("cartao");        
        $vencimento_fatura = $this->_getParam("fatura");        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        $this->view->id_cartao = $id_cartao;
                        
        $modelVwLancamentosCartao = new Model_VwLancamentoCartao();
        
        // busca as faturas
        $faturas = $modelVwLancamentosCartao->getFaturas($id_usuario, $id_cartao);
        $this->view->faturas = $faturas;
        
        // busca os lancamentos da fatura
        
        $lancamentos = $modelVwLancamentosCartao->getLancamentosFatura($id_cartao, $vencimento_fatura, $id_usuario);
        $this->view->lancamentos = $lancamentos;        
        // total da fatura
        $total_fatura = $modelVwLancamentosCartao->getTotalFatura($id_cartao, $vencimento_fatura, $id_usuario);
        $this->view->total_fatura = $total_fatura->valor_fatura;
        
    }
    
    /**
     * pagar fatura
     */
    public function pagarFaturaAction() {
        
        $id_cartao = $this->_getParam("cartao");        
        $vencimento_fatura = $this->_getParam("fatura");        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $modelVwLancamentoCartao = new Model_VwLancamentoCartao();
        $fatura = $modelVwLancamentoCartao->getTotalFatura($id_cartao, $vencimento_fatura, $id_usuario);
        $this->view->fatura = $fatura;
        
        // saldo anterior
        $saldo_anterior = 0;
        $this->view->saldo_anterior = $saldo_anterior;
        
        // valor pago fatura
        $modelFaturaCartao = new Model_FaturaCartao();
        $valorPago = $modelFaturaCartao->getSaldoPago($id_cartao, $vencimento_fatura);
        $this->view->valorPago = $valorPago;
        
        // total fatura
        $total_fatura = ($saldo_anterior + $valorPago->saldo) + $fatura->valor_fatura; 
        $this->view->total_fatura = $total_fatura;
                
        // pagamento minimo
        $pagamento_minimo = ($fatura->valor_fatura / 100) * 15;
        $this->view->pagamento_minimo = $pagamento_minimo;
                
        // form de pagamento
        $formCartoesPagarFatura = new Form_Cliente_Cartoes_PagarFatura();
        $descricao_movimentacao = "Pagamento Fatura " . $fatura->descricao_cartao;
        $formCartoesPagarFatura->id_cartao->setValue($id_cartao);
        $formCartoesPagarFatura->vencimento_fatura->setValue($vencimento_fatura);
        $formCartoesPagarFatura->saldo_atual->setValue($fatura->valor_fatura);
        $formCartoesPagarFatura->descricao_movimentacao->setValue($descricao_movimentacao);
        $this->view->formPagarFatura = $formCartoesPagarFatura;
        
        if ($this->_request->isPost()) {
            $dadosPagamentoFatura = $this->_request->getPost();
            if ($formCartoesPagarFatura->isValid($dadosPagamentoFatura)) {
                $dadosPagamentoFatura = $formCartoesPagarFatura->getValues();
                
                $dadosFaturaCartao['id_cartao'] = $dadosPagamentoFatura['id_cartao'];
                $dadosFaturaCartao['saldo_atual'] = $dadosPagamentoFatura['saldo_atual'];
                $dadosFaturaCartao['vencimento_fatura'] = $dadosPagamentoFatura['vencimento_fatura'];
                //$dadosFaturaCartao['valor_pago'] = View_Helper_Currency::setCurrencyDb($dadosPagamentoFatura['valor_movimentacao'], 'positivo');
                
                unset($dadosPagamentoFatura['saldo_atual']);
                unset($dadosPagamentoFatura['vencimento_fatura']);
                
                $dadosPagamentoFatura['id_tipo_movimentacao'] = self::TIPO_MOVIMENTACAO_DESPESA;
                $dadosPagamentoFatura['id_cartao'] = null;
                $dadosPagamentoFatura['id_categoria'] = null;
                $dadosPagamentoFatura['realizado'] = Controller_Helper_Movimentacao::getStatusMovimentacao($dadosPagamentoFatura['data_movimentacao']);                 
                $dadosPagamentoFatura['data_movimentacao'] = Controller_Helper_Date::getDateDb($dadosPagamentoFatura['data_movimentacao']);
                
                $dadosPagamentoFatura['valor_movimentacao'] = View_Helper_Currency::setCurrencyDb($dadosPagamentoFatura['valor_movimentacao']);
                                                
                $modelMovimentacao = new Model_Movimentacao();
                
                try {
                    $id_movimentacao = $modelMovimentacao->insert($dadosPagamentoFatura);
                    
                    // insere na tabela de fatura
                    $modelFaturaCartao = new Model_FaturaCartao();
                    $dadosFaturaCartao['id_movimentacao'] = $id_movimentacao;
                    $modelFaturaCartao->insert($dadosFaturaCartao);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'alert alert-success',
                        'message' => 'Fatura paga com sucesso!'
                    ));
                    
                    $this->_redirect("cliente/index/index");
                } catch (Exception $ex) {
                    echo $ex->getMessage();
                }
            }
        }        
        
    }

}







