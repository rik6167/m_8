<?php
/**
 * Management for catalog categories
 *
 */
class Catalog_CatalogController extends Zend_Controller_Action {
	function init() {
		$this->view->assign ( 'baseUrl', $this->getRequest ()->getBaseUrl () );
		$this->initView ();
		$this->_user = App_edvSecurity::getInstance ();
		
		if (! $this->_user->isLogged ()) {
			$this->_user->gotoLogin ();
		}
		if (! validate ( '1' )) {
			$this->_user->gotoLogin ();
		}
		$this->_userId = $this->_user->userLoggued ()->id;
		$this->_helper->layout->setLayout ( 'layout_admin' );
	}
	public function indexAction() {
		$table = new Default_Model_Generico ();
		$status = $this->_request->getParam ( "status" );
		if (empty ( $status )) {
			$this->view->products_list = $table->getRows ( "", "products" );
		} else {
			$this->view->products_list = $table->getRows ( "status='" . $status . "'", "products" );
		}
	}
	public function productsAction() {
		$id = $this->_request->getParam ( "id" );
		$table = new Default_Model_Generico ();
		if (! empty ( $id )) {
			$product_detail = $table->getRow ( "id=" . $id, "products" );
			$subcat = $table->getRows ( "id_subcategory=" . $product_detail ['id_subcategory'], "subcategories" );
			;
		} else {
			$product_detail = array ();
			$subcat = array ();
		}
		$this->view->products_detail = $product_detail;
		$this->view->cat_list = $table->getRows ( "", "categories" );
		$this->view->subcat_list = $subcat;
		$this->view->status = $table->getRows ( "type='Products'", "m8_status" );
		$this->view->supplier_list = $table->getRows ( "", "supplier" );
		$this->view->brand_list = $table->getRows_group ( '', 'products_temp', 'brand', '', array (
				'brand' 
		) );
	}
	function saveproductsAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		// oad model
		$objModel = new Default_Model_Generico ();
		// oad db conexion
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		// et data from url
		$f = new Zend_Filter_StripTags ();
		$id = $f->filter ( $this->_request->getParam ( 'id' ) );
		$last_update = $f->filter ( $this->_request->getParam ( 'last_update' ) );
		$price = $f->filter ( $this->_request->getParam ( 'price' ) );
		$sku = $f->filter ( $this->_request->getParam ( 'sku' ) );
		$id_supplier = $f->filter ( $this->_request->getParam ( 'id_supplier' ) );
		$name = $f->filter ( $this->_request->getParam ( 'name' ) );
		$description = $f->filter ( $this->_request->getParam ( 'description' ) );
		$id_category = $f->filter ( $this->_request->getParam ( 'id_category' ) );
		$id_subcategory = $f->filter ( $this->_request->getParam ( 'id_subcategory' ) );
		$price_nogst = $f->filter ( $this->_request->getParam ( 'price_nogst' ) );
		$rrp = $f->filter ( $this->_request->getParam ( 'rrp' ) );
		$freight_cost = $f->filter ( $this->_request->getParam ( 'freight_cost' ) );
		$status = $f->filter ( $this->_request->getParam ( 'status' ) );
		$brand = $f->filter ( $this->_request->getParam ( 'brand' ) );
		$comments = $f->filter ( $this->_request->getParam ( 'comments' ) );
		$image = $f->filter ( $this->_request->getParam ( 'image' ) );
		$id_product_temo = $f->filter ( $this->_request->getParam ( 'id_product_temo' ) );
		
		$products_table = array (
				'id_supplier' => $id_supplier,
				'id_category' => $id_category,
				'id_subcategory' => $id_subcategory,
				'name' => $name,
				'description' => $description,
				'brand' => $brand,
				'sku' => $sku,
				'price_nogst' => $price_nogst,
				'rrp' => $rrp,
				'price' => $price,
				'freight_cost' => $freight_cost,
				'image' => $image,
				'last_update' => $last_update,
				'status' => $status 
		);
		$products_temp_table = array (
				'last_update' => $last_update 
		);
		
		if ($this->getRequest ()->isPost ()) {
			if (! empty ( $id )) {
				$this->_db->update ( 'products', $products_table, 'id=' . $id );
				$this->_db->update ( 'products_temp', $products_temp_table, 'id=' . $id_product_temo );
			} else {
				$datos = $objModel->getGeneric ( 'sku="' . $sku . '" AND  id_supplier=' . $id_supplier, 'products' );
				if (empty ( $datos )) {
					$this->_db->insert ( 'products', $products_table );
					$id = $this->_db->lastInsertId ();
				} else {
					$this->_db->update ( 'products', $products_table, 'id=' . $id );
					$this->_db->update ( 'products_temp', $products_temp_table, 'id=' . $id_product_temo );
				}
			}
			echo $id;
		} else {
			echo 0;
		}
	} // nd save/update products
}
