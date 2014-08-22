<?php
/**
 * Licence
 *
 */
class Admin_LicenceController extends Zend_Controller_Action {
	
		function init() {
		$this->view->assign ( 'baseUrl', $this->getRequest ()->getBaseUrl () );
		$this->initView ();
		$this->_user = App_edvSecurity::getInstance ();
		
		if (! $this->_user->isLogged ()) {
			$this->_user->gotoLogin ();
		}
		if (! validate ( '1' )) {
			$this->_user->gotoLogin ();
		}
		$this->_userId = $this->_user->userLoggued ()->id;
		$this->_helper->layout->setLayout ( 'layout_admin' );
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
		$pView = str_replace ( '_', '', $dbtable );
		
		if (! empty ( $id )) {
			$datos = $objModel->getGeneric ( $dbid . '=' . $id, $dbtable );
		}
		
		if ($this->getRequest ()->isPost ()) {
			$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
			$form = ($_POST ['fields']);
			foreach ( $form as $row => $values ) {
				$table = $row;
				$id_client = $values ['client_id'];
				if (! empty ( $table )) {
					$select = $this->_db->select ()->from ( $table, 'count(1) total' )->where ( $dbid . '=?', $id );
					$result = $this->_db->fetchRow ( $select );
					
					if ($id != '') {
						$this->_db->update ( $table, $values, $dbid . '=' . $id, $dbtable );
						// $datos = $objModel->getGeneric( $dbid.'=' . $id, $dbtable);
					} else {
						$this->_db->insert ( $table, $values );
						$id = $this->_db->lastInsertId ();
						// $datos = $objModel->getGeneric($dbid.'=' . $id, $dbtable);
					}
				}
			}
			
			$users_list = $objModel->getRows_status ( "client_id=" . $id_client, $table );
			echo json_encode ( $users_list );
		} else {
			echo 0;
		}
	} // in guardado/modificacion generico
}