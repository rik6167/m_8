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
		$csv_responce	= $this->_request->getParam ( "csv" );
		$file_numparticipants	= $this->_request->getParam ( "fn" );
		$actual_numparticipants	= $this->_request->getParam ( "an" );
		$max_participants		= $this->_request->getParam ( "mx" );
		$new					= $this->_request->getParam ( "new" );
		$old					= $this->_request->getParam ( "old" );
		if(!empty($new) or  !empty($old)){
			$participants_msg = 'You have '.$new.' new participant, we found '.$old.' existing participants from your list.';
		} else {
			$participants_msg = '';
		}
		if($csv_responce == 1){
			$this->view->csv_responce ='You are trying to upload '.$file_numparticipants.' participants, your currently have '.$actual_numparticipants.' of '.$max_participants;
		} else {
			$this->view->csv_responce = '';
		}
				
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
		$this->view->participants_msg = $participants_msg;
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
		$table 					 = 'program_participants';	
		$idLicence 				 = $_POST['fields']['licenses']['id_licence'];
		$last_step 			 	 = $_POST['fields']['licenses']['last_step'];
		$id_client 				 = $_POST['fields']['licenses']['client_id'];
		$registration_page		 = $_POST['fields']['licenses']['registration_page'];
		$invitation_code		 = $_POST['fields']['licenses']['invitation_code'];
		$registration_msg		 = $_POST['fields']['licenses']['registration_msg'];
		$status					 = $_POST['fields']['licenses']['status'];
		$registration_limit_date = $_POST['fields']['licenses']['registration_limit_date'];
		$max_participants 		 = $_POST['fields']['licenses']['max_participants'];	
		$qty_save  =0;	
		$qty_update =0;
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		if ($adapter->receive ()) {
			$csv = new CSVFile ( $adapter->getFileName () );			
			$actual_numparticipants = $_POST['totalparticipants'];
			$file_numparticipants = count(file($adapter->getFileName ()));
			//$fp = file($adapter->getFileName (), FILE_SKIP_EMPTY_LINES);		
			$total = $actual_numparticipants + $file_numparticipants;		
			
			if($total <= $max_participants){			
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
									'email'			=> $values['email'],
									'phone' 		=> $values['Phone'],
									'mobile' 		=> $values['Mobile'],
									'address' 		=> $values['Address'],
									'suburb' 		=> $values['Suburb'],
									'state' 		=> $values['State'],
									'postcode' 		=> $values['PostCode'],									
									'password' 		=> $pss,
									'id_client'		=> $id_client,
					 );				
				
				$select = $this->_db->select ()->from ( $table, 'id_participant' )->where ( 'id_licence="' . $data ['id_licence'] . '" AND email="' . $data ['email'] . '"' );
				$result = $this->_db->fetchRow ( $select );
				
				if (empty ( $result )) {
					if (!empty ( $values['email'] )) {
						$data2 = array_merge($data, array('status' => 9 ));
						$this->_db->insert ( $table, $data2 );
						$qty_save = $qty_save + 1;
					}
				} else {					
					//$this->_db->update ( $table, $data, 'id_participant=' . $result ['id_participant'], $table );
					$qty_update = $qty_update + 1;
				}	
										
			}/*end foreach csv*/
			$save_csv = 0;			
		  } else {
			$save_csv = 1;  
		  }
		  
		}
		$lastStep = array('last_step'=> $last_step, 'registration_page'=> $registration_page, 'invitation_code'=> $invitation_code, 'registration_msg'=> $registration_msg,'registration_limit_date' => $registration_limit_date );
		$this->_db->update ('licenses', $lastStep, 'id_licence=' . $idLicence);			
		$info = array ('fn' => $file_numparticipants, 'an' => $actual_numparticipants,  'csv'=> $save_csv, 'licence' =>$idLicence, 'mx' => $max_participants, 'new' => $qty_save, 'old' => $qty_update); 
		$this->_helper->redirector('setup', 'program', 'client', $info);
		//$this->_redirect('client/program/setup/licence/'.$idLicence.'/rp/'.$save_csv, $info);
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
		$test_to				= $_POST['test_to'];
		$urlprogram				= $_POST['urlprogram'];	
		$id_licence 			= $_POST['fields']['licenses']['id_licence'];	
		$ObjGen 				= new Default_Model_Generico ();	
		$licence_detail 		= $ObjGen->getRow ( "id_licence=" . $id_licence, "licenses" );					
		$welcome_edm 			= $licence_detail['welcome_edm'];
		$welcome_edm_title		= $licence_detail['welcome_edm_title'];
		$welcome_edm_from 		= $licence_detail['welcome_edm_from'];	
		$banner_edm 			= $licence_detail['banner_edm'];		
		$to = $test_to;
		$url = 'http://'.$urlprogram.'/public/uploads/banner/'.$banner_edm;
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
				  <strong>URL</strong>: '.$urlprogram.'<br/>
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
		$date = date ( 'Y-m-d H:m:s' );
		$licence_data = array('last_step'=> '7', 'status'=> '6', 'lunch_date'=> $date, 'lunch_by'=> $user['id']);
		if($this->_db->update ('licenses', $licence_data, 'id_licence=' . $idLicence)){ echo 1; } else { echo 0;}		
	}#end funcation save
	
	public function sendinvAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		$f 						= new Zend_Filter_StripTags ();
		$ObjGen 				= new Default_Model_Generico ();
		$test_to				= $_POST['test_to_inv'];
		$urlprogram				= $_POST['urlprogram'];	
		$id_licence 			= $_POST['fields']['licenses']['id_licence'];		
		$licence_detail 		= $ObjGen->getRow ( "id_licence=" . $id_licence, "licenses" );					
		$invitation_edm_text 	= $licence_detail['invitation_edm_text'];
		$welcome_edm_title		= $licence_detail['invitation_edm_title'];
		$welcome_edm_from 		= $licence_detail['welcome_edm_from'];	
		$banner_edm 			= $licence_detail['banner_edm'];
		$use_banner 			= $licence_detail['use_banner'];				
		
		$to = $test_to;
		$url = 'http://'.$urlprogram.'/public/uploads/banner/'.$banner_edm;
		if($use_banner == 1){
			$edm_banner = $banner_edm != '' ? '<img src="'.$url.'"  />' :'';
		} else {
			$edm_banner = '';
		}
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
				  <p>'.$invitation_edm_text.'</p></td>
		  </tr>
		</table>		  
		</body>
		</html>
		';		
		// To send HTML mail, the Content-type header must be set
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$welcome_edm_from."\r\n";		
		if(mail ( $to, $subject, $message, $headers )){
			echo 1;
			} else {
			echo 0;
		}				
	}#end funcation save
	
	public function managementAction() {
	    $this->_helper->layout->setLayout ( 'layout_client' );
		$ObjGen 	= new Default_Model_Generico ();
        $auth   	= Zend_Auth::getInstance();
        $user   	= $auth->getIdentity();
        $clientId 	= $user['id_client'];
		$IdUser 	= $user['id'];
		$id 		= $this->_request->getParam ( "l" );
		$_SESSION['licence'] = $id;;		
		$this->view->licence_detail = $ObjGen->getRow ( "id_licence=" . $id, "licenses" );
		$this->view->userDetails 	= $user;
	}
	
	public function savestatusAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		$this->_db  = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		$f 			= new Zend_Filter_StripTags ();
		$form 		= $_POST['subcategory'];
		$idLicence 	= $_POST['fields']['licenses']['id_licence'];
		$status 	= $_POST['swt'];
		$data 		= array('status'=> $status);	
		if($this->_db->update ('licenses', $data, 'id_licence=' . $idLicence)){
			echo 1;
		} else {
			echo 0;
		}						
	}#end funcation save
	
	public function edmAction() {
		$this->_helper->layout->setLayout ( 'layout_client' );
		$ObjGen 	= new Default_Model_Generico ();
        $auth   	= Zend_Auth::getInstance();
        $user   	= $auth->getIdentity();
        $clientId 	= $user->id_client;
		$id 		= $this->_request->getParam ( "licence" );
		$saved 		= $this->_request->getParam ( "s" );
		$not_saved 		= $this->_request->getParam ( "n" );
		$numParticipants = $ObjGen->getRows ( "id_licence='".$id."' AND status = 9", "program_participants" );
		$_SESSION['licence'] = $id;	
		$siteurl 				= $_SESSION['siteurl'];	
		$this->view->licence_detail = $ObjGen->getRow ( "id_licence=" . $id, "licenses" );
		$this->view->edm_list		= $ObjGen->getRows_order ( "id_licence=" . $id, "edm_records" , 'launch_date DESC');
		$this->view->userDetails 	= $user;
		$this->view->saved 			= $saved;
		$this->view->not_saved 		= $not_saved;
		$this->view->siteurl 		= $siteurl;	
		$this->view->new_participant = count($numParticipants);			
	}
	
	public function csvedmAction() {
		$adapter = new Zend_File_Transfer_Adapter_Http ();
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		$adapter->setDestination ('../public/uploads/csv/');
		$ObjGen 	= new Default_Model_Generico ();
		$table 					 = 'program_participants';	
		$idLicence 				 = $_POST['fields']['licenses']['id_licence'];		
		$id_client 				 = $_POST['fields']['licenses']['client_id'];
		$total = 0;
		$total_f = -1;		
		$licence_detail 		= $ObjGen->getRow ( "id_licence=" . $idLicence, "licenses" );
		$invitation_edm_text 	= $licence_detail['invitation_edm_text'];
		$welcome_edm_title		= $licence_detail['invitation_edm_title'];
		$welcome_edm_from 		= $licence_detail['welcome_edm_from'];	
		$banner_edm 			= $licence_detail['banner_edm'];
		$use_banner 			= $licence_detail['use_banner'];
		$urlprogram				= $_POST['urlprogram'];	
		$url = 'http://'.$urlprogram.'/public/uploads/banner/'.$banner_edm;
		if($use_banner == 1){
			$edm_banner = $banner_edm != '' ? '<img src="'.$url.'"  />' :'';
		} else {
			$edm_banner = '';
		}
			
		if ($adapter->receive ()) {
			$csv = new CSVFile ( $adapter->getFileName () );	
				foreach ( $csv as $line => $values ) {	
					$to = $values['email'];
					$subject = $welcome_edm_title;		
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
							  <p>'.$invitation_edm_text.'</p></td>
					  </tr>
					</table>		  
					</body>
					</html>
					';		
					// To send HTML mail, the Content-type header must be set
					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: '.$welcome_edm_from."\r\n";		
					if(mail ( $to, $subject, $message, $headers )){
						$total = $total + 1;
						} else {
						$total_f = $total_f + 1;
					}
			
				}/*end foreach csv*/			
			$data = array ('id_licence'=> $idLicence,'launch_date'=> date('d-m-y H:m:s'),'total_send'=> $total, 'edm_type'=>'Invitation EDM');			
			$this->_db->insert ( 'edm_records', $data );		
			} 
		$this->_redirect('client/program/edm/licence/'.$idLicence.'/s/'.$total.'/n/'.$total_f);
	}
	
	public function invitationAction() {
		$this->_helper->viewRenderer->setNoRender ( true );
		$this->_helper->layout->disableLayout ();
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
		$ObjGen 				= new Default_Model_Generico ();
		$auth   				= Zend_Auth::getInstance();
        $user   				= $auth->getIdentity();
		$idLicence 				= $_POST['fields']['licenses']['id_licence'];
		$participantsList 		= $ObjGen->getRows ( "id_licence='".$idLicence."' AND status= 9", "program_participants" );	
		$licenses		 		= $ObjGen->getRow ( "id_licence=" . $idLicence, "licenses" );	
		$welcome_edm_title		= $licenses['welcome_edm_title'];
		$welcome_edm_from 		= $licenses['welcome_edm_from'];
		$welcome_edm			= $licenses['welcome_edm'];
		$subdomain		 		= $licenses['subdomain'];
		$siteurl 				= $_SESSION['siteurl'];
		$banner_edm 			= $licenses['banner_edm'];	
		$url 					= 'http://'.$siteurl.'/public/uploads/banner/'.$banner_edm;
		$edm_banner 			= $banner_edm != '' ? '<img src="'.$url.'"  />' :'';
		$total = 0;
		$total_f = -1;			
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
					<table style="width:700px; border-collapse:collapse;">
					  <tr>
						<td style="width:700px; vertical-align:top;">'.$edm_banner.'</td>
					  </tr>					  
					  <tr>
						<td ><br/><br/>
					  <p>Hi '.$values['first_name'].'</p>
					  <p>'.$welcome_edm.'</p>
					  <br/>
					  <strong>URL</strong>: <a href="http://'.$subdomain.'.'.$siteurl.'" target="_blank">http://'.$subdomain.'.'.$siteurl.'</a><br/>
					  <strong>User</strong>: '.$to.'<br/>
					  <strong>Password</strong>: '.$pass_val .'<br/></td>
					  </tr>
					</table>		  
					</body>
					</html>';		
					// To send HTML mail, the Content-type header must be set
					$headers = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: '.$welcome_edm_from."\r\n";		
					if(mail ( $to, $subject, $message,  $headers )){				
						$this->_db->update ('program_participants', $participant_data, 'id_participant="' . $values['id_participant'].'"');
						$total = $total + 1;
						} else {
						$total_f = $total_f + 1;
					}
				 }//end if email address not empty		
			}#end participants foreach	
		}#end if participants empty
		$data = array ('id_licence'=> $idLicence,'launch_date'=> date('d-m-y H:m:s'),'total_send'=> $total, 'edm_type'=>'Launch EDM');			
		$this->_db->insert ( 'edm_records', $data );
		if($total > 0){
			echo $total;
		} else {
			echo 0;
		}
			
	}#end funcation save
	
	public function reportsAction() {
		$this->_helper->layout->setLayout ( 'layout_client' );
		$ObjGen 	= new Default_Model_Generico ();
                $auth   	= Zend_Auth::getInstance();
                $user   	= $auth->getIdentity();
                $clientId 	= $user->id_client;
		$id 		= $this->_request->getParam ( "licence" );
		$numbyProgram = $ObjGen->getAll ( "id_licence='".$id."' AND status = 1 AND registration_page=0", "program_participants" , array('registration_page'));
		$numRegistration = $ObjGen->getAll ( "id_licence='".$id."' AND status = 1 AND  registration_page=1", "program_participants" , array('registration_page'));
		$numLogins = $ObjGen->getRows_group ( "id_licence='".$id."' AND id_profile = 3", "logsesion" , 'user_id', '', array('user_id'));
		$totalL = count($numLogins);		
		$num_uploaded = count($numbyProgram);
		$num_invited = count($numRegistration);
		$totalP = ($num_uploaded + $num_invited);		
		$total = ($totalP - $totalL);
		
		$this->view->num_notlogin = $total;
		$this->view->num_uploaded = $num_uploaded;
		$this->view->num_invited = $num_invited;
		$this->view->num_logins = $totalL;
		$this->view->num_participant = $totalP;
		$this->view->licence_detail = $ObjGen->getRow ( "id_licence=" . $id, "licenses" );		
	}
        
        public function deleteAction(){
            $this->_helper->layout->setLayout ( 'layout_client' );
            $auth   	= Zend_Auth::getInstance();
            $user   	= $auth->getIdentity();
            $clientId 	= $user['id_client'];
            $this->view->cid = $clientId;
            
            $ObjGen 	= new Default_Model_Generico ();
            $lid 	= $this->_request->getParam ( "licence" );
            $this->view->licence_detail = $ObjGen->getRow ( "id_licence=" . $lid, "licenses" ); 
            $this->view->lid = $lid;
            $numParticipantsTCNotAccepted = $ObjGen->getRows ( "id_licence='".$lid."' AND tc_accepted = 0", "program_participants" );
            
            $this->view->p_tc_notAccepted = count($numParticipantsTCNotAccepted); 
            
            #participants not logged in 
            
            $pWhere  = 'a.id_licence = 2';
            $dbtablea = 'program_participants';
            $dbtableb = 'logsesion';
            $dbselect = 'a.id_participant';
            $conditionb = 'a.id_participant != b.user_id';
            
            $numParticipantsNotloggedIn = $ObjGen->getRows_innerjoin($pWhere, $dbtablea, $dbtableb, $dbselect, $conditionb,'','a.id_participant');
//            print_r($numParticipantsNotloggedIn);
            $this->view->p_not_loggedin = count($numParticipantsNotloggedIn);
            
        }
        
        public function deletetcAction(){
            $this->_helper->viewRenderer->setNoRender ( true );
            $this->_helper->layout->disableLayout ();
            $auth   	= Zend_Auth::getInstance();
            $user   	= $auth->getIdentity();
            $clientId 	= $user['id_client'];
            $lid 	= $this->_request->getParam ( "lcid" ); 
            $cid 	= $this->_request->getParam ( "clid" ); 
            
            $ObjGen 	= new Default_Model_Generico ();
            $numParticipantsTCNotAccepted = $ObjGen->getRows ( "id_licence='".$lid."' AND tc_accepted = 0", "program_participants" );
            $this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
            foreach ($numParticipantsTCNotAccepted as $p){
                 $id = $p['id_participant'];
                 $this->_db->delete('program_participants',"id_participant=$id");
            }
            echo 1;
//            print_r($numParticipantsTCNotAccepted);
            
        }

        public function deletenotloginAction(){
            $this->_helper->viewRenderer->setNoRender ( true );
            $this->_helper->layout->disableLayout ();
            $auth   	= Zend_Auth::getInstance();
            $user   	= $auth->getIdentity();
            $clientId 	= $user['id_client'];
            $lid 	= $this->_request->getParam ( "lcid" ); 
            $cid 	= $this->_request->getParam ( "clid" ); 
            
            $ObjGen 	= new Default_Model_Generico ();

            $pWhere  = 'a.id_licence = 2';
            $dbtablea = 'program_participants';
            $dbtableb = 'logsesion';
            $dbselect = 'a.id_participant';
            $conditionb = 'a.id_participant != b.user_id';
            
            $numParticipantsNotloggedIn = $ObjGen->getRows_innerjoin($pWhere, $dbtablea, $dbtableb, $dbselect, $conditionb,'','a.id_participant');

            $this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
            foreach ($numParticipantsNotloggedIn as $p){
                 $id = $p['id_participant'];                
                // $this->_db->delete('program_participants',"id_participant=$id");
            }
            echo 1;
//            print_r($numParticipantsTCNotAccepted);
            
        }
	
		public function downloadAction()
    {
        $this->_helper->viewRenderer->setNoRender();
		$this->view->layout()->disableLayout();
		$ObjGen 	= new Default_Model_Generico ();
        $id = $this->_request->getParam ( "l" );
		$r = $this->_request->getParam ( "r" );
		
	
		if($r == 1){
			$data = $ObjGen->getRows_status_select ( "id_licence='".$id."' AND a.status = 1 AND registration_page=0", "program_participants", array('a.User_ID','a.first_name','a.last_name','a.position','a.email','b.status', 'a.mobile') );	
			$title_csv = 'Preloaded';			
		} else if($r == 2){
			$data = $ObjGen->getRows_status_select ( "id_licence='".$id."' AND a.status = 1 AND registration_page=1", "program_participants", array('a.User_ID','a.first_name','a.last_name','a.position','a.email','b.status', 'a.mobile') );
			$title_csv = 'Invited';		
		}else if($r == 3){
			$data = $ObjGen->getRows_status_select ( "id_licence='".$id."' AND a.status = 1 ", "program_participants", array('a.User_ID','a.first_name','a.last_name','a.position','a.email','b.status', 'a.mobile') );	
			$title_csv = 'All-Participants';	
		}else if($r == 4){
			$data = $ObjGen->getRows_group ( "id_licence='".$id."' AND id_profile = 3", "logsesion" , 'user_id', '', array('user_id'));	
			$title_csv = 'Not-Login';
		}
		
		$out = "UserID,Name,Surname,Position,email,Status,Mobile\r\n";
		$response = $this->getResponse();		
		$response->setHeader('Content-type', 'application/octet-stream');
		$response->setHeader('Content-Disposition', 'attachment; filename="Report-'.$title_csv.'.csv"');
		foreach($data as $arr) {
			$out .= implode(",", $arr) . "\r\n";		
		}
		echo $out;
    }
	
}
