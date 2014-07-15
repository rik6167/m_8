<?php

/**
 * App_Auth
 *
 * @author jfalvarez, vicman
 */
class App_Auth
{
    protected static $_instance = null;
    
    protected $_bootstrap   = null;
    protected $_identity   = 'nombre_usuario';
    protected $_credential = 'clave';

    protected function __construct() {}
    protected function __clone() {}

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

    public function setIdentityColumn( $identity = "nombre_usuario" )
    {
        $this->_identity = $identity;
        return $this;
    }

    public function getIdentityColumn()
    {
        return $this->_identity;
    }

    public function setCredentialColumn( $credential = "clave" )
    {
        $this->_credential = $credential;
        return $this;
    }

    public function getCredentialColumn()
    {
        return $this->_credential;
    }

    public function setTable( $table )
    {
        $this->_table = $table;
        return $this;
    }

    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Get Zend_Auth.
     *
     * @return Zend_Auth
     */
    public function getZendAuth()
    {
        return Zend_Auth::getInstance();
    }

    public function authenticate( $username, $password,$extraparam = array() )
    {

       
        $adapter = new Zend_Auth_Adapter_DbTable( $this->_bootstrap->getResource( "db" ) );
        $adapter->setTableName( $this->getTable() )
                ->setIdentityColumn( $this->getIdentityColumn() )
                ->setCredentialColumn( $this->getCredentialColumn() )
                ->setIdentity( $username );
        $adapter->setCredential( md5 ( $password ) );
        //pprint_r($adapter);
        //exit;

        $authResult = $this->getZendAuth()->authenticate( $adapter );
        //pprint_r($adapter);
        //exit;

        if ( false !== $authResult->isValid() )
        {

            $row = $adapter->getResultRowObject( null, $this->getCredentialColumn() );
            //pprint_r($row);
    
            if ( 'S' == $row->activo )
            {
               if(false == empty($extraparam)){
                    foreach($extraparam as $index => $data){
                        $row->$index = $data;
                    }
                }
                //$row->segmento = '123';
                $session = new Zend_Session_Namespace( "Zend_Auth" );
                $session->setExpirationSeconds( 86400 ); // 24 Horas
                $this->getZendAuth()->getStorage()->write( $row );

                return true;
            }
        }

        $this->getZendAuth()->clearIdentity();

        return false;
    }


    /**
     * getUserRole: devuelve los roles que tenga el usuario
     *
     * @author vicman
     */
    public function getUserRole($user_id){
        if(empty($user_id)){
            return false;
        }
        
    }
}
