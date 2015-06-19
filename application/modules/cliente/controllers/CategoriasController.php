<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CategoriasController
 *
 * @author Fernando Rodrigues
 */
class Cliente_CategoriasController extends Zend_Controller_Action {
    
    public function init() {
        $messages = $this->_helper->FlashMessenger->getMessages();
        $this->view->messages = $messages;
    }
    
    public function indexAction() {
     
        // busca as categorias do usuario
        $modelCategoria = new Model_Categoria();
        $categorias = $modelCategoria->getCategoriasUsuario();
        $this->view->categorias = $categorias;
        
    }
    
    /**
     * Nova categoria
     */
    public function novaCategoriaAction() {
        
        $formCategoriasCadastro = new Form_Cliente_Categorias_Cadastro();
        $this->view->formCategoriasCadastro = $formCategoriasCadastro;
        
        if ($this->_request->isPost()){
            $dadosCategoria = $this->_request->getPost();
            if ($formCategoriasCadastro->isValid($dadosCategoria)) {
                $dadosCategoria = $formCategoriasCadastro->getValues();
                
                if (empty($dadosCategoria['id_categoria_pai'])) {
                    $dadosCategoria['id_categoria_pai'] = null;
                }
                
                $modelCategoria = new Model_Categoria();
                
                if ($this->ifExist($dadosCategoria['descricao_categoria'])) {                
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'alert alert-warning',
                        'message' => 'Categoria já cadastrada!'
                    ));
                    $this->_redirect("cliente/categorias/nova-categoria");
                } 
                
                try {
                    $modelCategoria->insert($dadosCategoria);
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'alert alert-success',
                        'message' => 'Categoria Cadastrada com sucesso!'
                    ));                    

                } catch (Exception $ex) {
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'alert alert-danger',
                        'message' => 'Houve um erro ao cadastrar a categoria. Favor tentar mais tarde!'
                    ));
                }
                $this->_redirect("cliente/categorias/index");
                
            }
        }
    
    }
    
    /**
     * 
     */
    public function editarAction() {
        
        $id_categoria = $this->_getParam("id_categoria");
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        // busca dados da categoria
        $modelCategoria = new Model_Categoria();
        $categoria = $modelCategoria->fetchRow("id_categoria = {$id_categoria} and id_usuario = {$id_usuario}");
        
        // form de categoria
        $formCategoriaCadastro = new Form_Cliente_Categorias_Cadastro();
        $formCategoriaCadastro->populate($categoria->toArray());
        $formCategoriaCadastro->submit->setLabel("Editar");
        $this->view->formCategoriaCadastro = $formCategoriaCadastro;
        
        if ($this->_request->isPost()) {
            $dadosUpdate = $this->_request->getPost();
            if ($formCategoriaCadastro->isValid($dadosUpdate)) {
                $dadosUpdate = $formCategoriaCadastro->getValues();
                
                try {
                    $modelCategoria->update($dadosUpdate, "id_categoria = {$id_categoria}");
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'alert alert-success',
                        'message' => 'Categoria editada com sucesso!'
                    ));
                } catch (Exception $ex) {
                    $this->_helper->flashMessenger->addMessage(array(
                        'class' => 'alert alert-danger',
                        'message' => 'Houve um erro ao editar a categoria. Favor tente mais tarde!'
                    ));
                }
                $this->_redirect("cliente/categorias/index");
                
            }
        }
        
    }
    
    /**
     * 
     */
    public function excluirAction() {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        
        $id_categoria = $this->_getParam("id_categoria");
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        // busca dados da categoria
        $modelCategoria = new Model_Categoria();
        $categoria = $modelCategoria->fetchRow("id_categoria = {$id_categoria} and id_usuario = {$id_usuario}");
        
        // caso a categoria nao seja do usuario
        if (!$categoria) {
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'alert alert-danger',
                'message' => 'Nenhuma categoria encontrada!'
            ));
            $this->_redirect("cliente/categorias/index");
        }
        
        try {
            $modelCategoria->update(array('ativo_categoria' => 0), "id_categoria = {$id_categoria}");
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'alert alert-success',
                'message' => 'Categoria excluida com sucesso!'
            ));
        } catch (Exception $ex) {
            $this->_helper->flashMessenger->addMessage(array(
                'class' => 'alert alert-danger',
                'message' => 'Houve um erro ao excluir a categoria. Favor tente mais tarde!'
            ));
        }
        $this->_redirect("cliente/categorias/index");
        
    }

    /**
     * Verifica se ja existe o registro no banco
     * @param type $name
     */
    protected function ifExist($name) {
          
        $modelCategoria = new Model_Categoria();
        $where = "descricao_categoria = '" . $name . "' and id_usuario = " . Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $categoria = $modelCategoria->fetchRow($where);
        if ($categoria) {
            return true;
        }
        return false;
        
    }
    
}
