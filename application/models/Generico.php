<?php
/**
 * nom_tabla: all tables
 *
 * @author Natalia Metaute
 * @version 26-04-2013
 */
class Default_Model_Generico extends Zend_Db_Table_Abstract
{
    protected $_db;
    public function __construct() {
        $this->_db = parent::$_defaultDb;
    }

    public function __destruct() {

    }

     public function getGeneric($pWhere='', $dbtable) {
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
                ->from($dbtable)
                ->where($where);
            return $this->_db->fetchRow($select);
	  }

    public function getRow($pWhere='', $dbtable) {
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
                ->from($dbtable)
                ->where($where);
		//echo $select->__toString();
        return $this->_db->fetchRow($select);
    }

    public function getRows($pWhere='', $dbtable) {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
                ->from($dbtable)
                ->where($where);
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	
	    public function getRows_order($pWhere='', $dbtable, $order) {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
                ->from($dbtable)
                ->where($where)
				->order($order);
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	
	public function getAll($pWhere='', $dbtable, $pSelect) {
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
                ->from( array('a' => $dbtable), $pSelect)
                ->where($where);
				//echo $select->__toString();
         return $this->_db->fetchAll($select);
    }
	
	public function getLista($pWhere='', $dbtabla, $campos, $order){
        $where  = "1";
		$where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
                    ->from($dbtabla, $campos)
                    ->where($where)
                    ->order($order);
    //echo $select->__toString();
      return $this->_db->fetchPairs($select);
    }
	
	public function getLista_titles($pWhere='', $dbtabla, $campos, $order){
        $where  = "1";
		$where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
                    ->from($dbtabla, $campos)
                    ->where($where)
                    ->order($order);
   // echo $select->__toString();
      return $this->_db->fetchAll($select);
    }	
	
		public function getMenu($pWhere=''){
        $where  = "1";
		$where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
                     ->from(array('a' => 'menu_options'), array('a.*','b.id_client','b.profile','c.alias'))
				     ->joinLeft(array ('b' => 'menu' ),'a.id = b.option_id', array ())
					 ->joinLeft(array ('c' => 'profile' ),'c.id=b.profile', array ())
                    ->where($where)
                    ->order('a.order');
///echo $select->__toString();
        return $this->_db->fetchAll($select);
        
    }
	
	    public function getRows_status($pWhere='', $dbtable) {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => $dbtable), array('a.*','b.status as status_name','b.id_status'))
				->join(array ('b' => 'm8_status' ),'a.status = b.id_status', array ())
                ->where($where);
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	
 public function getRow_status($pWhere='', $dbtable) {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => $dbtable), array('a.*','b.status as status_name','b.id_status'))
				->join(array ('b' => 'm8_status' ),'a.status = b.id_status', array ())
                ->where($where);
				//echo $select->__toString();
        return $this->_db->fetchRow($select);
    }
	
	public function getRows_join($pWhere='', $dbtablea, $dbtableb, $dbselect, $conditionb,$order='') {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => $dbtablea), $dbselect)
				->join(array ('b' => $dbtableb ),  $conditionb, array ())
                ->where($where)
				->order($order);
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	
		public function getRows_join2Tables($pWhere='', $dbtablea, $dbtableb, $dbtablec, $dbselect, $conditionb, $conditionc,$order='') {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => $dbtablea), $dbselect)
				->join(array ('b' => $dbtableb ),  $conditionb, array ())
				->join(array ('c' => $dbtablec ),  $conditionc, array ())
                ->where($where)
				->order($order);
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	
		public function getRows_leftjoin($pWhere='', $dbtablea, $dbtableb, $dbselect, $conditionb,$order='',$group='') {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => $dbtablea), $dbselect)
				->joinLeft(array ('b' => $dbtableb ),  $conditionb, array ())
                ->where($where)
				->group($group)
				->order($order);
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	
	    public function getRowsLimit($pWhere='', $dbtable,$group,$order) {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
                ->from($dbtable)
                ->where($where)
				->group($group)
				->order($order);
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	    public function getRows_group($pWhere='', $dbtable,$group='',$order='',$dbselect) {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => $dbtable), $dbselect)
                ->where($where)
				->group($group)
				->order($order);
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }

    	public function getRow_select($pWhere='', $dbtable,$dbselect) {
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => $dbtable), $dbselect)
                ->where($where);
				//echo $select->__toString();
        return $this->_db->fetchRow($select);
    }	
	
	public function getRows_status_select($pWhere='', $dbtable, $select) {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => $dbtable), $select)
				->join(array ('b' => 'm8_status' ),'a.status = b.id_status', array ())
                ->where($where);
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
}