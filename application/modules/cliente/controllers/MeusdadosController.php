<?php

class Cliente_MeusdadosController extends Zend_Controller_Action
{

    public function init()
    {
        $messages = $this->_helper->FlashMessenger->getMessages();
        $this->view->messages = $messages;
    }

    public function indexAction()
    {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        // busca os dados do usuario
        $modelUsuario = new Model_Usuario();
        $usuario = $modelUsuario->getUsuario($id_usuario);
        $this->view->usuario = $usuario;
        
        $usuario->data_nascimento = View_Helper_Date::getDataView($usuario->data_nascimento);
        
        // form de usuario
        $formSiteCadastro = new Form_Site_Cadastro();
        
        $formSiteCadastro->removeElement('politica');
        $formSiteCadastro->cpf_usuario->setAttrib("readonly", true);
        $formSiteCadastro->email_usuario->setAttrib("readonly", true);
        $formSiteCadastro->senha_usuario->setRequired(false);
        $formSiteCadastro->confirma_senha->setRequired(false);
        
        $usuario->data_nascimento = View_Helper_Date::getDataView($usuario->data_nascimento);
        
        $formSiteCadastro->populate($usuario->toArray());
        $this->view->formSiteCadastro = $formSiteCadastro;
        
        // busca o plano do usuario
        $modelUsuarioPlano = new Model_UsuarioPlano();
        $planoUsuario = $modelUsuarioPlano->getPlanoAtual($id_usuario);
        $this->view->planoUsuario = $planoUsuario;
        
        if ($this->_request->isPost()) {
            $dadosDados = $this->_request->getPost();
            if ($formSiteCadastro->isValid($dadosDados)) {
                $dadosDados = $formSiteCadastro->getValues();
                
                if (!empty($dadosDados['senha_usuario'])) {                    
                    if ($dadosDados['senha_usuario'] != $dadosDados['confirma_senha']) {
                        $this->_helper->flashMessenger->addMessage(array(
                            'class' => 'alert alert-danger',
                            'message' => 'A confirmação da senha digitada é diferente da senha!'
                        ));

                        $this->_redirect("cliente/meusdados/");
                    } else {
                        $dadosDados['senha_usuario'] = md5($dadosDados['senha_usuario']);
                    }
                } else {
                    unset($dadosDados['senha_usuario']);
                }
                unset($dadosDados['confirma_senha']);
                
                $dadosDados['data_nascimento'] = Controller_Helper_Date::getDateDb($dadosDados['data_nascimento']);
                
                try {
                    $modelUsuario->update($dadosDados, "id_usuario = {$id_usuario}");
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'alert alert-success',
                        'message' => 'Dados atualizados com sucesso!'
                    ));

                    $this->_redirect("cliente/meusdados/");
                } catch (Exception $ex) {
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'alert alert-error',
                        'message' => 'Houve um erro ao atualizar os dados!'
                    ));

                    $this->_redirect("cliente/meusdados/");

                }
                
            }
        }
        
        
    }


}

