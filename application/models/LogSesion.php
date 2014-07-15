<?php
/*
 * Esta clase hace referencia a la tabla tblogsesion.
 */

/**
 * tblogsesion: log de ingresos al sistema
 *
 * @author vicman
 * @version 27-04-2010
 */
class Default_Model_LogSesion extends Zend_Db_Table_Abstract
{
	protected $_name = 'em_logsesion';
	protected $_db;
        public function __construct() {
            $this->_db = parent::$_defaultDb;
        }

        public function __destruct() {

        }
	
}