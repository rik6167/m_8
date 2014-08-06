<?php
/**
 * Management for catalog categories
 *
 */
class Participant_RegisterController extends Zend_Controller_Action {
//	function init() {
//		$this->view->assign ( 'baseUrl', $this->getRequest ()->getBaseUrl () );
//		$this->initView ();
//		$this->_user = App_edvSecurity::getInstance ();
//		
//		$this->_userId = $this->_user->userLoggued ()->id;
//		$this->_helper->layout->setLayout ( 'layout_shop' );
//	}
	
        public function indexAction() {
            
                $this->_helper->layout->setLayout ( 'layout_register' );
                $ObjGen 	= new Default_Model_Generico ();
                $licence_id = $this->_request->getParam ( "licence" );
                $clientId = $this->_request->getParam ( "clientId" );

//                $auth     = Zend_Auth::getInstance();
//                $user     = $auth->getIdentity();
//                $clientId = $user['id_client'];            
            
//            $this->view->participants_list = $ObjGen->getRows ( "id_licence=" . $licence_id, "program_participants" );
            $this->view->licence_info      = $ObjGen->getRows ( "id_licence=" . $licence_id, "licenses" );
//            $this->view->status            = $ObjGen->getLista_titles ( "type='Client'", "m8_status", array (
//                    'id_status',
//                    'status' 
//            ), "status" );
            $this->view->userList = $ObjGen->getRows_status ( "a.id_client=" . $clientId . " AND b.type='Client'", "user" );
            $this->view->idClient = $clientId;
            $this->view->idLicence = $licence_id;

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

}
