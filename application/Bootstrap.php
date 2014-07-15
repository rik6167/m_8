<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    private $_log;
    protected function _initAutoload()
    {
           $autoloader   = new Zend_Application_Module_Autoloader( array(
            "namespace" => "Default_",
            "basePath"  => dirname( __FILE__ ),
        ) );
        $this->setRegistry();
        $this->setLog();
        return $autoloader;
    }

    protected function _initLoadAuth()
    {
        $front = Zend_Controller_Front::getInstance();

    }

    public function _initErrors()
    {
        $errors = App_Errors::getInstance();
     //   $this->getResource( "view" )->errors = $errors;
        Zend_Registry::set( "errors", $errors );
        return $errors;
    }

    public function setLog()
    {
        $options  =  $this->getOptions();
        $log_file =  $options['error_log'];
        $stream   = fopen($log_file, 'a', false);
        if (!$stream) {
            throw new Exception('Failed to open stream');
        }
        $writer = new Zend_Log_Writer_Stream($stream);
        $this->_log = new Zend_Log($writer);
    }

    /**
     * Se dejan variables disponibles en el sistema
     * que generalmente se van a requieren en su
     * uso normal
     */
    public function setRegistry()
    {
        $this->_registry = Zend_Registry::getInstance();
        $options =  $this->getOptions();
        $baseUrl = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/index.php'));
        $this->_registry->set('base_path_html', realpath('.'));
        $this->_registry->set('base_path_app',  realpath('..'));
	    $this->_registry->set('base_url',  $baseUrl);
        $this->_registry->set('siteurl',$options['siteurl']);
        $this->_registry->set('files_path',$options['path_files']);
    }      
}


