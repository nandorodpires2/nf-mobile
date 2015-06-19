<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    
    /**
     * Cria uma instancia do Autoloader
     */
    protected function _initAutoloader() {
     
        $autoloader = new Zend_Application_Module_Autoloader(array(
           'namespace'  => '',
           'basePath'   => APPLICATION_PATH
        ));
        
        $autoloader->addResourceTypes(array(
            'actionhelper' => array(
                'path' => 'helpers/actions',
                'namespace' => 'Controller_Helper'
            ),
            'viewhelper' => array(
                'path' => 'helpers/views',
                'namespace' => 'View_Helper'
            )
        ));
        
    }
    
    protected function _initRegistry() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $config);
        
        $mail_config = array(            
            'auth' => $config->mail->auth,        
            'username' => $config->mail->username,
            'password' => $config->mail->password
        );
        
        if (APPLICATION_ENV === 'development') {
            $transport = new Zend_Mail_Transport_File($config->mail->host, $mail_config);
        } else {
            $transport = new Zend_Mail_Transport_Smtp($config->mail->host, $mail_config);
        }        
        
        Zend_Registry::set('mail_transport', $transport);
        
    }
    
    /**
     * _initController
     * 
     * Configura o controller
     */
    protected function _initController() {
    	$controller = Zend_Controller_Front::getInstance(); 
        $controller->registerPlugin(new Plugin_Auth());
        $controller->registerPlugin(new Plugin_Layout());
        $controller->registerPlugin(new Plugin_ErrorHandler());
        $controller->registerPlugin(new Plugin_Application());
        $controller->registerPlugin(new Plugin_Movimentacao());
        $controller->registerPlugin(new Plugin_Cartao());
        $controller->registerPlugin(new Plugin_Acl());
        $controller->registerPlugin(new Plugin_Log());
    }
    
    /**
     * init session
     */
    protected function _initSession() {
        Zend_Session::start();        
        $session = new Zend_Session_Namespace();
        $session->lang = isset($session->lang) ? $session->lang : "pt_BR";        
        Zend_Registry::set('session', new Zend_Session_Namespace);
    }
    
    public function _initCache() { 

        mb_internal_encoding("UTF-8");

        $frontend = array('lifetime' => 7200, 'automatic_serialization' => true);
        $cachedir = realpath(APPLICATION_PATH . '/data/cache');

        $backend = array('cache_dir' => $cachedir);
        $cache = Zend_Cache::factory('Core', 'File', $frontend, $backend);

        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        Zend_Registry::set('cache', $cache);

        // Cache dos Objetos Date. Utilize sempre. A não utilizaçao causa erros no zend cache.
        Zend_Locale::setCache($cache);

    }

    /**
     * Definindo a configuracao de Layout
     */
    protected function _initLayout() {
                
        $configs = array(
            'layout' => 'layout',
            'layoutPath' => APPLICATION_PATH . '/layouts/scripts'
        );
        // inicia o componente
        Zend_Layout::startMvc($configs);
        
    }
    
    /**
     * Zend Locale
     */
    public function _initLocale() {
        //instancia o componente usando  pt-BR como padrao
        $session = Zend_Registry::get('session');
        
        $locale = new Zend_Locale($session->lang);
        //salva o memso no Zend_Registry
        Zend_Registry::set('Zend_Locale', $locale);
        $translationFile = APPLICATION_PATH . '/lang/' . $locale . '.php';
        $translate = new Zend_Translate('array', $translationFile, $locale);
        Zend_Registry::set('Zend_Translate', $translate);
    }

}

