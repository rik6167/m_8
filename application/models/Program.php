<?php
/**
 * nom_tabla: program
 *
 * @author Natalia Metaute
 * @version 26-04-2013
 */
class Default_Model_Program extends Zend_Db_Table_Abstract
{
    protected $_db;
    public function __construct() {
        $this->_db = parent::$_defaultDb;
    }

    public function __destruct() {

    }

    public function getRows($pWhere='', $dbtable) {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array ('a' => $dbtable), array('a.*','b.status as status_name','c.name as licence_name'))
				->join(array ('b' => 'm8_status' ),'a.status = b.id_status', array ())
				->join(array ('c' => 'license_types' ),'a.licence = c.id', array ())
                ->where($where);
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	
}