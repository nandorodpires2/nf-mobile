<?php

class Cliente_AjaxController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {
        
    }
    
    public function movimentacoesAction() {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        $data_movimentacao = Zend_Auth::getInstance()->getIdentity()->data_usuario;
        $data_movimentacao = View_Helper_Date::getDataView($data_movimentacao);
        
        /**
         * lancamentos de hoje
         */
        $conta = $this->_getParam("id_conta", null);
        $data = $this->_getParam("data", $data_movimentacao);         
        $data_pesquisa = Controller_Helper_Date::getDateDb($data);
                        
        $this->view->data_movimentacao = Controller_Helper_Date::getDateViewComplete($data_pesquisa);
        
        $modelVwMovimentacao = new Model_VwMovimentacao();
        $movimentacoes = $modelVwMovimentacao->getMovimentacoesData($data_pesquisa, $id_usuario, $conta);   
                
        $this->view->movimentacoes = $movimentacoes;
        
        if ($data !== Zend_Auth::getInstance()->getIdentity()->data_usuario) {
            Zend_Auth::getInstance()->getIdentity()->data_usuario = $data;
        }
        
        /**
         * total receitas e despesas
         */
        $totalReceita = 0;
        $totalDespesa = 0;
        $totalReceitaPrevisto = 0;
        $totalDespesaPrevisto = 0;
        foreach ($movimentacoes as $movimentacao) {
            if ($movimentacao->realizado) {
                if ($movimentacao->id_tipo_movimentacao == 1) {
                    $totalReceita += $movimentacao->valor_movimentacao;
                } elseif ($movimentacao->id_tipo_movimentacao == 2) {
                    $totalDespesa += $movimentacao->valor_movimentacao;
                }
            } 
            if ($movimentacao->id_tipo_movimentacao == 1) {
                $totalReceitaPrevisto += $movimentacao->valor_movimentacao;
            } elseif ($movimentacao->id_tipo_movimentacao == 2) {
                $totalDespesaPrevisto += $movimentacao->valor_movimentacao;
            }
        }        
        $this->view->totalReceita = $totalReceita;
        $this->view->totalDespesa = $totalDespesa;
        $this->view->totalReceitaPrevisto = $totalReceitaPrevisto;
        $this->view->totalDespesaPrevisto = $totalDespesaPrevisto;
        $this->view->saldo = $totalReceita + $totalDespesa;
        $this->view->saldoPrevisto = $totalReceitaPrevisto + $totalDespesaPrevisto;
        
    }
    
    /**
     * GRAFICOS
     */
    public function graficoReceitasDespesasAction() {
        
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        $ano = date('Y');
        
        $modelRelatorios = new Model_Relatorios();
        $relatorios = $modelRelatorios->getTotalValoresMes($id_usuario);
        
        $jsonData = array(
            'dataCount' => 0
        );
        
        if ($relatorios->count() > 0) {
            $jsonData['categories']['data'] = array();
            $jsonData['receita']['data'] = array();
            $jsonData['despesa']['data'] = array();
            foreach ($relatorios as $key => $relatorio) {
                
                $relatorio->mes = Controller_Helper_Date::getNameMonth($relatorio->mes);
                
                if (!in_array($relatorio->mes, $jsonData['categories']['data'])) {
                    $jsonData['categories']['data'][] = $relatorio->mes;
                }                
                
                if ($relatorio->id_tipo_movimentacao == 1) {
                    $jsonData['receita']['data'][] = $relatorio->total;
                } else {
                    $jsonData['despesa']['data'][] = $relatorio->total * -1;
                }
            }
        }
        
        echo json_encode($jsonData);
        
    }
    
    public function graficoCategoriasAction () {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $modelCategoria = new Model_Categoria();
        $gastosCategoria = $modelCategoria->getGastosCategoriasMes($id_usuario);
        
        $jsonData = array(
            'dataCount' => 0
        );
        
        if ($gastosCategoria->count() > 0) {
            $jsonData['dataCount'] = $gastosCategoria->count();
            foreach ($gastosCategoria as $key => $gasto) {

                $jsonData['data'][$key]['name'] = $gasto->descricao_categoria;
                $jsonData['data'][$key]['y'] = $gasto->total * -1;

                if ($key == 0) {
                    $jsonData[$key]['sliced'] = true;
                } else {
                    $jsonData[$key]['sliced'] = false;
                }
            }
        }

        echo json_encode($jsonData);
    }
    
    public function graficoOrcamentoAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $modelMeta = new Model_Meta();
        $metas = $modelMeta->getGastosMetasUsuario($id_usuario);
        
        $jsonData = array(
            'dataCount' => 0
        );
        
        if ($metas->count() > 0) {
            $jsonData['dataCount'] = $metas->count();
            foreach ($metas as $key => $meta) {
                //$jsonData['receita']['data'][] = $relatorio->total;
                $jsonData['categories']['data'][] = $meta->descricao_categoria;
                $jsonData['total_orcamento']['data'][] = $meta->valor_meta;
                $jsonData['total_despesa']['data'][] = View_Helper_Movimentacao::getTotalGastoCategoriaMes($meta->id_categoria) * -1;
                //$jsonData['porcentagem']['data'][] = $meta->porcentagem;
                //$jsonData['projecao']['data'][] = View_Helper_Meta::getProjecaoMeta($meta->porcentagem);
            }
        }

        //Zend_Debug::dump($metas);
        
        echo json_encode($jsonData);
    }

}

