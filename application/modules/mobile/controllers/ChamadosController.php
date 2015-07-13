<?php

class Admin_ChamadosController extends Zend_Controller_Action {

    public function init() {
        $messages = $this->_helper->FlashMessenger->getMessages();
        $this->view->messages = $messages;
    }

    public function indexAction() {
        
        $modelChamados = new Model_Chamado();
        $chamados = $modelChamados->getChamados();
        $this->view->chamados = $chamados;
        
    }
    
    public function conversasAction() {
        
        $id_chamado = $this->_getParam('id_chamado');        
        
        $formResponder = new Form_Cliente_Chamados_Responder();
        $formResponder->getElement('opt_fechar')->getDecorator('label')->setOption('placement', 'APPEND');
        $formResponder->id_chamado->setValue($id_chamado);
        $this->view->formResponder = $formResponder;
        
        $modelChamado = new Model_Chamado();
        $modelChamadoResposta = new Model_ChamadoResposta();
        
        $chamado = $modelChamado->getDadosChamado($id_chamado);
        $respostas = $modelChamadoResposta->respostasChamado($id_chamado);
        
        $this->view->chamado = $chamado;
        $this->view->respostas = $respostas;
        
    }
    
    public function responderAction() {
        
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id_chamado = $this->_getParam('id_chamado');
        
        $modelChamadoResposta = new Model_ChamadoResposta();
        
        if ($this->_request->isPost()) {            
            $formResponder = new Form_Cliente_Chamados_Responder();
            $dadosResposta = $this->_request->getPost();
            if ($formResponder->isValid($dadosResposta)) {
                $dadosResposta = $formResponder->getValues();
                $opt_fechar = $dadosResposta['opt_fechar'];
                unset($dadosResposta['opt_fechar']);
                
                try {
                    $modelChamadoResposta->insert($dadosResposta);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'bg-success text-success padding-10px margin-10px-0px',
                        'message' => 'Resposta cadastrada com sucesso!'
                    ));
                    
                    if ($opt_fechar) {                        
                        $this->finalizarAction();
                    }
                    
                } catch (Exception $ex) {
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                        'message' => 'Houve um erro ao cadastrar a resposta!' . $ex->getMessage()
                    ));
                }
                
            } else {
                $this->_helper->flashMessenger->addMessage(array(
                    'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                    'message' => 'Existem erros no formulário de resposta!'
                ));
            }            
        } 
        $this->_redirect("admin/chamados");
        
    }
    
    public function finalizarAction() {
        
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $id_chamado = $this->_getParam('id_chamado');
        
        $modelChamado = new Model_Chamado();
        $chamado = $modelChamado->getDadosChamado($id_chamado);
        
        if ($chamado) {
            
            $dadosUpdate['status'] = "Fechado";
            $dadosUpdate['data_fechamento'] = Controller_Helper_Date::getDateTimeNowDb();
            $dadosUpdate['id_usuario_fechamento'] = $id_usuario;
            
            $whereUpdate = "id_chamado = " . $id_chamado;
            
            try {
                $modelChamado->update($dadosUpdate, $whereUpdate);
                
                $this->_helper->flashMessenger->addMessage(array(
                    'class' => 'bg-success text-success padding-10px margin-10px-0px',
                    'message' => 'Chamado finalizado com sucesso!'
                ));
                
            } catch (Exception $ex) {
                
                $this->_helper->flashMessenger->addMessage(array(
                    'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                    'message' => 'Houve um erro ao finalizar o chamado!'
                ));
                
            }            
            
        } else {
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                'message' => 'Nenhum chamado encontrado!'
            ));

        }
        $this->_redirect("admin/chamados");
        
    }

}

