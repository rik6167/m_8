<?php
class Admin_FullfilmentController extends Zend_Controller_Action {
	
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
	
    public function indexAction() {
		$ObjGen = new Default_Model_Generico ();		
		$wish_list 	= $ObjGen->get_fulfilment('a.status IN (13, 10)');
		$supplier_list 				= $ObjGen->get_fulfilment_list('a.status IN (13, 10)', array('DISTINCT(d.id_supplier)', 'd.supplier' ));
		$this->view->wish_list 		= $wish_list;
		$this->view->supplier_list 	= $supplier_list;
	}
      
	public function addinfoAction() {
		$this->_helper->layout->disableLayout ();
		$id		= $this->_request->getParam ( "id" );
		$ObjGen = new Default_Model_Generico ();		
		$fullfilment_list 	= $ObjGen->get_fulfilment('id_redemption='.$id);
		$this->view->fullfilment_list 	= $fullfilment_list;
		$this->view->status 	= $ObjGen->getLista_titles ( "type='Order' and id_status IN (12, 13, 14, 10)", "m8_status", array (
				'id_status',
				'status'), "status" );		
	} 
	
		public function downloadAction()
    {
		$this->view->layout()->disableLayout();
		$ObjGen 		= new Default_Model_Generico ();
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
        $supplier		= $this->_request->getParam ( "s" );
		$status			= $this->_request->getParam ( "status" );
		$change_status	= $this->_request->getParam ( "change_status" );				
		if($supplier == 'All'){
			$where = 'a.status = '.$status;
			$supplier_name		= 'ALL';			
		} else {
			$where = 'a.status = '.$status.' AND d.id_supplier='.$supplier;
			$supplier_name		= $this->_request->getParam ( "sn" );
		}	
		$wish_list = $ObjGen->get_fulfilment($where);		
		if($change_status == 1 and $status	== 10){			
			foreach ($wish_list as $row){
				$values =  array('new_status_date' => date('d-m-Y'), 'status' => 13);
				$this->_db->update ( 'program_redemtion', $values, 'id_redemption=' . $row['id_redemption']);			
			}
		}
		
		$this->view->wish_list 	= $wish_list;
		$this->view->supplier_name 	= $supplier_name;
    } 
	
	public function noinvoicedAction(){
		$ObjGen = new Default_Model_Generico ();		
		$supplier_list 	= $ObjGen->get_fulfilment_list("a.status = 12 AND invoice_c IS NULL or invoice_c = '' ", array('DISTINCT(e.id_licence)', 'e.name as licencename' ));	
		$wish_list 	= $ObjGen->get_fulfilment("a.status = 12 AND IFNULL(invoice_c, '') = ''  ");
		$this->view->wish_list2 	= $wish_list;
		$this->view->supplier_list2 	= $supplier_list;	
	}
	
	public function saveinvoiceAction(){
		$this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
		$this->_db  = Zend_Controller_Front::getInstance()->getParam( "bootstrap" )->getResource( "db" );
		$id_redemption  = ($_POST['id_redemption']);
		$invoice_c     	= ($_POST['invoice_c']);				
	    foreach($id_redemption as $row){			
			if($_POST['invoice_c'][$row] != ''){
				$values = array('invoice_c'=> $_POST['invoice_c'][$row], 'new_status_date'=> date('d-m-Y') );
            	$this->_db->update('program_redemtion', $values  , 'id_redemption=' . $row);
			}
        }
		echo 1;	
	}
	
	public function download2Action()
    {
		$this->view->layout()->disableLayout();
		$ObjGen 		= new Default_Model_Generico ();
        $program		= $this->_request->getParam ( "s" );
		$program_name	= $this->_request->getParam ( "sn" );	
		$wish_list 	= $ObjGen->get_fulfilment("a.status = 12 AND IFNULL(invoice_c, '') = '' AND e.id_licence =".$program);		
		$this->view->wish_list 	= $wish_list;
		$this->view->program_name 	= $program_name;
    } 
	
		public function invoicedAction(){
		$ObjGen = new Default_Model_Generico ();			
		$supplier_list 	= $ObjGen->get_fulfilment_list("a.status = 12 AND invoice_c != ''", array('DISTINCT(e.id_licence)', 'e.name as licencename' ));	
		$wish_list 	= $ObjGen->get_fulfilment("a.status = 12 AND invoice_c != '' ");
		$this->view->wish_list 	= $wish_list;
		$this->view->supplier_list 	= $supplier_list;	
	}
	
		public function download3Action()
    {
		$this->view->layout()->disableLayout();
		$ObjGen 		= new Default_Model_Generico ();
        $program		= $this->_request->getParam ( "s" );
		$program_name	= $this->_request->getParam ( "sn" );	
		$wish_list 	= $ObjGen->get_fulfilment("a.status = 12 AND invoice_c != '' AND e.id_licence =".$program);
		$this->view->wish_list 	= $wish_list;
		$this->view->program_name 	= $program_name;
    } 
	
	
	
}