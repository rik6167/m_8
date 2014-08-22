<?php
class Admin_FullfilmentController extends Zend_Controller_Action {
	
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
		$ObjGen = new Default_Model_Generico ();
		$wish_list 					= $ObjGen->getRows_join2Tables('a.status IN (10, 12, 13)', 'program_redemtion', 'products', 'm8_status', array('a.*','b.name', 'c.status AS status_name', 'b.image'), 'a.id_product = b.id', 'a.status = c.id_status', 'a.id_redemption');
		$this->view->wish_list 		= $wish_list;
	}
        
	
}