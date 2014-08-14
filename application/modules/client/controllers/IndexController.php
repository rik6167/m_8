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
        $clientId 	= $user['id_client'];
		$IdUser 	= $user['id'];
		$myprograms =  $ObjGen->getRows_join2Tables( "a.client_id='".$clientId. "' AND a.id_licence IN (SELECT id_licence FROM licenses_user WHERE id_user='".$IdUser. "' AND id_client = '".$clientId. "')", "licenses", "m8_status", "license_types", array("a.*", "b.status as statusName", "c.name as licenceType"), "a.status = b.id_status","a.license_types_id = c.id", "a.date_to");
		$total_programs = count($myprograms);
		//print_r($myprograms);
		$this->view->programList = $myprograms;
		$this->view->userDetails = $user;
		if($total_programs == 1){		
			if($myprograms [0]['status'] == 5){
				$this->_redirect('client/program/setup/licence/'.$myprograms [0]['id_licence']);
			} else {
				$this->_redirect('client/program/management/l/'.$myprograms [0]['id_licence']);				
			}
		}
		
	}
}
