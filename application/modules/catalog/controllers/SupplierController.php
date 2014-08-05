<?php
/**
 * Controlador que maneja las pantallas administrativas
 *
 */
class Catalog_SupplierController extends Zend_Controller_Action {
	private $_userId;
	private $_user;
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
}
