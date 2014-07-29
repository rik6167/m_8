<?php
/**
 * Manage client site
 *
 */
class Client_ProgramController extends Zend_Controller_Action {
	
	public function setupAction() {
		$this->_helper->layout->setLayout ( 'layout_client' );
		$ObjGen 	= new Default_Model_Generico ();
        $auth   	= Zend_Auth::getInstance();
        $user   	= $auth->getIdentity();
        $clientId 	= $user->id_client;
		$id 		= $this->_request->getParam ( "licence" );

		$_SESSION['licence'] = $id;
		
		$this->view->categories_list = $ObjGen->getLista_titles ( "", "categories AS a", array (
				'a.*',
				'(SELECT COUNT(b.id_category) FROM products b  WHERE b.id_category= a.id_category GROUP BY b.id_category) AS qty' 
		), 'a.category ASC' );
		
		$this->view->subcategories_list = $ObjGen->getLista_titles ( "(SELECT COUNT(b.id_subcategory) FROM products b WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) != ''", "subcategories AS a", array (
				'a.*',
				'(SELECT COUNT(b.id_subcategory) FROM products b  WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) AS qty' 
		), 'a.subcategory ASC' );
		
		$this->view->products_list = $ObjGen->getLista_titles ( "status='Enabled'", "products", array ('*'), 'id_subcategory ASC' );		
		$this->view->catalogue_list = $ObjGen->getLista_titles ( "", "program_catalogue AS a", array ('a.*'), '' );		
		$this->view->catalogueCategories_list = $ObjGen->getRows_join("", "program_catalogue", "subcategories", array("a.*","b.id_category"), "a.id_subcategory = b.id_subcategory","");		
		$this->view->licence_detail = $ObjGen->getRow ( "id_licence=" . $id, "licenses" );
		$this->view->userDetails = $user;
		$this->view->participants_list= $ObjGen->getRows ( "id_licence=" . $id, "program_participants" );
	}
	
		public function saverewardsAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		$f 				= new Zend_Filter_StripTags ();
		$form 			= $_POST['subcategory'];
		$idLicence 		= $_POST['fields']['licenses']['id_licence'];
		$idPoints		= $_POST['fields']['licenses']['points'];
		$last_step 		= $_POST['fields']['licenses']['last_step'];		
		$arrayLength 	= count ( $form);		
		$points = array('points'=> $idPoints, 'last_step'=> $last_step);
	
	if ($arrayLength != 0) {		
			$this->_db->delete('program_catalogue', 'id_licence='.$idLicence);		
			foreach ( $form as $row => $values ) {
				$data = array ('id_licence' => $idLicence,'id_subcategory' => $values);		
				$this->_db->insert ( 'program_catalogue', $data );
				$this->_db->update ('licenses', $points, 'id_licence=' . $idLicence);
			}#end for
		}#end if != 0		
		echo 1;				
	}#end funcation save
	
	public function csvparticipantsAction() {
		$adapter = new Zend_File_Transfer_Adapter_Http ();
		$adapter->setDestination ('../public/uploads/csv/');
		$table = 'program_participants';	
		$idLicence = $_POST['fields']['licenses']['id_licence'];
		$last_step = $_POST['fields']['licenses']['last_step'];
		$id_client = $_POST['fields']['licenses']['client_id'];
		if (! $adapter->receive ()) {
			$messages = $adapter->getMessages ();
			echo implode ( "\n", $messages );
			die ();
		} else {
			$csv = new CSVFile ( $adapter->getFileName () );
			foreach ( $csv as $line => $values ) {	
					$pss = md5($values['Password']);
			
					$data = array ('id_licence' 	=> $idLicence, 
									'User_ID' 		=> $values['User_ID'],
									'password' 		=> $pss,
									'first_name' 	=> $values['First_Name'],
									'last_name' 	=> $values['Last_Name'],
									'position'		=> $values['Position'],
									'company_code'	=> $values['Company_Code'],
									'company_name' 	=> $values['Company_Name'],
									'email'			=> $values['Email'],
									'phone' 		=> $values['Phone'],
									'mobile' 		=> $values['Mobile'],
									'address' 		=> $values['Address'],
									'suburb' 		=> $values['Suburb'],
									'state' 		=> $values['State'],
									'postcode' 		=> $values['PostCode'],
									'id_client'		=> $id_client,
					 );					
				
				$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
				$select = $this->_db->select ()->from ( $table, 'id_participant' )->where ( 'id_licence="' . $data ['id_licence'] . '" AND User_ID="' . $data ['User_ID'] . '"' );
				$result = $this->_db->fetchRow ( $select );
				
				if (empty ( $result )) {
					if (!empty ( $values['User_ID'] )) {
						$this->_db->insert ( $table, $data );
						$qty_save = $qty_save + 1;
					}
				} else {
					$this->_db->update ( $table, $data, 'id_participant=' . $result ['id_participant'], $table );
					$qty_update = $qty_update + 1;
				}
				$lastStep = array('last_step'=> $last_step);
				$this->_db->update ('licenses', $lastStep, 'id_licence=' . $idLicence);				
			}/*end foreach csv*/			
		}		
		$info = array ('qty_save' => $qty_save, 'qty_update' => $qty_update ); 
		$this->view->info = $info;
		$this->_redirect('client/program/setup/licence/'.$idLicence);
	}
	
}
