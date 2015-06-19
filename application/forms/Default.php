<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Default
 *
 * @author Realter
 */
class Form_Default extends Zend_Form {

    public $id_usuario;
    public $_observacao;    
    
    public function init() {        
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        } else {
            $this->id_usuario = 0;
        }
        
        $this->_observacao = new Zend_Form_Element_Textarea('observacao_movimentacao', array(
            'label' => 'Observações',
            'class' => 'form-control',
            'rows' => 5
        ));
        
    }

    /**
     * Retorna as categorias cadastradas no sistema
     * 
     * @return type
     */
    public function getCategorias() {
        
        $multiOptions = array('' => 'Selecione...');
        
        $modelCategoria = new Model_Categoria();
        $categorias = $modelCategoria->getCategoriasUsuario();
                
        foreach ($categorias as $categoria) {
            $multiOptions[$categoria->id_categoria] = $categoria->descricao_categoria;
        }
        
        return $multiOptions;
        
    }
    
    /**
     * Retorna as categorias pai
     * 
     * @return type
     */
    public function getCategoriasPai() {
        
        $multiOptions = array('' => 'Nenhuma...');
        
        $modelCategoria = new Model_Categoria();
        $categorias = $modelCategoria->getCategoriasPai();
                
        foreach ($categorias as $categoria) {
            $multiOptions[$categoria->id_categoria] = $categoria->descricao_categoria;
        }
        
        return $multiOptions;
        
    }
    
     /**
     * Retorna as categorias que ainda nao tem meta cadastrada
     * 
     * @return type
     */
    public function getCategoriasMeta() {
        
        $multiOptions = array('' => 'Selecione...');
        
        $modelCategoria = new Model_Categoria();
        $categorias = $modelCategoria->getCategoriasCadastradasMes($this->id_usuario);
        
        // busca as categorias cadastradas para este mes
        $categoriasCadastradas = $modelCategoria;
        
        foreach ($categorias as $categoria) {
            $multiOptions[$categoria->id_categoria] = $categoria->descricao_categoria;
        }
        
        return $multiOptions;
        
    }    
    
    /**
     * 
     * Retorna as contas do usuario
     * 
     * @param type $idUsuario
     * @return type
     */
    public function getContasUsuario() {
        
        $modelConta = new Model_Conta();
        $contas = $modelConta->getContasUsuario($this->id_usuario, 1);
        
        if ($contas->count() == 0 ) {            
            return $multiOptions = array('' => "Nenhuma conta cadastrada");
        }
        
        foreach ($contas as $conta) {
            $descricaoConta = $conta->descricao_conta . ' - ' . $conta->descricao_tipo_conta;
            $multiOptions[$conta->id_conta] = $descricaoConta;
        }
        
        return $multiOptions;        
        
    }
    
    public function getCartoesUsuario() {
        
        $modelCartao = new Model_Cartao();
        $cartoes = $modelCartao->fetchAll("id_usuario = {$this->id_usuario} and ativo_cartao = 1");
        
        if ($cartoes->count() == 0 ) {            
            return $multiOptions = array('' => "Nenhum cartão cadastrado");
        }
        
        foreach ($cartoes as $cartao) {
            $multiOptions[$cartao->id_cartao] = $cartao->descricao_cartao;
        }
        
        return $multiOptions;
        
    }
    
    /**
     * retorna os bancos
     */
    public function getBancos() {
        
        $multiOptions = array(null => 'Nenhum');
        
        $modelBanco = new Model_Banco();
        $bancos = $modelBanco->fetchAll();        
        
        foreach ($bancos as $banco) {
            $multiOptions[$banco->id_banco] = $banco->nome_banco;
        }
        
        return $multiOptions;
        
    }
    
    /**
     * retorna os tipos de contas
     */
    public function getTipoContas() {
        
        $multiOptions = array("" => "Selecione a conta");
        
        $modelTipoConta = new Model_TipoConta();
        $tipo_contas = $modelTipoConta->fetchAll("id_tipo_conta <> 3");
        
        foreach ($tipo_contas as $tipo_conta) {
            $multiOptions[$tipo_conta->id_tipo_conta] = $tipo_conta->descricao_tipo_conta;
        }
        
        return $multiOptions;
        
    }    
    
    /**
     * retorna os planos 
     */
    public function getPlanos() {
        
        $multiOptions = array();
        
        $modelPlano = new Model_Plano();
        $planos = $modelPlano->getPlanosUsuario();
        
        foreach ($planos as $plano) {
            $multiOptions[$plano->id_plano] = $plano->descricao_plano;
        }
        
        return $multiOptions;
        
    }
    
    /**
     * retorna os estados
     */
    public function getEstados() {
        
        $multiOptions = array("" => "Selecione...");
        
        $modelEstado = new Model_Estado();
        $estados = $modelEstado->fetchAll(null, "descricao_estado asc");
        
        foreach ($estados as $estado) {
            $multiOptions[$estado->id_estado] = $estado->descricao_estado;
        }
        
        return $multiOptions;
        
    }
    
    /**
     * retorna as posicoes para os menus cadastradas
     */
    public function getPosicoesMenu() {
        
        $multiOptions = array("" => "Selecione...");
        
        $modelMenuPosciao = new Model_MenuPosicao();
        $posicoes = $modelMenuPosciao->fetchAll();
        
        foreach ($posicoes as $posicao) {
            $multiOptions[$posicao->id_menu_posicao] = $posicao->posicao;
        }
        
        return $multiOptions;
        
    }
    
    /**
     * retorna as funcionalidade do sistema
     */
    public function getFuncionalidades() {
        $multiOptions = array();
        
        $modelFuncionalidades = new Model_Funcionalidade();
        $funcionalidades = $modelFuncionalidades->getFuncionalidadesPermissao();
        
        foreach ($funcionalidades as $funcionalidade) {
            $multiOptions[$funcionalidade->id_funcionalidade] = "  -  " . $funcionalidade->descricao_permissao . " -> " . $funcionalidade->controller . " - " . $funcionalidade->action;
        }
        
        return $multiOptions;
    }
    
    public function getTiposChamados() {
        
        $multiOptions = array('' => 'Selecione...');
        
        $modelTipoChamado = new Model_TipoChamado();
        $tiposChamados = $modelTipoChamado->fetchAll("ativo = 1", "descricao asc");
        
        foreach ($tiposChamados as $tipo) {
            $multiOptions[$tipo->id_tipo_chamado] = $tipo->descricao;
        }
        
        return $multiOptions;
        
    }
    
}

