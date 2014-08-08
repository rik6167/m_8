<?php
/**
 * Generic management for master tables
 *
 */
class Admin_GenericoController extends App_ZFDataGridController {
	// eneric save/update function returning id
	function genericoAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		// oad model
		$objModel = new Default_Model_Generico ();
		// et the post params
		$f = new Zend_Filter_StripTags ();
		$id = $f->filter ( $this->_request->getParam ( 'idval' ) );
		$dbtable = $f->filter ( $this->_request->getParam ( 'tabla' ) );
		$dbid = $f->filter ( $this->_request->getParam ( 'idname' ) );
		$listvalues = $f->filter ( $this->_request->getParam ( 'listvalues' ) );
		$pView = str_replace ( '_', '', $dbtable );
		if (! empty ( $id )) {
			$datos = $objModel->getGeneric ( $dbid . '=' . $id, $dbtable );
		}
		if ($this->getRequest ()->isPost ()) {
			$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
			$form = ($_POST ['fields']);
			foreach ( $form as $row => $values ) {
				$table = $row;
				if (! empty ( $table )) {
					$select = $this->_db->select ()->from ( $table, 'count(1) total' )->where ( $dbid . '=?', $id );
					$result = $this->_db->fetchRow ( $select );
					if ($id != '') {
						$this->_db->update ( $table, $values, $dbid . '=' . $id, $dbtable );
						$datos = $objModel->getGeneric ( $dbid . '=' . $id, $dbtable );
					} else {
						$this->_db->insert ( $table, $values );
						$id = $this->_db->lastInsertId ();
						$datos = $objModel->getGeneric ( $dbid . '=' . $id, $dbtable );
					}
				}
			}
			if ($listvalues == 1) {
				$objGen = new Default_Model_Generico ();
				$users_list = $objGen->getRows ( "", $table );
				echo json_encode ( $users_list );
			} else if ($listvalues == 2) {
				$objGen = new Default_Model_Generico ();
				$users_list = $objGen->getRows_join ( "", $table, "categories", array (
						'a.*',
						'b.category' 
				), 'a.id_category = b.id_category' );
				echo json_encode ( $users_list );
			} else {
				echo $id;
			}
		} else {
			echo 0;
		}
	} // nd generic save/update
	  
	// eneric combos rechargable ajax
	public function getlistasAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		$f = new Zend_Filter_StripTags ();
		$listas = new Default_Model_Generico ();
		$pWhere = $f->filter ( $this->_request->getParam ( 'pWhere' ) );
		$pTable = $f->filter ( $this->_request->getParam ( 'pTable' ) );
		$pSelect = $f->filter ( $this->_request->getParam ( 'pSelectid' ) );
		$pSelectname = $f->filter ( $this->_request->getParam ( 'pSelectname' ) );
		$pOrder = $f->filter ( $this->_request->getParam ( 'pOrder' ) );
		$lista = $listas->getLista ( $pWhere, $pTable, array (
				$pSelect,
				$pSelectname 
		), $pOrder );
		// print_r($lista);
		if (false == empty ( $lista )) {
			foreach ( $lista as $row => $value ) {
				$listap [] = array (
						'value' => $row,
						'text' => $value 
				);
			}
			// print_r($listap);
			echo json_encode ( $listap );
		} else {
			echo '{}';
		}
		die ();
	}
	
	public function checkgenericAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $f           	= new Zend_Filter_StripTags();
        $objGen 		= new Default_Model_Generico ();
        $tb   	= $f->filter($this->_request->getParam('tb'));
		$wr   	= $f->filter($this->_request->getParam('wr'));
		if($tb != '' and $wr != ''){
        	$lista        = $objGen->getRow($wr, $tb);
		} else {
			$lista        = array();
		}
	
        if(false == empty($lista)){
            echo '{"msg":"1"}';
        } else {
            echo '{"msg":"0"}';
        }
        die();
    }
}