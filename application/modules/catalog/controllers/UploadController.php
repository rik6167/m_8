<?php
/**
 * Management for catalog categories
 *
 */
class Catalog_UploadController extends Zend_Controller_Action {
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
		$this->view->supplier_list = $table->getRows ( "", "supplier" );
	}
	public function uploadlistAction() {
		$f = new Zend_Filter_StripTags ();
		$csv = new CSVFile ( $_POST ['fields'] ['file'] );
		$form = ($_POST ['fields'] ['products']);
		$table = 'products_temp';
		$supplier = $_POST ['fields'] ['supplier'];
		$imgSource = $_POST ['fields'] ['imgSource'];
		$qty_save = 0;
		$qty_update = 0;
		$date = date ( 'Y-m-d H:m:s' );
		foreach ( $csv as $line => $values ) {
			if (! empty ( $values )) {
				$data = array (
						'id_supplier' => $supplier,
						'imgSource' => $imgSource,
						'last_update' => $date 
				);
				foreach ( $values as $tittle => $val ) {
					foreach ( $form as $row => $v ) {
						if (! empty ( $v ) and $tittle == $v) {
							$data = array_merge ( $data, array (
									$row => $val 
							) );
						}
					}
				}
				$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
				$select = $this->_db->select ()->from ( $table, 'id' )->where ( 'id_supplier="' . $data ['id_supplier'] . '" AND sku="' . $data ['sku'] . '"' );
				$result = $this->_db->fetchRow ( $select );
				
				if (empty ( $result )) {
					$this->_db->insert ( $table, $data );
					$qty_save = $qty_save + 1;
				} else {
					$this->_db->update ( $table, $data, 'id=' . $result ['id'], $table );
					$qty_update = $qty_update + 1;
				}
			}
		} // end csv file save
		$info = array (
				'save_data' => $date,
				'supplier' => $supplier,
				'qty_save' => $qty_save,
				'qty_update' => $qty_update 
		);
		$objGen = new Default_Model_Generico ();
		$ajax_list = $objGen->getRowsLimit ( "id_supplier=" . $supplier . ' AND last_update="' . $date . '"', $table, $group = 'category', $order = 'category' );
		$this->view->products_list = $ajax_list;
		$this->view->info = $info;
		// echo json_encode($info);
	}
	public function csvuploadAction() {
		$adapter = new Zend_File_Transfer_Adapter_Http ();
		$adapter->setDestination ( '../public/uploads/csv/' );
		$table = new Default_Model_Generico ();
		
		if (! $adapter->receive ()) {
			$messages = $adapter->getMessages ();
			echo implode ( "\n", $messages );
			die ();
		} else {
			$this->view->file_name = $adapter->getFileName ();
			$this->view->supplier_list = $table->getRows ( "", "supplier" );
		}
	}
	
	public function csvproductsAction() {
		$table = new Default_Model_Generico ();
		$this->view->products_list = $table->getRows_join ( "", "products_temp", "supplier", array (
				'a.*',
				'b.supplier' 
		), 'a.id_supplier = b.id_supplier', 'a.category' );
	}
}
