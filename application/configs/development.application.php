<?php

// Desarrollo.
return array(
    'phpSettings' => array(
    'display_startup_errors' => 1,
    'display_errors'         => 1,
    'error_reporting'        => E_ALL & ~E_NOTICE
    ),
    'resources' => array(
        'frontController' => array(
        'baseUrl'         => '/public/',
        'throwExceptions' => true
        ),
      'db' => array(
		'params'   => array(
                'host'     => 'localhost',
                'username' => 'root',
                'password' => '',
                'dbname'   => 'motiv8_zf'
            )
	/* 		         'db' => array(
               'params'   => array(
                'host'     => 'localhost',
                'username' => 'root',
                'password' => 'fj4khDb3vy3Fkyq2',
               'dbname'   => 'm8'
            )*/
        )

    ),
    'siteurl' => 'http://' .  $_SERVER['SERVER_NAME'] . '/'
);