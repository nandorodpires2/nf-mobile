<?php

class Mobile_ContatoController extends Zend_Controller_Action {

    public function init() {
        $messages = $this->_helper->FlashMessenger->getMessages();
        $this->view->messages = $messages;
    }

    public function indexAction() {
        
        $formSiteContato = new Form_Site_Contato();            
        $formSiteContato->nome->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo Nome'));
        $formSiteContato->email->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo E-mail'));
        $formSiteContato->email->addValidator('EmailAddress', false, array(
            'messages' => array(
                Zend_Validate_EmailAddress::INVALID => 'Digite um e-mail válido',
                Zend_Validate_EmailAddress::INVALID_FORMAT => 'Formato de e-mail inválido',
                Zend_Validate_EmailAddress::INVALID_HOSTNAME => 'Formato de e-mail inválido',
                Zend_Validate_EmailAddress::INVALID_LOCAL_PART => 'Formato de e-mail inválido'                
            )
        ));
        $formSiteContato->assunto->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo Assunto'));
        $formSiteContato->texto->addValidator('NotEmpty', true, array('messages'=>'Preencha o campo Mensagem'));
        
        $this->view->formSiteContato = $formSiteContato;
        
        if ($this->_request->isPost()) {
            $dadosContato = $this->_request->getPost();
            if ($formSiteContato->isValid($dadosContato)) {
                $dadosContato = $formSiteContato->getValues();
                
                // grava o contato no banco                
                $modelContato = new Model_Contato();
                if ($modelContato->insert($dadosContato)) {
                    // data da mensagem
                    $data = date("d/m/Y H:i:s");

                    // envia um e-mail de resposta para o visitante
                    $html = new Zend_View();
                    $html->setScriptPath(EMAILS_MOBILE . '/contato/');

                    // assign values
                    $html->assign('nome', $dadosContato['nome']);
                    $html->assign('data', $data);
                    $html->assign('assunto', $dadosContato['assunto']);
                    $html->assign('mensagem', $dadosContato['texto']);

                    // render view
                    $bodyText = $html->render('contato.phtml');

                    // envia os emails para os usuarios
                    $mail = new Zend_Mail('utf-8');
                    $mail->setBodyHtml($bodyText);
                    $mail->setFrom('noreply@newfinances.com.br', 'NewFinances - Controle Financeiro');
                    $mail->addTo($dadosContato['email']);                          
                    $mail->setSubject("Contato recebido");

                    $mail->send(Zend_Registry::get('mail_transport'));

                    // envia um e-mail para o gestor                
                    $body = "
                        <p><b>Nova Mensagem de Contato enviado:</b></p>
                        <p><b>Data: </b>{$data}</p>
                        <p><b>Nome: </b>{$dadosContato['nome']}</p>
                        <p><b>E-mail: </b>{$dadosContato['email']}</p>
                        <p><b>Assnto: </b>{$dadosContato['assunto']}</p>
                        <p><b>Mensagem: </b></p>
                        <p>{$dadosContato['texto']}</p>
                    ";

                    // envia os emails para os usuarios
                    $mail = new Zend_Mail('utf-8');
                    $mail->setBodyHtml($body);
                    $mail->setFrom('noreply@newfinances.com.br', 'NewFinances - Controle Financeiro');
                    $mail->addTo("nandorodpires@gmail.com");                          
                    $mail->setSubject("Contato enviado");

                    if ($mail->send(Zend_Registry::get('mail_transport'))) {
                        $this->_helper->flashMessenger->addMessage(array(
                            'class' => 'bg-success text-success padding-10px margin-10px-0px',
                            'message' => 'Contato enviado com sucesso. Em breve retornaremos.'
                        ));
                        $this->_redirect("contato");
                        /*
                        $this->view->message_type = "bg-success text-success padding-10px margin-10px-0px";
                        $this->view->message_text = "Contato enviado com sucesso. Em breve retornaremos.";
                         * 
                         */
                    } else {
                        $messages = array(
                            array(
                                'type' => 'warning',
                                'class' => 'bg-warning text-warning padding-10px margin-10px-0px',
                                'message' => 'Contato registrado, porém houve um problema ao enviar o e-mail.'
                            )
                        );
                        $this->view->messages = $messages;
                    }
                    
                } else {
                    $messages = array(
                        array(
                            'type' => 'warning',
                            'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                            'message' => 'Houve um problema ao enviar seu contato. Por favor tente mais tarde.'
                        )
                    );
                    $this->view->messages = $messages;
                }                
                
            } else {
                $messages = array(
                    array(
                        'type' => 'warning',
                        'class' => 'bg-warning text-warning padding-10px margin-10px-0px',
                        'message' => 'Existem erros no preenchimento do formulário'
                    )
                );
                $this->view->messages = $messages;
            }
        }
        
        
    }


}

