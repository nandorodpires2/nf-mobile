<?php

class Cliente_MovimentacoesController extends Zend_Controller_Action {

    const TIPO_MOVIMENTACAO_RECEITA = 1;
    const TIPO_MOVIMENTACAO_DESPESA = 2;
    const TIPO_MOVIMENTACAO_CARTAO = 3;
    const TIPO_MOVIMENTACAO_TRANSFERENCIA = 4;
    const TIPO_MOVIMENTACAO_PGTO_FATURA = 6;

    public function init() {          
        $messages = $this->_helper->FlashMessenger->getMessages();
        $this->view->messages = $messages;
    }

    public function indexAction() {        
        
        set_time_limit(60);
        
        // enviando os forms de filtros para a view
        $this->view->formMes = $this->_formMes;
        $this->view->formDias = $this->_formDias;
        $this->view->formBusca = $this->_formBusca;
        $this->view->formConta = $this->_formConta;
        
        // buscar as movimentacoes por dia do mes atual        
        $ano = (int)$this->_getParam("ano", date('Y'));
        $mes = (int)$this->_getParam("mes", date('m'));
        
        // caso tenha sido selecionado uuma conta
        $conta = $this->_getParam("conta", null);
        
        // popula o combo de mes
        $this->_formMes->populate(array("mes" => $mes, "ano" => $ano));
        
        // busca as datas onde possui lancamentos
        $datasMes = $this->_modelMovimentacao->getDatasMes($ano, $mes, $this->_session->id_usuario, $conta);        
        $this->view->datasMes = $datasMes;
        
    }

