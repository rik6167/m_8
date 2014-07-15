<?php
/*
 carga el idioma 
*/
require_once 'Zend/Controller/Plugin/Abstract.php';

class LoadLang extends Zend_Controller_Plugin_Abstract
{
    var $locale;
    public function __construct($lang = "es")
    {
       try 
		{
			$this->locale = new Zend_Locale('auto');
		 } catch (Zend_Locale_Exception $e) {
			$this->locale = new Zend_Locale('es');
		} 
		$this->load($lang);

    }
	 #recibe el idioma por defecto guardado en el config
	private function load($lang)
	{
	   #lang
	   Zend_Registry::set('Zend_Locale', $this->locale);
	   //$lang = 'en';
      $defaultlanguage =  $lang;


		$langFile =  FILESYSTEM_APPLICATION_PATH . '/languages/' . $lang . '/lang.php';
		// Si el archivo de idioma no existe cargamos un archivo por defecto
		if ( !file_exists($langFile) ) $langFile = FILESYSTEM_APPLICATION_PATH . '/languages/es/lang.php';
		
		// Guardamos el objeto en el registro
		$translate = new Zend_Translate( 'Array', $langFile,$lang  );
		if (!$translate->isAvailable($this->locale->getLanguage())) {
			// when user requests a not available language reroute to default
			$translate->setLocale($defaultlanguage);
			
		}
		
		$translate->getLocale();
		Zend_Registry::set('translate', $translate);
		
	}
	
	public function loadAppLang($lang = '')
	{
	   Zend_Registry::set('Zend_Locale', $this->locale);
	   //$langFile =  FILESYSTEM_APPLICATION_PATH . '/languages/' . $lang . '/lang.php';
	  // echo FILESYSTEM_APPLICATION_PATH;
	}
	
	


}
            	
