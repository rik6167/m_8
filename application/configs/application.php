<?php
// Config  file.
return array_merge_recursive( array(
    'includePaths' => array(
        'library' => APPLICATION_PATH . '/../library',
        // 'models' => APPLICATION_PATH . '/models'
    ),
    'bootstrap' => array(
        'path' => APPLICATION_PATH . '/Bootstrap.php',
        'class' => 'Bootstrap'
    ),
    'autoloaderNamespaces' => array(
        'App_',
        'Model_',
        'Bvb_',
        'My_',
        'Doctrine',
        'Proxies'
        //'Default_'
    ),
	

          'resources' => array(
            'frontController' => array(
            'moduleDirectory' => APPLICATION_PATH . '/modules'
        ),
  
        'locale' => array(
            'default' => 'es_CO'
        ),

        'view' => array(
            'helperPath' => array(
                'App_View_Helper_' => APPLICATION_PATH . '/../library/App/View/Helper'
            )
        ),
        'layout' => array(
            'layout' => 'layout',
            'layoutPath' => APPLICATION_PATH . '/layouts/scripts'
        ),
        'db' => array(
            'adapter' => 'pdo_mysql', //pdo_mysql,Pdo_Pgsql
            'params' => array(
            'charset' => 'utf8'
            )
        )
    ),
    'error_log' => APPLICATION_PATH . '/../data/logs/errores.log',
    'path_files' => APPLICATION_PATH . '/../data/'
), include dirname( __FILE__ ) . '/' . APPLICATION_ENV . '.application.php' );