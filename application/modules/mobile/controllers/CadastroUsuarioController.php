<?php

class Mobile_CadastroUsuarioController extends Zend_Controller_Action {

    const PLANO_BASICO = 8;
    const VALOR_PLANO_BASICO = 9;
    
    public function init() {
        $messages = $this->_helper->FlashMessenger->getMessages();
        $this->view->messages = $messages;
    }

    public function indexAction() {
        
        $formSiteCadastro = new Form_Site_Cadastro();
                
        /* validators */
        $formSiteCadastro->nome_completo->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo Nome'));
        $formSiteCadastro->cidade->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo Cidade'));
        $formSiteCadastro->data_nascimento->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo Data de Nascimento'));
        $formSiteCadastro->id_estado->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo Estado'));
        $formSiteCadastro->cpf_usuario->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo CPF'));
        $formSiteCadastro->senha_usuario->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo Senha'));
        $formSiteCadastro->confirma_senha->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo Confirme a Senha'));
        $formSiteCadastro->nome_completo->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo Nome'));
        $formSiteCadastro->email_usuario->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo E-mail'));
        $formSiteCadastro->email_usuario->addValidator('EmailAddress', false, array(
            'messages' => array(
                Zend_Validate_EmailAddress::INVALID => 'Digite um e-mail válido',
                Zend_Validate_EmailAddress::INVALID_FORMAT => 'Formato de e-mail inválido',
                Zend_Validate_EmailAddress::INVALID_HOSTNAME => 'Formato de e-mail inválido',
                Zend_Validate_EmailAddress::INVALID_LOCAL_PART => 'Formato de e-mail inválido'                
            )
        ));
        
        $this->view->formSiteCadastro = $formSiteCadastro;
        
        if ($this->_request->isPost()) {
            $dadosCadastroUsuario = $this->_request->getPost();
            if ($formSiteCadastro->isValid($dadosCadastroUsuario)) {
                
                $dadosCadastroUsuario = $formSiteCadastro->getValues();
                
                /* verificando se a politica esta preenchida */
                if ($dadosCadastroUsuario['politica']) {
                    /* verificando a confirmacao da senah */
                    if ($dadosCadastroUsuario['confirma_senha'] === $dadosCadastroUsuario['senha_usuario']) {
                        
                        /* criptografando a senha do usuario */
                        $dadosCadastroUsuario['senha_usuario'] = md5($dadosCadastroUsuario['senha_usuario']);
                        
                        /* formatando a data de nascimento */
                        $dadosCadastroUsuario['data_nascimento'] = Controller_Helper_Date::getDateDb($dadosCadastroUsuario['data_nascimento']);                        
                        
                        $modelUsuario = new Model_Usuario();
                        unset($dadosCadastroUsuario['confirma_senha']);
                        
                        try {
                            $id = $modelUsuario->insert($dadosCadastroUsuario);
                            
                            /**
                             * cadastrar o plano basico para o usuario
                             */
                            
                            $modelUsuarioPlano = new Model_UsuarioPlano();
                            
                            $planoBasico['id_usuario'] = $id;
                            $planoBasico['id_plano'] = self::PLANO_BASICO;
                            $planoBasico['data_aderido'] = Controller_Helper_Date::getDatetimeNowDb();
                            $planoBasico['data_encerramento'] = Controller_Helper_Date::getDataEncerramentoPlano($planoBasico['data_aderido'], self::PLANO_BASICO);
                            $planoBasico['ativo_plano'] = 1;
                            $planoBasico['id_plano_valor'] = self::VALOR_PLANO_BASICO;

                            $modelUsuarioPlano->insert($planoBasico);                            
                            
                            $hash_id = md5($id);
                            
                            /* grava o hash */
                            $modelUsuarioAtivar = new Model_UsuarioAtivar();
                            $dadosUsuarioAtivar = array(
                                'id_usuario' => $id,
                                'hash' => $hash_id                                
                            );
                            $modelUsuarioAtivar->insert($dadosUsuarioAtivar);
                            
                            /* envia o link para ativar a senha*/
                            $this->sendHashAtivaUsuario($hash_id, $dadosCadastroUsuario);
                            
                            $messages = array(
                                'type' => 'success',
                                'class' => 'alert alert-success',
                                'message' => 'Cadastro realizado com sucesso! Seja bem-vindo ao NewFinances!'
                            );
                            $this->_helper->flashMessenger->addMessage($messages);
                            $messages = array(
                                'type' => 'warning',
                                'class' => 'alert alert-warning',
                                'message' => 'Você precisa ativar sua conta. Basta acessar o e-mail cadastrado e clicar no link enviado.'
                            );
                            $this->_helper->flashMessenger->addMessage($messages);
                            $this->_redirect('cadastro-usuario');
                        } catch (Exception $ex) {                           
                            if ($ex->getCode() == 1062) {
                                $messages = array(
                                    array(
                                        'type' => 'warning',
                                        'class' => 'alert alert-warning',
                                        'message' => 'Ja existe um usuário cadastrado com este CPF ou E-mail!'
                                    )
                                );
                            } else {                                
                                $messages = array(
                                    array(
                                        'type' => 'error',
                                        'class' => 'alert alert-danger',
                                        'message' => 'Houve um erro ao realizar o cadastro. Favor entrar em contato e relatar o mesmo. - Message: ' . $ex->getMessage()
                                    )
                                );
                            }
                            $this->view->messages = $messages;
                        }
                        
                    } else {
                        $messages = array(
                            array(
                                'type' => 'warning',
                                'class' => 'bg-warning text-warning padding-10px margin-10px-0px',
                                'message' => 'Existem erros no preenchimento do formulário!'
                            ),
                            array(
                                'type' => 'error',
                                'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                                'message' => 'A confirmação da senha é diferente da senha digitada!'
                            )
                        );
                        $this->view->messages = $messages;
                    }
                } else {
                    $messages = array(
                        array(
                            'type' => 'warning',
                            'class' => 'bg-warning text-warning padding-10px margin-10px-0px',
                            'message' => 'É preciso ler e aceitar as Políticas de Privacidade e Termo de Uso.'
                        )
                    );
                    $this->view->messages = $messages;
                }
            } else {                
                $messages = array(
                    array(
                        'type' => 'warning',
                        'class' => 'bg-warning text-warning padding-10px margin-10px-0px',
                        'message' => 'Existem erros no preenchimento do formulário!'
                    )
                );
                $this->view->messages = $messages;
            }
        }
        
    }
    
    protected function sendHashAtivaUsuario($hash, $dados) {
        
        $link = "http://newfinances.com.br/usuarios/ativar/hash/" . $hash;
        
        // envia um e-mail de resposta para o visitante
        $html = new Zend_View();
        $html->setScriptPath(EMAILS_SITE . '/usuario/');

        // assign values
        $html->assign('nome_completo', $dados['nome_completo']);
        $html->assign('url_ativar', $link);

        // render view
        $bodyText = $html->render('usuario-ativar.phtml');

        // envia os emails para os usuarios
        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($bodyText);
        $mail->setFrom('noreply@newfinances.com.br', 'NewFinances - Controle Financeiro');
        $mail->addTo($dados['email_usuario']);                          
        $mail->setSubject("Ativar Cadastro");

        $mail->send(Zend_Registry::get('mail_transport'));
        
    }

}

