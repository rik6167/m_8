<?php
class Client_PointsController extends Zend_Controller_Action {
	
	function init() {
		$this->_helper->layout->setLayout ( 'layout_client' );
	}
    
        public function indexAction() {
                $ObjGen 	= new Default_Model_Generico ();
                $licence_id = $this->_request->getParam ( "licence" );
				$new_points = $this->_request->getParam ( "ud" );
                $auth   = Zend_Auth::getInstance();
                $user   = $auth->getIdentity();
                $clientId = $user['id_client'];            
            	$paticipants = $ObjGen->getRows_status_select ( "id_licence=" . $licence_id, "program_participants", array('a.*','b.status as status_name','(SELECT SUM(points) FROM program_points WHERE id_participant = a.id_participant) AS total_points', '(SELECT SUM(points) FROM program_redemtion WHERE id_participant = a.id_participant AND `status` IN  (10, 12, 13) ) AS total_spend') );
				$this->view->participants_list	= $paticipants; 
				$this->view->licence_name 		= $ObjGen->getRow ( "id_licence=" . $licence_id, "licenses" );
				$this->view->idClient = $clientId;
				$this->view->idUser = $user['id'];
				$this->view->new_points = $new_points;

	}
	

	public function bulkAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$this->view->layout()->disableLayout();
		$ObjGen 	= new Default_Model_Generico ();
        $licence_id = $this->_request->getParam ( "l" );
		$paticipants = $ObjGen->getRows_status_select ( "id_licence=" . $licence_id, "program_participants", array('a.id_participant','a.User_ID','a.first_name','a.last_name','a.position','a.email','b.status') );
		$response = $this->getResponse();		
		$response->setHeader('Content-type', 'application/octet-stream');
		$response->setHeader('Content-Disposition', 'attachment; filename="Participants.csv"');
		$out = "Code,UserID,Name,Surname,Position,email,Status,Points,Comments\r\n";
		foreach($paticipants as $arr) {
			$out .= implode(",", $arr) . "\r\n";		
		}
		echo $out;
    }
	
	public function csvpointsAction() {
		$adapter = new Zend_File_Transfer_Adapter_Http ();
		$adapter->setDestination ('../public/uploads/csv/');
		$idLicence 		= $_POST['fields']['program_points']['id_licence'];
		$issue_date 	= $_POST['fields']['program_points']['issue_date'];
		$id_user 		= $_POST['fields']['program_points']['id_user'];	
		$new_points 	= 0;
		$qty_not 		= 0;	
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		if ($adapter->receive ()) {
			$csv = new CSVFile ( $adapter->getFileName () );		
				foreach ( $csv as $line => $values ) {			
					$data = array ('id_licence' 		=> $idLicence, 
									'id_user'			=> $id_user,
									'issue_date'		=> $issue_date,
									'id_participant' 	=> $values['Code'],											
									'points'			=> $values['Points'],
									'comments' 			=> $values['Comments']);				
				
				if (!empty ( $values['Code'] )) {
					$this->_db->insert ( 'program_points', $data );
					$new_points = $new_points + 1;
				} else {					
					$qty_not = $qty_not + 1;
				}							
			}/*end foreach csv*/		  
		}
		
		$info = array ('ud' => $new_points, 'nud' => $qty_save, 'licence' =>$idLicence); 
		$this->_helper->redirector('index', 'points', 'client', $info);
	}
	

	
	
        
 }