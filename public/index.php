<?php
#
//echo dirname( __FILE__ ).'<br>';
//echo $_SERVER['PHP_SELF'];

ini_set('display_errors', 1);  
ini_set('log_errors', 1);  
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');  
error_reporting(E_ALL); 


error_reporting(E_ALL | E_STRICT);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
@date_default_timezone_set('America/Bogota');

#Define FILESYSTEM_ROOT_PATH, site root.
defined('FILESYSTEM_ROOT_PATH')
    || define("FILESYSTEM_ROOT_PATH", dirname( getcwd() ));

    
#Define path to application directory
defined( 'APPLICATION_PATH' )
    || define( 'APPLICATION_PATH', FILESYSTEM_ROOT_PATH . '/application' );


#Define application environment
defined( 'APPLICATION_ENV' )
    || define( 'APPLICATION_ENV', ( getenv( 'APPLICATION_ENV' ) ? getenv( 'APPLICATION_ENV' ) : 'production' ) );


#rute external modules
defined('FILESYSTEM_APPLICATION_APPS_PATH')
    || define('FILESYSTEM_APPLICATION_APPS_PATH', "apps");



# Ensure library/ is on include_path
set_include_path(implode( PATH_SEPARATOR, array(
                realpath( APPLICATION_PATH . '/../library' )
                .PATH_SEPARATOR .realpath( APPLICATION_PATH . '/../library/doctrine' )
                .PATH_SEPARATOR . get_include_path(),
) ) );

require_once APPLICATION_PATH . "/../library/App/validate.php";
require_once APPLICATION_PATH . "/../public/plugins/csvImport.php";
require_once APPLICATION_PATH . "/../public/plugins/IMGupload.php";
/*require_once APPLICATION_PATH . "/../public/plugins/tinymceimg.php";*/

/** Zend_Application */
require_once 'Zend/Application.php';  

#Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.php'
);

//Cache Options
$frontendOptions = array('lifetime' => 7200,'automatic_serialization' => true);
$backendOptions = array('cache_dir' => '../data/cache/');
$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
Zend_Registry::set('cache',$cache);

$application->bootstrap()
    ->run();

/// THis is a change to check