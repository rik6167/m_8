<?php
/**
 * Management for catalog categories
 *
 */
class Catalog_SetupController extends Zend_Controller_Action {
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
		$ignored = $this->_request->getParam ( "ignored" );
		
		$this->view->wash_qty = $table->getRows_group ( "", "products", "a.status", "a.status ASC", array (
				"COUNT(a.id) AS total",
				"a.status" 
		) );
		$this->view->cat_qty = $table->getRow_select ( "", "products", array (
				"COUNT(DISTINCT(id_category)) AS total_cat" 
		) );
		$this->view->sub_qty = $table->getRow_select ( "", "products", array (
				"COUNT(DISTINCT(id_subcategory)) AS total_subcat" 
		) );
		
		$this->view->qty_ignored = $table->getRow_select ( "id_subcategory = 0", "csvcategory_m8category", array (
				"COUNT(id_subcategory) as qty" 
		) );
		$this->view->qty_added = $table->getRow_select ( "id_subcategory <> 0", "csvcategory_m8category", array (
				"COUNT(id_subcategory) as qty" 
		) );
		if (empty ( $ignored )) {
			$this->view->subcat_list = $table->getRows_leftjoin ( "b.csvsubcategory IS NULL", "products_temp", "csvcategory_m8category", array (
					'a.*' 
			), 'b.csvsubcategory = a.subcategory', 'a.category', 'a.subcategory' );
		} else {
			$this->view->subcat_list = $table->getRows_leftjoin ( "b.id_subcategory = 0", "products_temp", "csvcategory_m8category", array (
					'a.*' 
			), 'b.csvsubcategory = a.subcategory', 'a.category', 'a.subcategory' );
		}
		/*
		 * $this->view->subcat_select = $table->getRows_join ( "", "subcategories", "categories", array ( 'a.*', 'b.category' ), 'a.id_category = b.id_category' );
		 */
		$this->view->cat_select = $table->getRows ( "", "categories", array (
				'id_category',
				'category' 
		) );
		
		$this->view->wash_list = $table->getToWashQTY ();
		
	}
	public function savecategoriesAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		$f = new Zend_Filter_StripTags ();
		$form = $_POST;
		$arrayLength = count ( $_POST ['csvcategory'] );
		$qty_save = 0;
		$qty_update = 0;
		$date = date ( 'Y-m-d H:m:s' );
		
		if ($arrayLength != 0) {
			for($i = 0; $i < $arrayLength; $i ++) {
				
				if (! empty ( $_POST ['subcat'] [$i] )) {
					$select = $this->_db->select ()->from ( 'csvcategory_m8category', array (
							'csvcategory',
							'csvsubcategory' 
					) )->where ( 'csvcategory="' . $_POST ['csvcategory'] [$i] . '" AND csvsubcategory="' . $_POST ['csvsubcategory'] [$i] . '"' );
					$result = $this->_db->fetchRow ( $select );
					$data = array (
							'id_subcategory' => $_POST ['id_subcategory'],
							'csvcategory' => $_POST ['csvcategory'] [$i],
							'csvsubcategory' => $_POST ['csvsubcategory'] [$i] 
					);
					if (empty ( $result )) {
						$this->_db->insert ( 'csvcategory_m8category', $data );
					} else {
						$this->_db->update ( 'csvcategory_m8category', $data, 'csvcategory="' . $_POST ['csvcategory'] [$i] . '" AND csvsubcategory="' . $_POST ['csvsubcategory'] [$i] . '"' );
					} // nd of insert/update
				} // nd if subcat selected
			} // nd for
		} // nd if != 0
		echo 1;
	} // nd funcation save
	public function washupAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		$f = new Zend_Filter_StripTags ();
		$form = $_POST;
		$qty_save = 0;
		$qty_update = 0;
		$date = date ( 'Y-m-d H:m:s' );
		$table = new Default_Model_Generico ();
		$products = $table->getToWash ();
		if (! empty ( $products )) {
			foreach ( $products as $rows ) {
				$select_prod = $this->_db->select ()->from ( 'products', 'id' )->where ( 'sku="' . $rows ['sku'] . '"' );
				$result_prod = $this->_db->fetchRow ( $select_prod );
				$sku = $rows ['sku'];
				
				if (! empty ( $rows ['image'] )) {
					$DfImage = 'defaul.jpg';
					if ($rows ['imgSource'] == 2) {
						$ruteimage = $this->baseUrl () . '/uploads/catalog/' . $sl ['sku'] . '.jpg';
						if (@getimagesize ( $ruteimage ) !== false) {
							$file = new SplFileInfo ( $ruteimage );
							$ext = $file->getExtension ();
							$image = md5 ( $sku ) . '.' . $ext;
							$delImg = 1;
						} else {
							$image = $DfImage;
						}
					} else if ($rows ['imgSource'] == 1) {
						$ruteimage = $rows ['image'];
						if (@getimagesize ( $ruteimage ) !== false) {
							$file = new SplFileInfo ( $rows ['image'] );
							$ext = $file->getExtension ();
							$image = md5 ( $sku ) . '.' . $ext;
							$delImg = 0;
						} else {
							$image = $DfImage;
							$delImg = 0;
						}
					} else {
						$image = $DfImage;
						$delImg = 0;
					}
				} else {
					$ruteimage = '';
					$image = 'defaul.jpg';
					$delImg = 0;
				}
				$toProducts = array ('id_product_temo' => $rows ['id'],
									'sku' => $rows ['sku'],
									'image' => $image,
									'name' => $rows ['name'],
									'description' => $rows ['description'],
									'brand' => $rows ['brand'],
									'rrp' => $rows ['rrp'],
									'price' => $rows ['price'],
									'price_nogst' => $rows ['price_nogst'],
									'freight_cost' => $rows ['freight_cost'],
									'status' => $rows ['status'],
									'id_supplier' => $rows ['id_supplier'],
									'last_update' => $rows ['last_update_csv'],
									'id_category' => $rows ['id_category'],
									'id_subcategory' => $rows ['id_subcategory'] );
				if (empty ( $result_prod )) {
					$this->_db->insert ( 'products', $toProducts );
					if (! empty ( $ruteimage )) {
						$csv = new IMGupload ( $ruteimage, md5 ( $sku ) );
						if ($delImg == 1) {
							unlink ( $ruteimage );
						}
					}
					
					$qty_save = $qty_save + 1;
				} else {
					$this->_db->update ( 'products', $toProducts, 'id=' . $result_prod ['id'] );
					$qty_update = $qty_update + 1;
				} // nd of insert/update
			} // nd foreach
		} // nd of if not empty
		echo 1;
		$info = array (
				'qty_update' => $qty_update,
				'qty_save' => $qty_save 
		);
	} // nd funcation save
}