    /**
     * Nova Receita
     */
    public function novaReceitaAction() {
     
        $modelMovimentacao = New Model_Movimentacao();        
        $modelMovimentacaoRepeticao = New Model_MovimentacaoRepeticao();
        
        $formMovimentacoesReceitas = new Form_Cliente_Movimentacoes_Receita();
        $formMovimentacoesReceitas->getElement('opt_repetir')->getDecorator('label')->setOption('placement', 'APPEND');
        $this->view->formMovimentacoesReceitas = $formMovimentacoesReceitas;
        
        if ($this->_request->isPost()) {
            $dadosReceita = $this->_request->getPost();
            if ($formMovimentacoesReceitas->isValid($dadosReceita)) {
                $dadosReceita = $formMovimentacoesReceitas->getValues();
                
                if ($dadosReceita['tipo_pgto'] == 'conta') {
                    $dadosReceita['id_tipo_movimentacao'] = self::TIPO_MOVIMENTACAO_RECEITA;
                    $dadosReceita['realizado'] = Controller_Helper_Movimentacao::getStatusMovimentacao($dadosReceita['data_movimentacao']);
                    $dadosReceita['id_cartao'] = null;
                } else {
                    $dadosReceita['id_tipo_movimentacao'] = self::TIPO_MOVIMENTACAO_CARTAO;
                    $dadosReceita['realizado'] = 1;
                    $dadosReceita['id_conta'] = null;
                }
                
                unset($dadosReceita['tipo_pgto']);
                
                $dadosReceita['data_movimentacao'] = Controller_Helper_Date::getDateDb($dadosReceita['data_movimentacao']);
                $dadosReceita['data_inclusao'] = Controller_Helper_Date::getDatetimeNowDb();                
                $dadosReceita['valor_movimentacao'] = View_Helper_Currency::setCurrencyDb($dadosReceita['valor_movimentacao'], "positivo");
                                
                // verificando se a movimentacao repete                
                if ((int)$dadosReceita['opt_repetir']) {
                    $dadosRepeticao = array();                    
                    $dadosRepeticao['tipo'] = $dadosReceita['modo_repeticao'];
                    $dadosRepeticao['processado'] = 0;
                    switch ($dadosReceita['modo_repeticao']) {
                        case 'fixo':                            
                            $dadosRepeticao['modo'] = $dadosReceita['repetir'];
                            break;;
                        case 'parcelado':
                            $dadosRepeticao['modo'] = $dadosReceita['parcelas'];
                            $dadosReceita['valor_movimentacao'] /= (int)$dadosReceita['parcelas'];
                            $dadosReceita['descricao_movimentacao'] .= ' - 1/' . (int)$dadosReceita['parcelas'];
                            break;
                    }
                }                
                
                if ( empty($dadosReceita['observacao_movimentacao'])) {
                    $dadosReceita['observacao_movimentacao'] = null;
                }
                
                // retirando campos nao necessarios                                
                $repetir = (int)$dadosReceita['opt_repetir'];
                unset($dadosReceita['repetir']);                
                unset($dadosReceita['opt_repetir']);
                unset($dadosReceita['parcelas']);                
                unset($dadosReceita['modo_repeticao']);
                
                try {
                    $id_movimentacao = $modelMovimentacao->insert($dadosReceita);
                    
                    // recuperando o id da movimentacao
                    if ($repetir) {                        
                        $dadosRepeticao['id_movimentacao'] = $modelMovimentacao->lastInsertId();                    
                        $modelMovimentacaoRepeticao->insert($dadosRepeticao);                    
                        // recupera o id da movimentacao repeticao
                        $lastId = $modelMovimentacaoRepeticao->lastInsertId();

                        // atualiza o id pai 
                        $dadosUpdateMovimentacao['id_movimentacao_pai'] = $lastId;
                        $whereUpdateMovimentacao = "id_movimentacao = " . $modelMovimentacao->lastInsertId();
                        $modelMovimentacao->update($dadosUpdateMovimentacao, $whereUpdateMovimentacao);
                    }                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'alert alert-success',
                        'message' => 'Receita Cadastrada com sucesso!'
                    ));
                    
                    $this->_redirect("cliente/index/index");
                    
                } catch (Zend_Exception $error) {
                    echo $error->getMessage();
                }
                            
            }
        }
        
    }
    
    /**
     * Nova Despesa
     */
    public function novaDespesaAction() {
        
        $formMovimentacoesDespesa = new Form_Cliente_Movimentacoes_Despesa();
        $formMovimentacoesDespesa->getElement('opt_repetir')->getDecorator('label')->setOption('placement', 'APPEND');
        
        $this->view->formMovimentacoesDespesa = $formMovimentacoesDespesa;
        
        $modelMovimentacao = New Model_Movimentacao();
        $modelMovimentacaoRepeticao = New Model_MovimentacaoRepeticao();
        
        if ($this->_request->isPost()) {
            $dadosDespesa = $this->_request->getPost();
            if ($formMovimentacoesDespesa->isValid($dadosDespesa)) {
                $dadosDespesa = $formMovimentacoesDespesa->getValues();
                
                $dadosDespesa['valor_movimentacao'] = View_Helper_Currency::setCurrencyDb($dadosDespesa['valor_movimentacao']);
                
                if ($dadosDespesa['tipo_pgto'] == 'conta') {
                    $dadosDespesa['id_tipo_movimentacao'] = self::TIPO_MOVIMENTACAO_DESPESA;
                    $dadosDespesa['realizado'] = Controller_Helper_Movimentacao::getStatusMovimentacao($dadosDespesa['data_movimentacao']);
                    $dadosDespesa['id_cartao'] = null;
                } else {
                    $dadosDespesa['id_tipo_movimentacao'] = self::TIPO_MOVIMENTACAO_CARTAO;
                    $dadosDespesa['realizado'] = 1;
                    $dadosDespesa['id_conta'] = null;
                }
                
                // verificando se a movimentacao repete                
                if ((int)$dadosDespesa['opt_repetir']) {
                    $dadosRepeticao = array();                    
                    $dadosRepeticao['tipo'] = $dadosDespesa['modo_repeticao'];
                    $dadosRepeticao['processado'] = 0;
                    switch ($dadosDespesa['modo_repeticao']) {
                        case 'fixo':                            
                            $dadosRepeticao['modo'] = $dadosDespesa['repetir'];
                            break;;
                        case 'parcelado':
                            $dadosRepeticao['modo'] = $dadosDespesa['parcelas'];
                            $dadosDespesa['valor_movimentacao'] /= (int)$dadosDespesa['parcelas'];
                            $dadosDespesa['descricao_movimentacao'] .= ' - 1/' . (int)$dadosDespesa['parcelas'];
                            break;
                    }
                }
                
                $dadosDespesa['data_movimentacao'] = Controller_Helper_Date::getDateDb($dadosDespesa['data_movimentacao']);
                $dadosDespesa['data_inclusao'] = Controller_Helper_Date::getDatetimeNowDb();
                
                if ( empty($dadosDespesa['observacao_movimentacao'])) {
                    $dadosDespesa['observacao_movimentacao'] = null;
                }
                
                // retirando campos nao necessarios
                $repetir = (int)$dadosDespesa['opt_repetir'];
                unset($dadosDespesa['tipo_pgto']);           
                unset($dadosDespesa['opt_repetir']);
                unset($dadosDespesa['repetir']);                
                unset($dadosDespesa['parcelas']);                
                unset($dadosDespesa['modo_repeticao']);
                
                try {
                    $modelMovimentacao->insert($dadosDespesa);
                    
                    // recuperando o id da movimentacao
                    if ($repetir) {
                        unset($dadosDespesa['opt_repetir']);
                        $dadosRepeticao['id_movimentacao'] = $modelMovimentacao->lastInsertId();                    
                        $modelMovimentacaoRepeticao->insert($dadosRepeticao);                    
                        
                        // recupera o id da movimentacao repeticao
                        $lastId = $modelMovimentacaoRepeticao->lastInsertId();

                        // atualiza o id pai 
                        $dadosUpdateMovimentacao['id_movimentacao_pai'] = $lastId;
                        $whereUpdateMovimentacao = "id_movimentacao = " . $modelMovimentacao->lastInsertId();
                        $modelMovimentacao->update($dadosUpdateMovimentacao, $whereUpdateMovimentacao);
                        
                    }
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'alert alert-success',
                        'message' => 'Despesa Cadastrada com sucesso!'
                    ));
                    
                    $this->_redirect("cliente/index/index");
                } catch (Zend_Exception $error) {
                    echo $error->getMessage();
                }            
                
            }
        }
        
    }
    
    /**
     * transferencia
     */
    public function transferenciaAction() {
        
        $formMovimentacoesTransferencia = new Form_Cliente_Movimentacoes_Transferencia();
        $formMovimentacoesTransferencia->removeElement("id_categoria");
        $this->view->formMovimentacaoTransferencia = $formMovimentacoesTransferencia;
        
        $modelMovimentacao = new Model_Movimentacao();
        
        if ($this->_request->isPost()) {
            $dadosTransferencia = $this->_request->getPost();
            if ($formMovimentacoesTransferencia->isValid($dadosTransferencia)) {
                $dadosTransferencia = $formMovimentacoesTransferencia->getValues();
                
                $id_conta_origem = $dadosTransferencia['id_conta_origem'];
                unset($dadosTransferencia['id_conta_origem']);
                
                $dadosTransferencia['id_tipo_movimentacao'] = self::TIPO_MOVIMENTACAO_TRANSFERENCIA;
                
                $dadosTransferencia['data_movimentacao'] = Controller_Helper_Date::getDateDb($dadosTransferencia['data_movimentacao']);
                $dadosTransferencia['data_inclusao'] = Controller_Helper_Date::getDatetimeNowDb();
                $dadosTransferencia['realizado'] = Controller_Helper_Movimentacao::getStatusMovimentacao($dadosTransferencia['data_movimentacao']);                
                $dadosTransferencia['valor_movimentacao'] = View_Helper_Currency::setCurrencyDb($dadosTransferencia['valor_movimentacao']) * -1;
                $dadosTransferencia['id_categoria'] = 9;
                
                if ( empty($dadosTransferencia['observacao_movimentacao'])) {
                    $dadosTransferencia['observacao_movimentacao'] = null;
                }
                
                try {
                    $modelMovimentacao->insert($dadosTransferencia);                 
                
                    // lancando a movimentacao de saida
                    $dadosTransferencia['id_conta'] = $id_conta_origem;
                    $dadosTransferencia['valor_movimentacao'] *= -1; 
                
                    $modelMovimentacao->insert($dadosTransferencia);
                    
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'alert alert-success',
                        'message' => 'Transferência Cadastrada com sucesso!'
                    ));
                    
                    $this->_redirect("cliente/index/index");
                } catch (Zend_Exception $error) {
                    echo $error->getMessage();
                }
                
            }
        }
    }
    
    public function statusAction() {
        
        $modelMovimentacao = new Model_Movimentacao();
        
        // desabilitando o layout
        $this->_helper->layout->disableLayout(true);
        // desabilitando a view
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id = $this->_getParam("id_movimentacao");
        $status = $this->_getParam("status");
        
        $where = "id_movimentacao = " . $id;
        
        $statusUpdate['realizado'] = ((bool)$status) ? 0 : 1;
        
        try {
            $modelMovimentacao->update($statusUpdate, $where);
            
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'alert alert-success',
                'message' => 'Status atualizado com sucesso!'
            ));
            $this->_redirect("cliente/index/index");
            
        } catch (Zend_Exception $error) {
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                'message' => 'Houve um erro ao atualizar o status - ' . $error->getMessage()
            ));
            $this->_redirect("cliente/index/index");
        }
            
    }
    
    /**
     * editar movimentacao
     */
    public function editarMovimentacaoAction() {

        $idMovimentacao = $this->_getParam("id_movimentacao");
        // buscando os dados da movimentacao
        $modelMovimentacao = new Model_Movimentacao;
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $dadosMovimentacao = $modelMovimentacao->getDadosMovimentacao($idMovimentacao, $id_usuario);

        // data da movimentacao
        $data_movimentacao = $dadosMovimentacao->data_movimentacao;
                
        if ($dadosMovimentacao) {
            
            //Zend_Debug::dump($dadosMovimentacao);            

            $dadosMovimentacao = $dadosMovimentacao->toArray();
            
            // formatando alguns dados
            if ($dadosMovimentacao['id_tipo_movimentacao'] == self::TIPO_MOVIMENTACAO_TRANSFERENCIA || 
                    $dadosMovimentacao['id_tipo_movimentacao'] == self::TIPO_MOVIMENTACAO_RECEITA
                ) {
                $dadosMovimentacao['valor_movimentacao'] = number_format($dadosMovimentacao['valor_movimentacao'], 2, ',', '.');
            } else {
                $dadosMovimentacao['valor_movimentacao'] = number_format($dadosMovimentacao['valor_movimentacao'] * -1, 2, ',', '.');
            }
            
            $dadosMovimentacao['data_movimentacao'] = View_Helper_Date::getDataView($dadosMovimentacao['data_movimentacao']);
            
            if ($dadosMovimentacao['id_tipo_movimentacao'] == self::TIPO_MOVIMENTACAO_CARTAO) {
                $dadosMovimentacao['tipo_pgto'] = 'cartao';
            } elseif ($dadosMovimentacao['id_tipo_movimentacao'] == self::TIPO_MOVIMENTACAO_RECEITA || 
                    $dadosMovimentacao['id_tipo_movimentacao'] == self::TIPO_MOVIMENTACAO_DESPESA
                ) {
                $dadosMovimentacao['tipo_pgto'] = 'conta';
            }
            
            // verifica se a movimentacao se repete
            if ($dadosMovimentacao['id_movimentacao_pai'] != null) {                 
                $this->view->dadosRepeticao = false;                
            } else {
                $this->view->dadosRepeticao = false;
            }
            
            $formUpdate = $this->populateMovimentacao($dadosMovimentacao);            
            
            // atualizando a movimentacao            
            if ($this->_request->isPost()) {
                $dadosMovimentacaoUpdate = $this->_request->getPost();
                if ($formUpdate->isValid($dadosMovimentacaoUpdate)) {
                    $dadosMovimentacaoUpdate = $formUpdate->getValues();
                                        
                    if (isset ($dadosMovimentacaoUpdate['tipo_pgto'])) {                    
                        if ($dadosMovimentacaoUpdate['tipo_pgto'] == 'cartao') {                            
                            $dadosMovimentacaoUpdate['id_tipo_movimentacao'] = self::TIPO_MOVIMENTACAO_CARTAO;
                            $dadosMovimentacaoUpdate['id_conta'] = null;                            
                        } else {
                            $dadosMovimentacaoUpdate['id_cartao'] = null;                            
                        }
                        unset($dadosMovimentacaoUpdate['tipo_pgto']);
                    } 
                                        
                    if ($dadosMovimentacao['id_tipo_movimentacao'] == self::TIPO_MOVIMENTACAO_TRANSFERENCIA ||
                        $dadosMovimentacao['id_tipo_movimentacao'] == self::TIPO_MOVIMENTACAO_RECEITA    
                        ) {
                        $dadosMovimentacaoUpdate['valor_movimentacao'] = View_Helper_Currency::setCurrencyDb($dadosMovimentacaoUpdate['valor_movimentacao']) * -1;
                    } else {
                        $dadosMovimentacaoUpdate['valor_movimentacao'] = View_Helper_Currency::setCurrencyDb($dadosMovimentacaoUpdate['valor_movimentacao']);
                    }                    
                    
                    $dadosMovimentacaoUpdate['data_movimentacao'] = Controller_Helper_Date::getDateDb($dadosMovimentacaoUpdate['data_movimentacao']);
                    if ( isset ($dadosMovimentacaoUpdate['id_conta_origem'])) {
                        $id_conta_origem = $dadosMovimentacaoUpdate['id_conta_origem'];
                        unset($dadosMovimentacaoUpdate['id_conta_origem']);
                    }

                    // retirando os campos que nao serao usados                    
                    unset($dadosMovimentacaoUpdate['opt_repetir']);
                    unset($dadosMovimentacaoUpdate['modo_repeticao']);
                    unset($dadosMovimentacaoUpdate['parcelas']);
                    unset($dadosMovimentacaoUpdate['repetir']);                    
                    unset($dadosMovimentacaoUpdate['modo_edicao']);   
                    unset($dadosMovimentacaoUpdate['tipo_pgto']);
                    
                    $whereUpdate = "id_movimentacao = " . $idMovimentacao;

                    if ( empty($dadosMovimentacaoUpdate['observacao_movimentacao'])) {
                        $dadosMovimentacaoUpdate['observacao_movimentacao'] = null;
                    }
                    
                    try {
                        $modelMovimentacao->update($dadosMovimentacaoUpdate, $whereUpdate);

                        if ($dadosMovimentacao['id_tipo_movimentacao'] == self::TIPO_MOVIMENTACAO_TRANSFERENCIA) {                        
                            // atualizando a movimentacao de origem no caso de transferencia
                            $idMovimentacao++;
                            $whereOrigem = "id_movimentacao = " . $idMovimentacao;
                            $dadosMovimentacaoUpdate['id_conta'] = $id_conta_origem;
                            $dadosMovimentacaoUpdate['valor_movimentacao'] *= -1; 
                            $modelMovimentacao->update($dadosMovimentacaoUpdate, $whereOrigem);
                        }
                        
                        $this->_helper->flashMessenger->addMessage(array(
                            'class' => 'alert alert-success',
                            'message' => 'Lançamento alterado com sucesso!'
                        ));
                        $this->_redirect("cliente/index/index");
                        
                    } catch (Exception $erro) {
                        echo $erro->getMessage();
                    }
                                    
                }
            }
            
        } else {
            $modelMovimentacao->delete($where);
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'bg-danger text-danger padding-10px margin-10px-0px',
                'message' => 'Página não encontrada!'
            ));
            $this->_redirect("cliente/index/index");
        }
    }

    /**
     * excluir movimentacao
     */
    public function excluirMovimentacaoAction() {
        
        $idMovimentacao = $this->_getParam("id_movimentacao");
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $modelMovimentacao = new Model_Movimentacao();
        $modelVwMovimentacao = new Model_VwMovimentacao();
        
        // buscando os dados da movimentacao
        $dadosMovimentacao = $modelMovimentacao->getDadosMovimentacao($idMovimentacao, $id_usuario);
        $this->view->dadosMovimentacao = $dadosMovimentacao;
        
        // verificar se a movimentacao se repete                
        $id_movimentacao_pai = $modelMovimentacao->getIdMovimentacaoPai($idMovimentacao);
        $this->view->id_movimentacao_pai = $id_movimentacao_pai;
        
        if ($this->_request->isPost()) {
            $dadosExclusao = $this->_request->getPost();                        
            if ($dadosExclusao['btnResposta'] == 'Cancelar') {                
                $this->_helper->flashMessenger->addMessage(array(
                    'class' => 'alert alert-warning',
                    'message' => 'Exclusão cancelada!'
                ));
                $this->_redirect("cliente/index/index");                
            } else {                               
                if (!$id_movimentacao_pai) {
                    $dadosVwMovimentacao = $modelVwMovimentacao->fetchRow("id_movimentacao = {$idMovimentacao}");
                
                    if ($dadosVwMovimentacao->id_tipo_movimentacao == 4) {
                        $where = "id_movimentacao in ({$idMovimentacao}, {$dadosVwMovimentacao->id_movimentacao_origem})";
                    } else {               
                        $where = "id_movimentacao = " . $idMovimentacao;
                    }
                    
                    try {
                        $modelMovimentacao->delete($where);
                        $this->_helper->flashMessenger->addMessage(array(
                            'class' => 'alert alert-success',
                            'message' => 'Lançamento excluído com sucesso!'
                        ));
                        $this->_redirect("cliente/index/index");
                    } catch (Exception $error) {
                        echo $error->getMessage(); die('aki');
                    }
                } else {
                    
                    $opt_delete = $dadosExclusao['opt-delete'];
                    $where = "";
                    
                    switch ($opt_delete) {
                        case 0: // atual
                            $where .= "id_movimentacao = {$idMovimentacao}";
                            break;
                        case 1: // atual e anteriores
                            $where .= "id_movimentacao_pai = {$id_movimentacao_pai} and data_movimentacao <= '{$dadosMovimentacao['data_movimentacao']}'";
                            break;
                        case 2: // atual e posteriores
                            $where .= "id_movimentacao_pai = {$id_movimentacao_pai} and data_movimentacao >= '{$dadosMovimentacao['data_movimentacao']}'";
                            break;
                        case 3: // todos
                            $where .= "id_movimentacao_pai = {$id_movimentacao_pai}";
                            break;
                        default:
                            break;
                    }
                    
                    try {
                        $modelMovimentacao->delete($where);
                        $this->_helper->flashMessenger->addMessage(array(
                            'class' => 'alert alert-success',
                            'message' => 'Lançamento excluído com sucesso!'
                        ));
                        $this->_redirect("cliente/index/index");
                    } catch (Exception $error) {
                        echo $error->getMessage(); 
                    }                   
                    
                }                
            }                        
            
        }
        
    }
    
    protected function populateMovimentacao($dadosMovimentacao) {
        
        $formMovimentacoesReceitas = new Form_Cliente_Movimentacoes_Receita();
        $formMovimentacoesDespesa = new Form_Cliente_Movimentacoes_Despesa();
        $formMovimentacoesTransferencia = new Form_Cliente_Movimentacoes_Transferencia();
                
        switch ($dadosMovimentacao['id_tipo_movimentacao']) {
            case self::TIPO_MOVIMENTACAO_RECEITA:
                $formMovimentacoesReceitas->addElement('radio', 'realizado', array(
                    'label' => 'Marcar como realizado?',
                    'multioptions' => array(
                        1 => 'Sim',
                        0 => 'Não'
                    ),
                    'order' => 8
                ));
                /*
                if ($dadosMovimentacao['id_movimentacao_pai'] != null) {
                    $formMovimentacoesReceitas->addElement('radio', 'modo_edicao', array(
                        'label' => 'Essa movimentacao se repete, escolha um modo de edição:',
                        'multioptions' => array(
                            1 => 'Atualizar somente o registro atual',
                            2 => 'Atualizar este registro e todos os anteriores',
                            3 => 'Atualizar este registro e todos os futuros',
                            4 => 'Atualizar todos os registros'
                        ),
                        'value' => 1
                    ));
                    $formMovimentacoesDespesa->getElement('modo_edicao')->setOrder(7);                    
                }
                 * 
                 */
                $formMovimentacoesDespesa->removeElement('opt_repetir');
                $formMovimentacoesReceitas->populate($dadosMovimentacao);
                $this->view->formMovimentacoes = $formMovimentacoesReceitas;
                $this->view->class = "panel-success";
                return $formMovimentacoesReceitas;
                break;
            case self::TIPO_MOVIMENTACAO_DESPESA:
                $formMovimentacoesDespesa->addElement('radio', 'realizado', array(
                    'label' => 'Marcar como realizado?',
                    'multioptions' => array(
                        1 => 'Sim',
                        0 => 'Não'
                    ),
                    'order' => 8
                ));
                /*
                if ($dadosMovimentacao['id_movimentacao_pai'] != null) {                    
                    $formMovimentacoesDespesa->addElement('radio', 'modo_edicao', array(
                        'label' => 'Essa movimentacao se repete, escolha um modo de edição:',
                        'multioptions' => array(
                            1 => 'Atualizar somente o registro atual',
                            2 => 'Atualizar este registro e todos os anteriores',
                            3 => 'Atualizar este registro e todos os futuros',
                            4 => 'Atualizar todos os registros'
                        ),
                        'value' => 1
                    ));
                    $formMovimentacoesDespesa->getElement('modo_edicao')->setOrder(7);                    
                }
                 * 
                 */
                $formMovimentacoesDespesa->removeElement('opt_repetir');
                $formMovimentacoesDespesa->populate($dadosMovimentacao);
                $this->view->formMovimentacoes = $formMovimentacoesDespesa;
                $this->view->class = "panel-danger";
                return $formMovimentacoesDespesa;
                break;
            case self::TIPO_MOVIMENTACAO_TRANSFERENCIA:                
                $formMovimentacoesTransferencia->populate($dadosMovimentacao);
                $this->view->formMovimentacoes = $formMovimentacoesTransferencia;
                return $this->_formMovimentacoesTransferencia;
                break;
            case self::TIPO_MOVIMENTACAO_CARTAO:
                $formMovimentacoesDespesa->removeElement('opt_repetir');
                $formMovimentacoesDespesa->populate($dadosMovimentacao);
                $this->view->formMovimentacoes = $formMovimentacoesDespesa;
                $this->view->class = "panel-warning";
                return $formMovimentacoesDespesa;
                break;
            default :
                die('Nenhum tipo');
                break;
        }
        
    }

}

