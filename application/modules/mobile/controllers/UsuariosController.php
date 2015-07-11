<?php

class Mobile_UsuariosController extends Zend_Controller_Action {

    public function init() {
        $messages = $this->_helper->FlashMessenger->getMessages();
        $this->view->messages = $messages;        
    }

    public function indexAction() {
        
    }
    
    public function loginAction() {
        
        $modelUsuario = new Model_Usuario();
        
        $formUsuariosLogin = new Form_Site_Login();
        $this->view->formUsuariosLogin = $formUsuariosLogin;
        
        if ($this->getRequest()->isPost()) {
            
            $dadosUsuariosLogin = $this->getRequest()->getPost();            
            if ($formUsuariosLogin->isValid($dadosUsuariosLogin)) {
                $dadosUsuariosLogin = $formUsuariosLogin->getValues();                
                                
                $ZendAuth = Zend_Auth::getInstance();                
                $adapter = $modelUsuario->login($dadosUsuariosLogin);
                $usuarioRow = $modelUsuario->getDadosUsuario($dadosUsuariosLogin['email_usuario']); 
                
                $result = $ZendAuth->authenticate($adapter); 
                
                if ($result->isValid()) {                       
                    $ZendAuth->getStorage()->write($usuarioRow);   
                                        
                    // gravando o log
                    $dadosInsertLog['id_usuario'] = $usuarioRow->id_usuario;
                    $modelUsuarioLogin = new Model_UsuarioLogin();
                    $modelUsuarioLogin->insert($dadosInsertLog);
                    
                    $this->_redirect("cliente/index/");
                } else {         
                    $this->view->messages = array(
                        array(
                            'class' => "bg-danger text-danger padding-10px margin-10px-0px",
                            'message' => "Usuário e/ou senha iválidos!"
                        )
                    );
                }                                
                
            }
        }
        
    }

    public function recuperarSenhaAction() {
        
        $formSiteRecuperarSenha = new Form_Site_RecuperarSenha();
        $this->view->formSiteRecuperarSenha = $formSiteRecuperarSenha;
        
        if ($this->_request->isPost()) {
            $dadosSenha = $this->_request->getPost();
            if ($formSiteRecuperarSenha->isValid($dadosSenha)) {
                
                $modelUsuario = new Model_Usuario();
                $usuario = $modelUsuario->fetchRow("email_usuario = '{$dadosSenha['email']}'");
                
                if ($usuario) {
                    
                    $dadosUpdate = array(
                        'hash_senha' => md5(uniqid()),
                        'ativo_usuario' => 0
                    );
                    
                    try {
                        $modelUsuario->update($dadosUpdate, "id_usuario = {$usuario->id_usuario}");
                        
                        // envia o email                        
                        $this->_helper->flashMessenger->addMessage(
                            array(
                                'class' => "alert alert-success",
                                'message' => "Foi enviado um link para o e-mail informado para alterar sua senha"
                            )
                        );
                         
                        $this->_redirect("usuarios/recuperar-senha");
                    } catch (Exception $ex) {
                        $this->view->messages = array(
                            array(
                                'class' => "alert alert-danger",
                                'message' => "Houve um erro ao recuperar senha. Por favor tente mais tarde!"
                            )
                        );
                    }
                    
                } else {
                    $this->view->messages = array(
                        array(
                            'class' => "alert alert-danger",
                            'message' => "E-mail não cadastrado em nosso sistema!"
                        )
                    );
                }
                
            }
        }
        
    }

    /**
     * altera a senha
     */
    public function alterarSenhaAction() {
        
        $hash = $this->_getParam("hash");
        
        if ($hash) {            
            $modelUsuario = new Model_Usuario();
            $usuario = $modelUsuario->fetchRow("hash_senha = '{$hash}'");
            
            if ($usuario) {

                $formAlterarSenha = new Form_Site_AlterarSenha();
                $this->view->formAlterarSenha = $formAlterarSenha;

                if ($this->_request->isPost()) {
                    $dadosSenha = $this->_request->getPost();
                    if ($formAlterarSenha->isValid($dadosSenha)) {
                        $dadosSenha = $formAlterarSenha->getValues();
                        
                        if ($dadosSenha['nova_senha'] == $dadosSenha['confirma_nova_senha']) {
                            
                            $dadosUpdate = array(
                                'hash_senha' => null,
                                'ativo_usuario' => 1,
                                'senha_usuario' => md5($dadosSenha['nova_senha'])
                            );
                            
                            try {
                                $modelUsuario->update($dadosUpdate, "id_usuario = {$usuario->id_usuario}");
                                $this->_helper->flashMessenger->addMessage(
                                    array(
                                        'class' => "alert alert-success",
                                        'message' => "Senha alterada com sucesso!"
                                    )
                                );
                            } catch (Exception $ex) {
                                $this->_helper->flashMessenger->addMessage(
                                    array(
                                        'class' => "alert alert-danger",
                                        'message' => "Houve um erro ao alterar a senha!" . $ex->getMessage()
                                    )
                                );
                            }
                            $this->_redirect("usuarios/login");
                            
                        } else {
                            $this->view->messages = array(
                                array(
                                    'class' => "alert alert-danger",
                                    'message' => "A confirmação da senha e diferente da senha digitada!"
                                )
                            );
                        }
                        
                    }
                }
                
                
            } else {                        
                $this->_helper->flashMessenger->addMessage(
                    array(
                        'class' => "alert alert-danger",
                        'message' => "Houve um erro ao tentar alterar a senha!"
                    )
                );

                $this->_redirect("usuarios/alterar-senha");
            }
            
        }
        
    }

    public function ativarAction() {
        
        $hash = $this->_getParam('hash');
        
        /* busca os dados do hash */
        $modelUsuarioAtivar = new Model_UsuarioAtivar();        
        $dadosHashUsuario = $modelUsuarioAtivar->getDadosUsuarioHash($hash);
        
        $modelUsuario = new Model_Usuario();
        
        /* verificar se esta ativado */
        if (!$dadosHashUsuario->ativado) {
            $dadosAtivar = array(
                'ativado' => 1,
                'data_ativacao' => date('Y-m-d H:i:s')
            );
            $where = "id_usuario = " . $dadosHashUsuario->id_usuario;
            
            $whereUsuario = "id_usuario = " . $dadosHashUsuario->id_usuario;
            try {
                $modelUsuarioAtivar->update($dadosAtivar, $where);
                $modelUsuario->update(array('ativo_usuario' => 1), $whereUsuario);

                $messages = array(
                    'type' => 'success',
                    'class' => 'bg-success text-success padding-10px margin-10px-0px',
                    'message' => 'Conta ativada com sucesso.'
                );
                $this->_helper->flashMessenger->addMessage($messages);
            } catch (ErrorException $erro) {
                $messages = array(
                    'type' => 'danger',
                    'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                    'message' => 'Houve um erro ao ativar a conta. Favor entrar em contato relatando o problema.'
                );
                $this->_helper->flashMessenger->addMessage($messages);
            }                        
        } else {
            $messages = array(
                'type' => 'danger',
                'class' => 'bg-danger text-warning padding-10px margin-10px-0px',
                'message' => 'Esta conta já foi ativada!'
            );
            $this->_helper->flashMessenger->addMessage($messages);
            
        }
        
        $this->_redirect("usuarios/login");
        
    }

}



