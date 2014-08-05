<?php

// Produccion.
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
               /*  'host'     => 'localhost',
				 'username' => 'bbankorg_root',
                'password' => 'root123',
               'dbname'   => 'bbankorg_bbank'
               'username' => 'root',
                'password' => 'fj4khDb3vy3Fkyq2',
               'dbname'   => 'm8'*/
            )
        )

    ),
    'lang'    => 'en',
    'siteurl' => 'http://' .  $_SERVER['SERVER_NAME'] . '/'
);