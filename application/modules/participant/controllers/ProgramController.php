<?php
/**
 * Management for catalog categories
 *
 */
class Participant_ProgramController extends Zend_Controller_Action {
	function init() {
		$this->view->assign ( 'baseUrl', $this->getRequest ()->getBaseUrl () );
		$this->initView ();
		$this->_user = App_edvSecurity::getInstance ();
		
		$this->_userId = $this->_user->userLoggued ()->id;
		$this->_helper->layout->setLayout ( 'layout_shop' );
	}
	
	public function indexAction() {
		$ObjGen = new Default_Model_Generico ();
		$idLicence = $_SESSION['licence'];
		$this->view->page = $this->_request->getParam ( "pg" );
		$this->view->id_licence = $idLicence;
		$this->view->licence_detail = $ObjGen->getRow ( "id_licence='" . $idLicence."'", "licenses" );
	}
	
  public function rewardsAction() {
		$ObjGen = new Default_Model_Generico ();
		$idLicence = $_SESSION['licence'];
		$this->view->id_licence = $idLicence;
		$this->view->categories_list = $ObjGen->getLista_titles ( "a.id_category IN (SELECT c.id_category FROM subcategories AS c WHERE (SELECT COUNT(d.id_subcategory) FROM products d WHERE d.id_subcategory= c.id_subcategory GROUP BY d.id_subcategory) != '' AND c.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence}) GROUP BY c.id_category)", "categories AS a", array ('a.*',
				'(SELECT COUNT(b.id_category) FROM products b WHERE b.id_category= a.id_category AND b.id_subcategory IN (SELECT id_subcategory FROM program_catalogue WHERE id_licence = '.$idLicence.') GROUP BY b.id_category) AS qty' ), 'a.category ASC' );
		
		$this->view->subcategories_list = $ObjGen->getLista_titles ( "(SELECT COUNT(b.id_subcategory) FROM products b WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) != '' AND
a.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence})", "subcategories AS a", array (
				'a.*',
				'(SELECT COUNT(b.id_subcategory) FROM products b  WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) AS qty' 
		), 'a.subcategory ASC' );
	}
	
	public function shopAction() {
		$id_category = $this->_request->getParam ( "ct" );
		$id_subcategory = $this->_request->getParam ( "sct" );
		$sctname = $this->_request->getParam ( "sctname" );
		$idLicence = $_SESSION['licence'];
		$this->view->id_licence = $idLicence;
		$ObjGen = new Default_Model_Generico ();
		
		if (empty ( $id_subcategory )) {
			$this->view->products_list = $ObjGen->getLista_titles ( "id_category=" . $id_category . " and status='Enabled'", "products", array('*','ROUND((rrp * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.'))) AS points'), 'id_subcategory ASC' );
			$this->view->sc_name = '';
		} else {
			$this->view->products_list = $ObjGen->getLista_titles ( "id_subcategory=" . $id_subcategory . " AND status='Enabled'", "products", array('*','ROUND((rrp * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.'))) AS points'), "" );
			$this->view->sc_name = '/' . $sctname;
		}
		$this->view->category_info = $ObjGen->getRow_select ( "id_category=" . $id_category, "categories", array (
				'a.*',
				'(SELECT COUNT(b.id_category) FROM products b  WHERE b.id_category= a.id_category GROUP BY b.id_category) AS qty' 
		) );
		
		$idLicence = $_SESSION['licence'];
		$this->view->id_licence = $idLicence;
		$this->view->categories_list = $ObjGen->getLista_titles ( "a.id_category IN (SELECT c.id_category FROM subcategories AS c WHERE (SELECT COUNT(d.id_subcategory) FROM products d WHERE d.id_subcategory= c.id_subcategory GROUP BY d.id_subcategory) != '' AND c.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence}) GROUP BY c.id_category)", "categories AS a", array ('a.*',
				'(SELECT COUNT(b.id_category) FROM products b WHERE b.id_category= a.id_category AND b.id_subcategory IN (SELECT id_subcategory FROM program_catalogue WHERE id_licence = '.$idLicence.') GROUP BY b.id_category) AS qty' ), 'a.category ASC' );
		
		$this->view->subcategories_list = $ObjGen->getLista_titles ( "(SELECT COUNT(b.id_subcategory) FROM products b WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) != '' AND
a.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence})", "subcategories AS a", array (
				'a.*',
				'(SELECT COUNT(b.id_subcategory) FROM products b  WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) AS qty' 
		), 'a.subcategory ASC' );
		
		$this->view->search_list = $ObjGen->getLista_titles ( "status='Enabled'", "products", array (
				'id as value',
				'name as label',
				'image' 
		), 'name ASC' );
		
		$this->view->categoryId = $id_category;
		
		$this->view->search_list = $ObjGen->getLista_titles ( "status='Enabled'", "products", array (
				'id as value',
				'name as label',
				'image' 
		), 'name ASC' );
		
	}
	
	public function detailsAction() {
		$id_product = $this->_request->getParam ( "product" );
		$idLicence 	= $_SESSION['licence'];
		$ObjGen 		= new Default_Model_Generico ();
		$this->view->id_licence = $idLicence;
		$this->view->product_info = $ObjGen->getRow_select ( "id=" . $id_product, "products", array ('a.*','ROUND((a.rrp * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.'))) AS points') );
			$idLicence = $_SESSION['licence'];
		$this->view->id_licence = $idLicence;
		$this->view->categories_list = $ObjGen->getLista_titles ( "a.id_category IN (SELECT c.id_category FROM subcategories AS c WHERE (SELECT COUNT(d.id_subcategory) FROM products d WHERE d.id_subcategory= c.id_subcategory GROUP BY d.id_subcategory) != '' AND c.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence}) GROUP BY c.id_category)", "categories AS a", array ('a.*',
				'(SELECT COUNT(b.id_category) FROM products b WHERE b.id_category= a.id_category AND b.id_subcategory IN (SELECT id_subcategory FROM program_catalogue WHERE id_licence = '.$idLicence.') GROUP BY b.id_category) AS qty' ), 'a.category ASC' );
		
		$this->view->subcategories_list = $ObjGen->getLista_titles ( "(SELECT COUNT(b.id_subcategory) FROM products b WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) != '' AND
a.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence})", "subcategories AS a", array (
				'a.*',
				'(SELECT COUNT(b.id_subcategory) FROM products b  WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) AS qty' 
		), 'a.subcategory ASC' );
		
		$this->view->search_list = $ObjGen->getLista_titles ( "status='Enabled'", "products", array (
				'id as value',
				'name as label',
				'image' 
		), 'name ASC' );
	}
}
