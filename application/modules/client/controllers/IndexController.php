<?php
/**
 * Manage client site
 *
 */
class Client_IndexController extends Zend_Controller_Action {
	
	function init() {
		$this->view->assign ( 'baseUrl', $this->getRequest ()->getBaseUrl () );
		$this->initView ();
		$this->_user = App_edvSecurity::getInstance ();
		if (! $this->_user->isLogged ()) {
			$this->_user->gotoLogin ();
		}
		if (! validate ( '2' )) {
			$this->_user->gotoLogin ();
		}
		$this->_userId = $this->_user->userLoggued ()->id;
		$this->_helper->layout->setLayout ( 'layout_client' );
	}
	public function indexAction() {
		$ObjGen 	= new Default_Model_Generico ();
        $auth   	= Zend_Auth::getInstance();
        $user   	= $auth->getIdentity();
        $clientId 	= $user->id_client;
		
		$this->view->programList = $ObjGen->getRows_join2Tables( "client_id=".$clientId, "licenses", "m8_status", "license_types", array("a.*", "b.status as statusName", "c.name as licenceType"), "a.status = b.id_status","a.license_types_id = c.id", "a.date_to");
		$this->view->userDetails = $user;
	}
}
