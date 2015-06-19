<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Messages
 *
 * @author Fernando Rodrigues
 */
class Plugin_Messages extends Zend_Controller_Plugin_Abstract {
    
    public function isConta() {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $modelConta = new Model_Conta();
        $contas = $modelConta->isConta($id_usuario);
        
        $flashMessenger = Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer')->view;
        $flashMessenger->isConta = $contas;
        
        if (!$contas) {
            $url_cadastro = SYSTEM_URL . "cliente/contas/nova-conta";              
            $flashMessenger->alerts = array(
                array(
                    'class' => 'alert alert-warning',
                    'message' => "Por favor cadastre uma nova conta! <a class='text-info' href='{$url_cadastro}'>Cadastrar Conta</a>"
                )
            );
        }        
        
    }
    
    public function isPendencias() {
        
        $id_usuario = Zend_Auth::getInstance()->getIdentity()->id_usuario;
        
        $modelMovimentacao = new Model_Movimentacao();
        $pendencias = $modelMovimentacao->getPendencias($id_usuario);
        $count = $pendencias->count();
        
        if ($count > 0) {       
            $url_pendencias = SYSTEM_URL . "cliente/pendencias";
            $flashMessenger = Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer')->view;
            $flashMessenger->alerts = array(
                array(
                    'class' => 'alert alert-warning',
                    'message' => "Você possui {$count} pendências! <a class='text-info' href='{$url_pendencias}'>Ver Pendências</a>"
                )
            );
        }        
        
    }
    
}
