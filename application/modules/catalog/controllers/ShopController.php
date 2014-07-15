<?php
/**
 * Management for catalog categories
 *
 */
class Catalog_ShopController extends Zend_Controller_Action {
	function init() {
		$this->view->assign ( 'baseUrl', $this->getRequest ()->getBaseUrl () );
		$this->initView ();
		$this->_user = App_edvSecurity::getInstance ();
		
		$this->_userId = $this->_user->userLoggued ()->id;
		$this->_helper->layout->setLayout ( 'layout_shop' );
	}
	public function indexAction() {
		$table = new Default_Model_Generico ();
		$this->view->categories_list = $table->getLista_titles ( "", "categories AS a", array (
				'a.*',
				'(SELECT COUNT(b.id_category) FROM products b  WHERE b.id_category= a.id_category GROUP BY b.id_category) AS qty' 
		), 'a.category ASC' );
	}
	public function shopAction() {
		$id_category = $this->_request->getParam ( "ct" );
		$id_subcategory = $this->_request->getParam ( "sct" );
		$sctname = $this->_request->getParam ( "sctname" );
		$table = new Default_Model_Generico ();
		if (empty ( $id_subcategory )) {
			$this->view->products_list = $table->getLista_titles ( "id_category=" . $id_category . " and status='Enabled'", "products", array (
					'*' 
			), 'id_subcategory ASC' );
			$this->view->sc_name = '';
		} else {
			$this->view->products_list = $table->getLista_titles ( "id_category=" . $id_category . " AND id_subcategory=" . $id_subcategory . " and status='Enabled'", "products", array (
					'*' 
			), 'id_subcategory ASC' );
			$this->view->sc_name = '/' . $sctname;
		}
		$this->view->category_info = $table->getRow_select ( "id_category=" . $id_category, "categories", array (
				'a.*',
				'(SELECT COUNT(b.id_category) FROM products b  WHERE b.id_category= a.id_category GROUP BY b.id_category) AS qty' 
		) );
		
		$this->view->subcategories_list = $table->getLista_titles ( "a.id_category = " . $id_category, "subcategories AS a", array (
				'a.*',
				'(SELECT COUNT(b.id_subcategory) FROM products b  WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) AS qty' 
		), 'a.subcategory ASC' );
		
		$this->view->categories_list = $table->getLista_titles ( "a.id_category <> " . $id_category, "categories AS a", array (
				'a.*',
				'(SELECT COUNT(b.id_category) FROM products b  WHERE b.id_category= a.id_category GROUP BY b.id_category) AS qty' 
		), 'a.category ASC' );
		$this->view->search_list = $table->getLista_titles ( "status='Enabled'", "products", array (
				'id as value',
				'name as label',
				'image' 
		), 'name ASC' );
	}
	public function detailsAction() {
		$id_product = $this->_request->getParam ( "product" );
		$table = new Default_Model_Generico ();
		$this->view->product_info = $table->getRow_select ( "id=" . $id_product, "products", array (
				'a.*' 
		) );
	}
}
