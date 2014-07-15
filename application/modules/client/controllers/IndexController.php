<?php
/**
 * Manage client site
 *
 */
class Client_IndexController extends Zend_Controller_Action {
	private $_userId;
	private $_user;
	function init() {
		$this->view->assign ( 'baseUrl', $this->getRequest ()->getBaseUrl () );
		$this->initView ();
		$this->_user = App_edvSecurity::getInstance ();
		if (! $this->_user->isLogged ()) {
			$this->_user->gotoLogin ();
		}
		if (! validate ( 'CLN' )) {
			$this->_user->gotoLogin ();
		}
		$this->_userId = $this->_user->userLoggued ()->id;
		$this->_helper->layout->setLayout ( 'layout_client' );
	}
	public function indexAction() {
		$table = new Default_Model_Generico ();
		$this->view->client = $table->getRows ( "", "program" );
	}
}
