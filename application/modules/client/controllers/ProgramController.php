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
		$this->view->catalogue_list = $ObjGen->getLista_titles ( "id_licence ='".$id."'", "program_catalogue AS a", array ('a.*'), '' );		
		$this->view->catalogueCategories_list = $ObjGen->getRows_join("a.id_licence ='".$id."'", "program_catalogue", "subcategories", array("a.*","b.id_category"), "a.id_subcategory = b.id_subcategory","");		
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
		$table 				= 'program_participants';	
		$idLicence 			= $_POST['fields']['licenses']['id_licence'];
		$last_step 			= $_POST['fields']['licenses']['last_step'];
		$id_client 			= $_POST['fields']['licenses']['client_id'];
		$registration_page	= $_POST['fields']['licenses']['registration_page'];
		$invitation_code	= $_POST['fields']['licenses']['invitation_code'];
		$registration_msg	= $_POST['fields']['licenses']['registration_msg'];
		$status				= $_POST['fields']['licenses']['status'];
		
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		if ($adapter->receive ()) {
			$csv = new CSVFile ( $adapter->getFileName () );			
			$actual_numparticipants = $_POST['totalparticipants'];
			$file_numparticipants = count($csv);			
			$total = $actual_numparticipants + $file_numparticipants;			
			
			if($total <= 10000){			
				foreach ( $csv as $line => $values ) {				
					if($status == 6){
						$pass_val = $this->randomPassword();
						$pss = md5($pass_val);
					} else {
						$pss = '';	
					}				
					$data = array ('id_licence' 	=> $idLicence, 
									'User_ID' 		=> $values['User_ID'],
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
									'password' 		=> $pss,
									'id_client'		=> $id_client,
					 );				
				
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
			}/*end foreach csv*/
			$save_csv = 1;			
		  } else {
			$save_csv = 0;  
		  }
		}
		$lastStep = array('last_step'=> $last_step, 'registration_page'=> $registration_page, 'invitation_code'=> $invitation_code, 'registration_msg'=> $registration_msg);
		$this->_db->update ('licenses', $lastStep, 'id_licence=' . $idLicence);			
		$info = array ('qty_save' => $qty_save, 'qty_update' => $qty_update,  'csv_responce'=> $save_csv); 
		$this->view->info = $info;
		$this->_redirect('client/program/setup/licence/'.$idLicence, $info);
	}
	
	public function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

	public function sendedmAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		$f 				= new Zend_Filter_StripTags ();
		$welcome_edm 			= $_POST['fields']['licenses']['welcome_edm'];
		$welcome_edm_title		= $_POST['fields']['licenses']['welcome_edm_title'];
		$welcome_edm_from 		= $_POST['fields']['licenses']['welcome_edm_from'];	
		$banner_edm 			= $_POST['fields']['licenses']['banner_edm'];	
		$test_to				= $_POST['test_to'];		
		$to = $test_to;
		$siteurl = $_SESSION['siteurl'];
		$url = $siteurl.'http://test2.motive8dev.adincentives.com.au/public/uploads/banner/'.$banner_edm;
		$edm_banner = $banner_edm != '' ? '<img src="'.$url.'"  />' :'';
		
		// subject
		$subject = $welcome_edm_title;		
		// message
		$message = '
		<html>
		<head>
		  <title>'.$welcome_edm_title.'</title>
		  <style type="text/css">
     		body { font-family: Tahoma; font-size: 15px;  color: #525255; }
     		p { font-size:15px; text-align:justify;	text-justify:inter-word; }     
		</style>
		</head>
		<body>
		<table style="width:700px; border-collapse:collapse;">
		  <tr>
			<td style="width:700px; vertical-align:top;">'.$edm_banner.'</td>
		  </tr>
		  
		  <tr>
			<td ><br/><br/>
				  <p>'.$welcome_edm.'</p>
				  <br/>
				  <strong>URL</strong>: '.$siteurl.'<br/>
				  <strong>User</strong>: username<br/>
				  <strong>Password</strong>: password<br/></td>
		  </tr>
		</table>		  
		</body>
		</html>
		';		
		// To send HTML mail, the Content-type header must be set
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$welcome_edm_from."\r\n";		
		if(mail ( $to, $subject, $message,  $headers )){
			echo 1;
			} else {
			echo 0;
		}				
	}#end funcation save
	
	public function publishAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		$ObjGen 				= new Default_Model_Generico ();
		$auth   				= Zend_Auth::getInstance();
        $user   				= $auth->getIdentity();
		$idLicence 				= $_POST['fields']['licenses']['id_licence'];
		$participantsList 		= $ObjGen->getRows ( "id_licence=" . $idLicence, "program_participants" );	
		$licenses		 		= $ObjGen->getRow ( "id_licence=" . $idLicence, "licenses" );	
		$welcome_edm_title		= $licenses['welcome_edm_title'];
		$welcome_edm_from 		= $licenses['welcome_edm_from'];
		$welcome_edm			= $licenses['welcome_edm'];
		$subdomain		 		= $licenses['subdomain'];
		$siteurl = $_SESSION['siteurl'];	
		if(!empty($participantsList)){	
			foreach ( $participantsList as $row => $values ) {	
				if(!empty($values['email'])){	
					$pass_val 	= $this->randomPassword();
					$pass_md5 = md5($pass_val);
					$to			= $values['email'];					
					$participant_data = array('password' => $pass_md5, 'status' => 1 );
					
					$subject = $welcome_edm_title;		
					$message = '
					<html>
					<head>
					  <title>'.$welcome_edm_title.'</title>
					  <style type="text/css">
						body { font-family: Tahoma; font-size: 15px;  color: #525255; }
						p { font-size:15px; text-align:justify;
						text-justify:inter-word; }     
					</style>
					</head>
					<body>
					  <p>Hi '.$values['first_name'].'</p>
					  <p>'.$welcome_edm.'</p>
					  <br/>
					  <strong>URL</strong>: <a href="http://'.$subdomain.'.'.$siteurl.'" target="_blank">http://'.$subdomain.'.'.$siteurl.'</a><br/>
					  <strong>User</strong>: '.$to.'<br/>
					  <strong>Password</strong>: '.$pass_val .'<br/>		  
					</body>
					</html>
					';		
					// To send HTML mail, the Content-type header must be set
					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: '.$welcome_edm_from."\r\n";		
					if(mail ( $to, $subject, $message,  $headers )){				
						$this->_db->update ('program_participants', $participant_data, 'id_participant="' . $values['id_participant'].'"');
						echo 1;
						} else {
						echo 0;
					}
				 }//end if email address not empty		
			}#end participants foreach	
		}#end if participants empty
		$date = date ( 'Y-m-d H:m:s' );
		$licence_data = array('last_step'=> '7', 'status'=> '6', 'lunch_date'=> $date, 'lunch_by'=> $user['id']);
		$this->_db->update ('licenses', $licence_data, 'id_licence=' . $idLicence);
		
	}#end funcation save
	
	
}
