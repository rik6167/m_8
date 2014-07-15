<?php

//APi Enterdev E-SAP para traer la informacion mas comun
/*
1-paises

*/
require_once 'Zend/Loader.php';
require_once 'Zend/Controller/Action/Helper/Redirector.php';

class edvReferences
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
	      Zend_Loader::loadClass('Countries');
		  $countriesObj = new Countries();
		  $countries    = $countriesObj->fetchAll();
		  return $countries;		 
		  
	}
	
	#trae la lista de departamentos de un pais
	function getStates($country_id = null)
	{
	     Zend_Loader::loadClass('States');
		 $statesObj = new States();
		 $states = $statesObj->find($country_id)->current();
		 return $states;
	
	}
	

}