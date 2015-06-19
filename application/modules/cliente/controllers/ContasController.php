<?php

class Cliente_ContasController extends Zend_Controller_Action
{

    public function init() {
        $messages = $this->_helper->FlashMessenger->getMessages();
        $this->view->messages = $messages;
    }

    public function indexAction()
    {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        $modelConta = new Model_Conta();
        $contas_ativas = $modelConta->getContasUsuario($id_usuario, 1);
        $this->view->contas_ativas = $contas_ativas;
        
        $contas_inativas = $modelConta->getContasUsuario($id_usuario, 0);
        $this->view->contas_inativas = $contas_inativas;
     
        //Zend_Debug::dump($contas_inativas);
        
    }

    public function novaContaAction()
    {
        
        $formClienteConta = new Form_Cliente_Contas_Conta();
        $this->view->formClienteConta = $formClienteConta;
        
        $modelConta = new Model_Conta();
        
        if ($this->_request->isPost()) {            
            $dadosConta = $this->_request->getPost();
            if ($formClienteConta->isValid($dadosConta)) {
                $dadosConta = $formClienteConta->getValues();
                                
                if ($dadosConta['id_banco'] == '') {
                    $dadosConta['id_banco'] = null;
                }
                
                $dadosConta['ativo_conta'] = 1;
                $dadosConta['data_inclusao'] = Controller_Helper_Date::getDatetimeNowDb();
                $dadosConta['saldo_inicial'] = View_Helper_Currency::setCurrencyDb($dadosConta['saldo_inicial'], 'positivo');
                
                try {                
                    $modelConta->insert($dadosConta);   
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'bg-success text-success padding-10px margin-10px-0px',
                        'message' => "Conta Cadastada com sucesso!"
                    ));
                    
                    $this->_redirect("cliente/contas");
                    
                } catch (Zend_Exception $erro) {
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                        'message' => "Houve um erro ao cadastrar sua conta!"
                    ));
                    
                    $this->_redirect("cliente/contas");
                }   
                
            }
        }
        
    }

    public function editarContaAction() {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $id_conta = $this->_getParam('id_conta');        
        
        $modelConta = new Model_Conta();
        $conta = $modelConta->getConta($id_conta, $id_usuario)->toArray();
         $conta['saldo_inicial'] = number_format($conta['saldo_inicial'], 2, ',', '.');
        $formConta = new Form_Cliente_Contas_Conta();
        $formConta->populate($conta);
        $formConta->submit->setLabel("Editar");
        $this->view->formConta = $formConta;
        
        if ($this->_request->isPost()) {
            $dadosUpdate = $this->_request->getPost();
            if ($formConta->isValid($dadosUpdate)) {
                $dadosUpdate = $formConta->getValues();
                
                $dadosUpdate['saldo_inicial'] = View_Helper_Currency::setCurrencyDb($dadosUpdate['saldo_inicial'], 'positivo');
                
                $where = "id_conta = " . $id_conta;
                
                try {
                    $modelConta->update($dadosUpdate, $where);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'bg-success text-success padding-10px margin-10px-0px',
                        'message' => "Dados alterados com sucesso!"
                    ));
                    
                    $this->_redirect("cliente/contas");
                } catch (Exception $ex) {
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                        'message' => "Houve um erro ao editar sua conta!"
                    ));
                    
                    $this->_redirect("cliente/contas");
                }
                
                
            }
        }
        
    }

    public function excluirContaAction() {
     
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $id_conta = $this->_getParam('id_conta');
        
        $modelConta = new Model_Conta();
        $conta = $modelConta->getConta($id_conta, $id_usuario);
        if (!$conta) {
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                'message' => 'Nenhuma conta encontrada!'
            ));
            $this->_redirect("cliente/contas");
        }
        $this->view->conta = $conta;
        
        if ($this->_request->isPost()) {
            $dados = $this->_request->getPost();
            
            if ($dados['btn-opt'] == 'Cancelar') {                
                $this->_helper->flashMessenger->addMessage(array(
                    'class' => 'bg-warning text-warning padding-10px margin-10px-0px',
                    'message' => 'Exclusão cancelada!'
                ));
                $this->_redirect("cliente/contas");
            } else {
                $dadosUpdate['ativo_conta'] = 0;
                $whereUpdate = "id_conta = " . $id_conta;
                $modelConta->update($dadosUpdate, $whereUpdate);
                
                $this->_helper->flashMessenger->addMessage(array(
                    'class' => 'bg-success text-success padding-10px margin-10px-0px',
                    'message' => 'Conta excluída com sucesso!'
                ));
                $this->_redirect("cliente/contas");
                
            }
            
        }
        
    }
    
    public function reativarContaAction() {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $id_conta = $this->_getParam('id_conta');
        $modelConta = new Model_Conta();
        $conta = $modelConta->getConta($id_conta, $id_usuario);
        
        if (!$conta) {
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-warning text-warning padding-10px margin-10px-0px',
                'message' => 'Conta não encontrada!'
            ));
            $this->_redirect("cliente/contas");    
        }
        
        $dadosUpdate['ativo_conta'] = 1;
        $where = "id_conta = " . $id_conta;

        try {
            $modelConta->update($dadosUpdate, $where);
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-success text-success padding-10px margin-10px-0px',
                'message' => 'Conta reativada com sucesso!'
            ));
            $this->_redirect("cliente/contas");
        } catch (Exception $ex) {
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                'message' => 'Nenhuma conta encontrada!'
            ));
            $this->_redirect("cliente/contas");
        }
        
    }

}







