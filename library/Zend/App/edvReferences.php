<?php

/**
 * Traer la informacion comun de la BD
 *
 * @author vicman
 */
require_once 'Zend/Loader.php';
require_once 'Zend/Controller/Action/Helper/Redirector.php';

class App_edvReferences
{
    
	protected $_helper  = null;
	protected $base_url = null;
		
	public function __construct()
        {

		$this->base_url = Zend_Registry::get('base_url');
	
		$this->init();


      }
     public function init()
    {
	   
    }
	
	#devuelve la lista de ciudades
	function getCountries()
	{
	      //Zend_Loader::loadClass('Countries');
              $countriesObj = new Default_Model_Countries();
              $countries    = $countriesObj->fetchAll();
              return $countries;
		  
	}
	
	#trae la lista de departamentos de un pais
	function getStates(Array $options = array())
	{
	     //Zend_Loader::loadClass('States');
             $statesObj = new Default_Model_States();
             //$states = $statesObj->find($country_id)->current();
             $states = $statesObj->getStates($options);
             return $states;
	
	}
	

}