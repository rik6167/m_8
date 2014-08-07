<?php
class Client_ParticipantController extends Zend_Controller_Action {
    
    
    
        public function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
            parent::__construct($request, $response, $invokeArgs);       
            $this->_helper->layout->setLayout ( 'layout_client' );
        }

        public function indexAction() {
                $ObjGen 	= new Default_Model_Generico ();
                $licence_id = $this->_request->getParam ( "licence" );

                $auth   = Zend_Auth::getInstance();
                $user   = $auth->getIdentity();
                $clientId = $user['id_client'];            
            
            $this->view->participants_list= $ObjGen->getRows ( "id_licence=" . $licence_id, "program_participants" );
            $this->view->licence_name= $ObjGen->getRows ( "id_licence=" . $licence_id, "licenses" );
            $this->view->status = $ObjGen->getLista_titles ( "type='Client'", "m8_status", array (
                    'id_status',
                    'status' 
            ), "status" );
            $this->view->userList = $ObjGen->getRows_status ( "a.id_client=" . $clientId . " AND b.type='Client'", "user" );
            $this->view->idClient = $clientId;

	}
        
        public function editAction() {
            $ObjGen 	= new Default_Model_Generico ();
            $id_participant = $this->_request->getParam ( "id" );
            $this->view->participant= $ObjGen->getRows ( "id_participant=" . $id_participant, "program_participants" );
            echo 1;

	}
        
        function saveAction() {
        $this->_helper->viewRenderer->setNoRender ( true );
        $this->_helper->layout->disableLayout ();
        // arga los modelos
        $objModel = new Default_Model_Generico ();
        // signa los datos pasados por la url a variables
        $f = new Zend_Filter_StripTags ();
        $id = $f->filter ( $this->_request->getParam ( 'idval' ) );
        $dbtable = $f->filter ( $this->_request->getParam ( 'tabla' ) );
        $dbid = $f->filter ( $this->_request->getParam ( 'idname' ) );

        if (! empty ( $id )) {
                $datos = $objModel->getGeneric ( $dbid . '=' . $id, $dbtable );
        }
        if ($this->getRequest ()->isPost ()) {
                $this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
                $form = ($_POST ['fields']);

                $licenses_list = $_POST ['paLicence'];			
                // if email alrady exist return false
                

                foreach ( $form as $row => $values ) {
                        $table = $row;
                        $id_client = $values ['id_client'];
                        if (! empty ( $table )) {
                            
//                            $email =     $objModel->getRows("id_licence=" . $values ['id_licence'] . " AND email=".$values ['email'],$table);

                            /* WE NEED TO PUT A VALIDATION CHECK HERE FOR ANY DUPLICATE EMAIL*/
                         
                            
                            
                                $select = $this->_db->select ()->from ( $table, 'count(1) total' )->where ( $dbid . '=?', $id );
                                $result = $this->_db->fetchRow ( $select );

                                $data = array (
                                                "id_licence" => $values ['id_licence'],
                                                "id_client" => $values ['id_client'],
                                                "User_ID" => $values ['User_ID'],
                                                "first_name" => $values ['first_name'],
                                                "last_name" => $values ['last_name'],                                                                                                
                                                "position" => $values ['position'],                                                                                                
                                                "email" => $values ['email'],
                                                "phone" => $values ['phone'],
                                                "mobile" => $values ['mobile'],
                                                "address" => $values ['address'],
                                                "suburb" => $values ['suburb'],
                                                "company_code" => $values ['company_code'],
                                                "company_name" => $values ['company_name'],
                                                "state" => $values ['state'],
                                                "postcode" => $values ['postcode'],                                                
                                                "status" => $values ['status'],
                                                "tc_accepted" => '1',
                                                
                                                
                                );

                                if (! empty ( $values ['password'] )) {
                                                $data = array_merge ( $data, array ("password" => md5 ( $values ['password'] ) 
                                                ) );
                                        }

                                if ($id != '') {					

                                        $this->_db->update ( $table, $data, $dbid . '=' . $id, $dbtable );
                                        $datos = $objModel->getGeneric ( $dbid . '=' . $id, $dbtable );

                                } else {
                                        $result = array_merge ( $data, array (
                                                        "password" => md5 ( $values ['password'] ) 
                                        ) );
                                        $this->_db->insert ( $table, $data );
                                        $id = $this->_db->lastInsertId ();
                                        $datos = $objModel->getGeneric ( $dbid . '=' . $id, $dbtable );
                                }

                                $this->_db->delete ('licenses_user', 'id_user ='. $id );
                                if( !empty($licenses_list) ){						
                                        foreach($licenses_list as $row){
                                                $licenses_array = array('id_user'=> $id,
                                                                                                'id_licence'=> $row,
                                                                                                'id_client'=> $values ['id_client']);
                                                $this->_db->insert ('licenses_user', $licenses_array );
                                        }
                                }

                        }
                }
                $objGen = new Default_Model_Generico ();
                $users_list = $objGen->getRows_status ( "a.id_client=" . $id_client . " AND b.type='Client' ", $table );
                echo json_encode ( $users_list );
        } else {
                echo 0;
        }
    } // in guardado/modificacion generico
        

}