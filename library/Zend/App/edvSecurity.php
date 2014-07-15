<?php

//APi Enterdev E-SAP
require_once 'Zend/Loader.php';
require_once 'Zend/Controller/Action/Helper/Redirector.php';

class App_edvSecurity
{
    
    protected static $_instance = null;

    protected $_bootstrap   = null;
    protected function __construct() {}
    protected function __clone() {}
    protected $_db;
    protected $_helper  = null;
    protected $_base_url = null;

    public static function getInstance()
    {
        if ( null === self::$_instance )
        {
                self::$_instance = new self();
                self::$_instance->_initialize();
        }

        return self::$_instance;
    }

    protected function _initialize()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $this->_bootstrap = $frontController->getParam( "bootstrap" );
        
    }
	
	
	#devuelve verdadero o falso si el usuario esta logueado o no
	public function isLogged()
	{
	   //$logged = Zend_Auth::getInstance()->getIdentity();
	     $auth = Zend_Auth::getInstance();
	     if ($auth->hasIdentity())
	     {
	         return true;
	     }
    	     return false;	   
	}
        #devuelve los datos del usuario logueado
        public function userLoggued()
        {
             $auth = Zend_Auth::getInstance();
	     if ($auth->hasIdentity())
	     {
	         return $auth->getIdentity();
	     }
    	     return 'false';
        }
	#devuelve a la pagina de login
	public function gotoLogin($sessionsave = true)
	{
	   //$this->_redirect('login');
	   //$this->_helper->redirector->gotoUrl('login',  array());
           //echo '<pre>' . print_r($this->_bootstrap->getOptions(),true) . '</pre>';
           $session        = new Zend_Session_Namespace();
           $session->next = '';
           if($sessionsave){
               //$session->next  = $_SERVER['REDIRECT_URL'];
           }else{
              $session->next = '';
           }
       	   $config = $this->_bootstrap->getOptions();
           $base_url = $config['siteurl'];
	   echo "<script>parent.parent.document.location.href = '{$base_url}'</script>";
           exit;
	}

        /**
         * Valida si el usuario tiene el perfil que es
         * @param String $perfiles perfiles uno solo o separado por comas
         * @return bool true o false
         */
        public function validate($strPerfiles = '')
        {
            if ($this->isLogged())
            {

                if(false === empty($strPerfiles))
                {
                    $arrayPerfiles = explode(',', $strPerfiles);
                    #Si el rol de la persona no concuerda con el pasado, niega el acceso
                    if (false === in_array($this->userLoggued()->perfil_id , $arrayPerfiles))
                    {
                        return false;
                    }

                }
                return true;
             
            } else {
                return false;
            }
        }

}