<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class User_ProfileController extends App_ZFDataGridController {
    
    public function indexAction() {	
				$ObjGen = new Default_Model_Generico ();
                $auth   = Zend_Auth::getInstance();
                $user   = $auth->getIdentity();
                $clientId = $user['id_client'];
				$id = $this->_request->getParam ( "uid" );	
				$ly = $this->_request->getParam ( "ly" );
				$this->_helper->layout->setLayout ( 'layout_'.$ly );			
				$this->view->status = $ObjGen->getLista_titles ( "type='Client'", "m8_status", array (
						'id_status',
						'status' 
				), "status" );				
                $this->view->user_detail = $ObjGen->getRow ( "id='". $id."'", "user" );
                $this->view->idClient = $clientId;
	}

    
}
