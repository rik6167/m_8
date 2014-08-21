<?php
/**
 * Management for catalog categories
 *
 */
class Participant_RegisterController extends Zend_Controller_Action {
	
	function init() {
		$this->view->assign ( 'baseUrl', $this->getRequest ()->getBaseUrl () );
		$this->initView ();
		$this->_user = App_edvSecurity::getInstance ();
		if (! $this->_user->isLogged ()) {
			$this->_user->gotoLogin ();
		}
		
		if (! validate ( '2,1,3' )) {
			$this->_user->gotoLogin ();
		}
		
		$this->_userId = $this->_user->userLoggued ()->id;
		$this->_helper->layout->setLayout ( 'layout_shop' );
	}
	
        public function indexAction() {
            
                $this->_helper->layout->setLayout ( 'layout_register' );
                $ObjGen 	= new Default_Model_Generico ();
                $licence_id = $this->_request->getParam ( "licence" );
                $clientId = $this->_request->getParam ( "clientId" );
  
            $this->view->licence_info      = $ObjGen->getRows ( "id_licence=" . $licence_id, "licenses" );
            $this->view->userList   = $ObjGen->getRows_status ( "a.id_client=" . $clientId . " AND b.type='Client'", "user" );
            $this->view->idClient   = $clientId;
            $this->view->idLicence  = $licence_id;
            $this->view->client     = $ObjGen->getRow("id_client='".$clientId."'", "client");
            $this->view->state      = $ObjGen->getLista_titles ( "CountryCode='AUS'", "city", array (
				'DISTINCT(District)' 
		), "District" );   

	}
        
        public function codecheckAction(){
            
            
        $this->_helper->viewRenderer->setNoRender ( true );
        $this->_helper->layout->disableLayout ();
        // arga los modelos
        $objModel = new Default_Model_Generico ();
        // signa los datos pasados por la url a variables
        $f = new Zend_Filter_StripTags ();
        $field_value = $f->filter ( $this->_request->getParam ( 'idval' ) );
        $dbtable = $f->filter ( $this->_request->getParam ( 'tabla' ) );
        $fieldname = $f->filter ( $this->_request->getParam ( 'idname' ) );  
        
        if (! empty ( $field_value )) {
                        $datos = $objModel->getGeneric ( $fieldname . '=' . $field_value, $dbtable );
                }                
        if ($this->getRequest ()->isPost ()) {
                $this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
                $form = ($_POST ['fields']);

                foreach ( $form as $row => $values ) {
                        $table = $row;
                        
                        $invitation_code = $values ['invitation_code'];
                        if (! empty ( $table )) {
                                $select = $this->_db->select ()->from ( $table, 'count(1) total' )->where ( $fieldname . '=?', $field_value );
                                $result = $this->_db->fetchRow ( $select );
                                $ObjGen 	= new Default_Model_Generico ();
                                $licence_info= $ObjGen->getRows ( "id_licence=" . $field_value, "licenses" );                                
                                if($licence_info[0]['invitation_code'] == $values ['invitation_code'])  
                                     echo 1;                           
                                     else echo 0 ;
                        } 
                }               
			} else {
				echo 0;
			}
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
                            
                            # check if email address alrady exist under that licence
                            $email = $objModel->getRows("id_licence= '{$values ['id_licence']}'  AND email= '{$values ['email']}'",$table);
                            $count = count($email);
                            
                            if($count>=1){
                                echo '2'; // return 2 as a ajax call, msg is in the jquery script which is email aready exit
                                die();
                            }

                            # if all good insert the information 
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
                                                "status" => '1',
                                                "tc_accepted" => '1', 
												"registration_page" => '1'           
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
                    # send email to participant here 
                    try{                    
                        $mail = new Zend_Mail();
                        $body = "<p>Welcome message.</p><strong>User</strong>: {$values ['email']}, <br/><strong>Password</strong>:  {$values ['password']}<br/><strong>Program URL</strong>: {$values ['url']}</p>";
                        $mail->setBodyHtml($body)
                             ->setFrom('motive8@ad-inc.com.au','motive8')
                             ->addTo($values ['email'],$values ['first_name'])
                             ->setSubject('Welcome to motive8')
                             ->send();
                    }  catch (Zend_Mail_Transport_Exception $e){
                        $e->getMessage();
                    }   
                        
                }
                
             
                echo '1'; // returning 1 to ajax means registration successful
                die();
                
        } else {
                echo 0;
        }
    } // in guardado/modificacion generico

    
}
