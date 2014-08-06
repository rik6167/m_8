<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class User_UserController extends App_ZFDataGridController {
    
        public function indexAction() {
	
		$ObjGen = new Default_Model_Generico ();
                $auth   = Zend_Auth::getInstance();
                $user   = $auth->getIdentity();
                $clientId = $user['id_client'];
		$this->view->status = $ObjGen->getLista_titles ( "type='Client'", "m8_status", array (
				'id_status',
				'status' 
		), "status" );
                $this->view->userList = $ObjGen->getRows_status ( "a.id_client=" . $clientId . " AND b.type='Client'", "user" );
                $this->view->idClient = $clientId;
                
	}

    
}
