<?php
class Participant_ParticipantController extends Zend_Controller_Action {
    
        public function editAction() {            
            $ObjGen 	= new Default_Model_Generico ();
            $auth   = Zend_Auth::getInstance();
            $user   = $auth->getIdentity();
            $id_participant = $user['id'];
			$idLicence = $this->_request->getParam ( "l" );
			$this->view->page = 1;
			$this->view->id_licence = $idLicence;
           	$this->view->licence_detail = $ObjGen->getRow ( "id_licence='" . $idLicence."'", "licenses" ); 
          	$this->view->participant= $ObjGen->getRow ( "id_participant=" . $id_participant, "program_participants" );
           	$this->view->state      = $ObjGen->getLista_titles ( "CountryCode='AUS'", "city", array (
                    'DISTINCT(District)' 
            ), "District" );  
			$this->_helper->layout->setLayout ( 'layout_shop' );

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

                                $select = $this->_db->select ()->from ( $table, 'count(1) total' )->where ( $dbid . '=?', $id );
                                $result = $this->_db->fetchRow ( $select );

                                $data = array (

                                                "first_name" => $values ['first_name'],
                                                "last_name" => $values ['last_name'],                                                                                                
                                                "position" => $values ['position'],                                                                                                
                                                "phone" => $values ['phone'],
                                                "mobile" => $values ['mobile'],
                                                "address" => $values ['address'],
                                                "suburb" => $values ['suburb'],
                                                "company_name" => $values ['company_name'],
                                                "state" => $values ['state'],
                                                "postcode" => $values ['postcode'],                                                
              
                                );

                                if (! empty ( $values ['password'] )) {
                                                $data = array_merge ( $data, array ("password" => md5 ( $values ['password'] ) 
                                                ) );
                                        }

                                if ($id != '') {					

                                        $this->_db->update ( $table, $data, $dbid . '=' . $id, $dbtable );
                                        $datos = $objModel->getGeneric ( $dbid . '=' . $id, $dbtable );

                                } 

                        }
                }
                echo 1;
        } else {
                echo 0;
        }
    } // in guardado/modificacion generico
        

}