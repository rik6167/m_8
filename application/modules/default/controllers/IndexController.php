<?php
class IndexController extends Zend_Controller_Action {
	function init() {
		$this->view->assign ( 'baseUrl', $this->getRequest ()->getBaseUrl () );
		$this->initView ();
	}
	public function indexAction() {
		$this->_redirect ( 'login/' );
		// $this->_redirect('forms/index/index/form/1');
	}
}
