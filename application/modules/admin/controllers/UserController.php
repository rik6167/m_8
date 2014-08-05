<?php
/**
 * Generic management for master tables
 *
 */
class Admin_UserController extends App_ZFDataGridController {
	
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
			
			
			foreach ( $form as $row => $values ) {
				$table = $row;
				$id_client = $values ['id_client'];
				if (! empty ( $table )) {
					$select = $this->_db->select ()->from ( $table, 'count(1) total' )->where ( $dbid . '=?', $id );
					$result = $this->_db->fetchRow ( $select );
					
					$data = array (
							"id" => $values ['id'],
							"fullname" => $values ['fullname'],
							"user" => $values ['user'],
							"profile_id" => $values ['profile_id'],
							"email" => $values ['email'],
							"phone" => $values ['phone'],
							"position" => $values ['position'],
							"status" => $values ['status'],
							"updated" => $values ['updated'],
							"id_client" => $values ['id_client'] 
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
	
	public function getlicensesAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		$f = new Zend_Filter_StripTags ();
		$objGen = new Default_Model_Generico ();
		$iduser = $f->filter ( $this->_request->getParam ( 'iduser' ) );
		$client = $f->filter ( $this->_request->getParam ( 'client' ) );		

		$lista = $objGen->getAll ( "a.client_id = '".$client."'", "licenses",array("IF( (SELECT b.id_licence FROM licenses_user b WHERE b.id_user = '".$iduser."' AND b.id_client = a.client_id AND b.id_licence =a.id_licence) = a.id_licence , 'selected' , ''  )  AS slted", "a.id_licence", "a.client_id", "name") );

		if (false == empty ( $lista )) {
			echo json_encode ( $lista );
		} else {
			echo '{}';
		}
		die ();
	}
	
 	public function checkjsonAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $f           	= new Zend_Filter_StripTags();
        $objGen 		= new Default_Model_Generico ();
        $idClient   	= $f->filter($this->_request->getParam('idClient'));
		$user   		= $f->filter($this->_request->getParam('user'));
		$idUser 		= $f->filter ( $this->_request->getParam ( 'idUser' ) );		
		if($user != '' and $idClient != ''){
        	$lista        = $objGen->getRow("id_client = '{$idClient}' AND user ='{$user}'  AND id <> '{$idUser}'", 'user');
		} else {
			$lista        = array();
		}
        //pprint_r($lista);
        if(false == empty($lista)){
            echo '{"msg":"1"}';
        } else {
            echo '{"msg":"0"}';
        }
        die();
    }
	
}