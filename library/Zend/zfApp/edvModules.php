<?

//Con tiene los modulos que puede cargar la app
require_once 'Zend/Loader.php';

class edvModules
{
    
	protected $_db;
	protected $base_url = null;
	
	public function __construct()
    {		
		$this->base_url = Zend_Registry::get('base_url');	
		$this->init();


    }
	public function init()
    {
	   
    }
	
	public function loader()
	{
	
	}
	
	
}