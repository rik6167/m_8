<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Test
 *
 * @author vicman
 */
class App_Test
{
    protected static $_instance = null;
    public static function getInstance()
    {
        if ( null === self::$_instance )
        {
                self::$_instance = new self();
        }

        return self::$_instance;
    }
    public function __construct()
    {

    }

    public function miName()
    {
        return 'hola mundo';
    }
}

