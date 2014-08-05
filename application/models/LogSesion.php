<?php
/**
 * tblogsesion: log de ingresos al sistema
 *
 * @author vicman
 * @version 27-04-2010
 */
class Default_Model_LogSesion extends Zend_Db_Table_Abstract
{
	protected $_name = 'logsesion';
	protected $_db;
        public function __construct() {
            $this->_db = parent::$_defaultDb;
        }

        public function __destruct() {

        }
	
}