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
   //echo $select->__toString();
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
	
		public function getRow_join($pWhere='', $dbtablea, $dbtableb, $dbselect, $conditionb,$order='') {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => $dbtablea), $dbselect)
				->join(array ('b' => $dbtableb ),  $conditionb, array ())
                ->where($where)
				->order($order);
				//echo $select->__toString();
        return $this->_db->fetchRow($select);
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
                
        public function getRows_leftjoinNoGroup($pWhere='', $dbtablea, $dbtableb, $dbselect, $conditionb,$order='') {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => $dbtablea), $dbselect)
				->joinLeft(array ('b' => $dbtableb ),  $conditionb, array ())
                ->where($where)
				
				->order($order);
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
    
		public function getRows_innerjoin($pWhere='', $dbtablea, $dbtableb, $dbselect, $conditionb,$order='',$group='') {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => $dbtablea), $dbselect)
				->join(array ('b' => $dbtableb ),  $conditionb, array ())
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
			//	echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	
	public function get_orders($pWhere='',$select) {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => 'program_redemtion'), $select)
				->join(array ('b' => 'products' ),  'a.id_product = b.id ', array ())
				->join(array ('c' => 'm8_status' ),  'a.status = c.id_status ', array ())
				->join(array ('d' => 'program_participants' ),  'd.id_participant = a.id_participant ', array ())
				->join(array ('e' => 'licenses' ),  'a.id_licence = e.id_licence', array ())
                ->where($where)
				->order('a.id_redemption DESC');
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	
	public function get_fulfilment_list($pWhere='',$select) {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
				->from(array('a' => 'program_redemtion'), $select)
				->join(array ('b' => 'products' ),  'a.id_product = b.id', array ())
				->join(array ('c' => 'm8_status' ),  'a.status = c.id_status', array ())
				->join(array ('d' => 'supplier' ),  'd.id_supplier = b.id_supplier', array ())
				->join(array ('e' => 'licenses' ),  'a.id_licence = e.id_licence', array ())
				->join(array ('f' => 'client' ),  'f.id_client = e.client_id', array ())
				->join(array ('g' => 'program_participants' ),  'g.id_participant = a.id_participant', array ())
				->join(array ('h' => 'subcategories' ),  'b.id_subcategory = h.id_subcategory', array ())
                ->where($where)
				->order('a.id_redemption DESC');
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	
		public function get_fulfilment($pWhere='') {        
        $where  = "1";
        $where .= (empty($pWhere)) ? '' : ' and '.$pWhere ;
        $select = $this->_db->select()
		->from(array('a' => 'program_redemtion'), array("CONCAT(e.jobnumber, '-', a.id_redemption) AS ordernumber",'h.gst','f.client','e.jobnumber', 
							'e.name as licencename','b.sku','d.supplier', 'd.id_supplier','a.invoice_c',
							'CASE WHEN   
								(SELECT price_nogst FROM  products_temp  WHERE sku = b.sku  ORDER BY price_nogst ASC  LIMIT 1) IS NULL THEN b.price_nogst  
							  ELSE 
								(SELECT price_nogst FROM products_temp WHERE sku = b.sku ORDER BY price_nogst ASC LIMIT 1) 
							 END AS `buy_price_no_gst`',
							 
							 'CASE  WHEN 
								(SELECT freight_cost FROM products_temp WHERE sku = b.sku ORDER BY price_nogst ASC LIMIT 1) IS NULL THEN b.freight_cost 
							 ELSE 
								(SELECT freight_cost FROM  products_temp  WHERE sku = b.sku ORDER BY price_nogst ASC  LIMIT 1)  
							 END AS `buy_freight_no_gst`',
							 
							' CASE  WHEN 
								(SELECT freight_cost FROM products_temp WHERE sku = b.sku ORDER BY price_nogst ASC LIMIT 1) IS NULL THEN (b.freight_cost/100)*10
							 ELSE 
								((SELECT freight_cost FROM  products_temp  WHERE sku = b.sku ORDER BY price_nogst ASC  LIMIT 1) /100)*10 
							 END AS `buy_freight_gst`',
			 
							"IF (h.gst = 10, CASE WHEN  (SELECT  price_nogst FROM  products_temp  WHERE sku = b.sku  ORDER BY price_nogst ASC  LIMIT 1) IS NULL 
								THEN (b.price_nogst/100)*10  ELSE ((SELECT price_nogst FROM products_temp WHERE sku = b.sku ORDER BY price_nogst ASC LIMIT 1) /100) *10 END,0) 
							AS buy_price_gst",
							
 
							'(a.points / e.points) AS inv_price_inc',
							'(a.freight_cost / e.points) AS inv_freigh_inc',
							
						   'a.points AS total_points',	
							
									
							'CASE WHEN e.freight_to = 1 AND h.gst = 10 
								THEN (((a.points / e.points) / 110) * 100) 
							WHEN e.freight_to = 2  AND h.gst = 10 
								THEN ((((a.points - a.freight_cost) / e.points ) / 110   ) * 100 ) 
							WHEN e.freight_to = 1 AND h.gst = 0 
								THEN (a.points / e.points)
							WHEN e.freight_to = 2  AND h.gst = 0 
								THEN ((a.points - a.freight_cost) / e.points )	
							END AS inv_price_ex',	
							
							'CASE WHEN e.freight_to = 1 AND h.gst = 10 
								THEN (((a.points  / e.points ) / 110   ) * 10 ) 
							WHEN e.freight_to = 2  AND h.gst = 10 
								THEN ((((a.points - a.freight_cost) / e.points ) / 110   ) * 10 ) 
							WHEN e.freight_to = 1 AND h.gst = 0 
								THEN 0
							WHEN e.freight_to = 2  AND h.gst = 0 
								THEN 0
							END AS inv_gst_price',											
													
							'b.freight_cost AS inv_freigh_ex',	
							'((b.freight_cost / 100) * 10) AS inv_freigh_gst',	
							'b.price_nogst', 
							'b.freight_cost',  
							'a.*', 
							'b.name','c.status AS status_name', 'b.image',
							"CONCAT(g.first_name, ' ', g.last_name)", 'g.address','g.suburb','g.state','g.postcode' ,
							'g.first_name', 'g.last_name','g.phone','g.mobile', 'g.company_name')
		)
				->join(array ('b' => 'products' ),  'a.id_product = b.id', array ())
				->join(array ('c' => 'm8_status' ),  'a.status = c.id_status', array ())
				->join(array ('d' => 'supplier' ),  'd.id_supplier = b.id_supplier', array ())
				->join(array ('e' => 'licenses' ),  'a.id_licence = e.id_licence', array ())
				->join(array ('f' => 'client' ),  'f.id_client = e.client_id', array ())
				->join(array ('g' => 'program_participants' ),  'g.id_participant = a.id_participant', array ())
				->join(array ('h' => 'subcategories' ),  'b.id_subcategory = h.id_subcategory', array ())
                ->where($where)
				->order('a.id_redemption DESC');
				//echo $select->__toString();
        return $this->_db->fetchAll($select);
    }
	
	   public function getToWashQTY() {
        $where = 'b.id_subcategory IN(SELECT b.id_subcategory FROM csvcategory_m8category AS b) 
					AND (b.id_subcategory <> 0) 
					AND `a`.`sku` IN(	SELECT `c`.`sku` 
										FROM (`products_temp` AS `c` 
										LEFT JOIN `products` AS `d` ON ((`c`.`sku` = `d`.`sku`))) 
										WHERE ((`c`.`last_update` <> `d`.`last_update`) OR (NOT(`c`.`sku` IN(SELECT `f`.`sku` FROM `products` AS `f`)))))' ;
        $select = $this->_db->select()
				->from(array('a' => 'products_temp'), array ("COUNT(DISTINCT(id)) AS total_rows" ))
				->join(array ('b' => 'csvcategory_m8category' ),  'a.subcategory = b.csvsubcategory', array ())
				->join(array ('e' => 'subcategories' ),  'e.id_subcategory = b.id_subcategory', array ())
                ->where($where)
				->group('a.sku')
				->order('a.price DESC');
				//echo $select->__toString();
        return $this->_db->fetchRow($select);
    }
	
		public function getToWash() {
        $where = 'b.id_subcategory IN(SELECT b.id_subcategory FROM csvcategory_m8category AS b) 
					AND (b.id_subcategory <> 0) 
					AND `a`.`sku` IN(	SELECT `c`.`sku` 
										FROM (`products_temp` AS `c` 
										LEFT JOIN `products` AS `d` ON ((`c`.`sku` = `d`.`sku`))) 
										WHERE ((`c`.`last_update` <> `d`.`last_update`) OR (NOT(`c`.`sku` IN(SELECT `f`.`sku` FROM `products` AS `f`)))))' ;
        $select = $this->_db->select()
				->from(array('a' => 'products_temp'), array ("a.id AS id",
															  "a.sku AS sku",
															  "a.image AS image",
															  "a.name AS name",
															  "a.description AS description",
															  "a.brand AS brand",
															  "a.rrp AS rrp",
															  "a.id_supplier AS id_supplier",
															  "a.status AS status",
															  "b.id_subcategory AS id_subcategory",
															  "a.price AS price",
															  "a.price_nogst AS price_nogst",
															  "a.freight_cost AS freight_cost",
															  "a.last_update AS last_update_csv",
															  "a.imgSource AS imgSource",
															  "e.id_category AS id_category",
															  "(a.price_nogst + a.freight_cost) AS best_price"))
				->join(array ('b' => 'csvcategory_m8category' ),  'a.subcategory = b.csvsubcategory', array ())
				->join(array ('e' => 'subcategories' ),  'e.id_subcategory = b.id_subcategory', array ())
                ->where($where)
				->group('a.sku')
				->order('best_price ASC');
				//echo $select->__toString();
        return $this->_db->fetchRow($select);
    }
}