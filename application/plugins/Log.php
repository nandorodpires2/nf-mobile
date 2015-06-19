<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author Fernando
 */
class Plugin_Log extends Zend_Controller_Plugin_Abstract {
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);
        
        if ($request->getControllerName() !== 'ajax' && $request->getControllerName() !== 'log') {
        
            $auth = Zend_Auth::getInstance();        
            $id_usuario = $auth->hasIdentity() ? $auth->getIdentity()->id_usuario : null;
            $params = Zend_Serializer::serialize($request->getParams());

            $modelLog = new Model_Log();

            $insert = array(
                'parametros_log' => $params,
                'id_usuario' => $id_usuario
            );

            try {
                $modelLog->insert($insert);
            } catch (Exception $ex) {

            }
            
        }
    }
    
    public static function getLogUrl($log) {        
        
        $url = "";
        
        $dataLogs = Zend_Serializer::unserialize($log);        
        
        $module = $dataLogs['module'];
        $controller = $dataLogs['controller'];
        $action = $dataLogs['action'];
        
        unset($dataLogs['module']);
        unset($dataLogs['controller']);
        unset($dataLogs['action']);
        
        $url .= $module . "/" . $controller . "/" . $action;
        
        foreach ($dataLogs as $key => $data) {
            //Zend_Debug::dump($data);
            if (!is_array($data) && !is_object($data)) {
                $url .= "/" . $key . "/" . $data;
            }
        }
        
        //Zend_Debug::dump($dataLogs);
        
        return substr($url, 0, 50);
    }
    
}
