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
		$pView = str_replace ( '_', '', $dbtable );
		if (! empty ( $id )) {
			$datos = $objModel->getGeneric ( $dbid . '=' . $id, $dbtable );
		}
		if ($this->getRequest ()->isPost ()) {
			$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
			$form = ($_POST ['fields']);
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
					
					if ($id != '') {
						if (! empty ( $values ['password'] )) {
							$data = array_merge ( $data, array (
									"password" => md5 ( $values ['password'] ) 
							) );
						}
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