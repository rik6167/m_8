<?php
/*
 * Login Controller
 */

/**
 *
 * @author NatMet
 * @version 09-08-2014
 */
class LoginController extends Zend_Controller_Action {
	
	protected $_bootstrap = null;
	protected $_translate = null;
	
	function init() {
		$this->_bootstrap = $this->getInvokeArg ( "bootstrap" );
		$this->initView ();
		$this->_helper->layout->setLayout ( 'layout_login' );
	}
	
	public function indexAction() {
		$response = $this->getResponse ();
		$form = new Zend_Form ();
		$referer = '';
		$f = new Zend_Filter_StripTags ();
		// validate form if post
		
		if ($this->getRequest ()->isPost ()) {			
			$isajax = false;
			
			if ($this->getRequest ()->isXmlHttpRequest ()) {
				$this->_helper->layout->disableLayout ();
				$isajax = true;
			}
			
			//$input = new Zend_Filter_Input ( $filters, $validators, $this->getRequest ()->getPost () );*/		
			$username = $this->_request->getParam ( "username" );
			$password = $this->_request->getParam ( "password" );
			$client_id = $this->_request->getParam ( "client_id" );
			$id_licence = $this->_request->getParam ( "id_licence" );
			$table = 'user_login';
			$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
			$auth = App_Auth::getInstance ();
			/*$auth->setTable ( 'user' );
			$auth->setCredentialColumn ( 'password' );
			$auth->setIdentityColumn ( 'user' );*/
			
			$select = $this->_db->select ()->from ( $table, '*' )->where ( 'user="'.$username.'" AND password="'.md5($password).'" AND id_client='.$client_id);
			$result = $this->_db->fetchRow ( $select );
			
			if (!empty($result)) {
				/*$session = new Zend_Session_Namespace( "Zend_Auth" );
                $session->setExpirationSeconds( 86400 ); // 24 Horas
                $this->getZendAuth()->getStorage()->write( $row );
				
				$session = new Zend_Session_Namespace ();
				$auth = Zend_Auth::getInstance ();*/
				$user = $result;
				
				$session = new Zend_Session_Namespace( "Zend_Auth" );
                $session->setExpirationSeconds( 86400 ); // 24 Horas
                $this->getZendAuth()->getStorage()->write( $user );
				$_SESSION['siteurl'] = 'motive8dev.adincentives.com.au';
				$objusers = new Default_Model_LogSesion ();
				$data = array (
						'user_id' => $user['id'],
						'id_client' => $client_id,
						'id_licence' => $id_licence,
						'id_profile' => $user['profile_id'],
						'login' => date ( 'Y-m-d H:i:s', time () ),
						'ip_login' => $_SERVER ['REMOTE_ADDR'] 
				);
				$objusers->insert ( $data );
				// heck the profile of the user
			//	$uri = 'login';
				
				if (!empty ( $user['profile_id'] )) {
					$objGen = new Default_Model_Generico ();
					$profile = $objGen->getRow ( "id = ".$user['profile_id'], "profile" );					
					$uri = $profile ['url'];					
				}
				
				if($user['profile_id'] == 3){
					if(empty($id_licence)){
						$uri = 'login';
					}else {
					$_SESSION['licence'] = $id_licence;	
							
					}
				}
				
				
				$this->_redirect ( $uri );

			} else {
				
				if ($isajax) {
					echo "Incorrect username or password";
					exit ();
				} else {
					$this->view->message = "<div class=status>Incorrect username or password</div>";
					return $this->render ();
				}
			}
		}
		//$view = Zend_Layout::getMvcInstance ()->getView ();
	}
	
	public function getZendAuth()
    {
        return Zend_Auth::getInstance();
    }
	
	function checkuserAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
	}
	
	function logoutAction() {
		Zend_Auth::getInstance()->clearIdentity();
		session_destroy ();
		$this->_redirect ( 'login' );
	}
        
                function forgetAction(){
            $this->_helper->layout->setLayout ( 'layout_forget' );
            
        }
        
        function forgetpostAction(){
            
            # this function is bit complicated as it's changing password both for participants and Admin. 
            # first check if based on URL. If it's an admin's url then it is looking only in the user table
            # else it is looking in the participants table
            # and generating random 8 chars string and sending email
            // NEED TO PUT THE ADMIN URL IN THE DATABASE TOO TO MAKE IT DYNAMIC WHICH IS USEFUL FOR STAGING SITES ETC.
            
            
            $this->_helper->viewRenderer->setNoRender ( true );
            $this->_helper->layout->disableLayout ();
            $f = new Zend_Filter_StripTags ();
            $email = $f->filter ( $this->_request->getParam ( 'idval' ) );
            $dbtable = $f->filter ( $this->_request->getParam ( 'tabla' ) );
            $dbfiled = $f->filter ( $this->_request->getParam ( 'idname' ) );
            
            $objModel = new Default_Model_Generico ();
            
            if ($this->getRequest ()->isPost ()) {
                $this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
                
                
                // update table with new password    
                $password = md5($this->rand_string(8));          
                $values = array('password'=>$password);
                
                $domain_name = $_SERVER['HTTP_HOST'];
                $client_id = $_POST ['client_id'];
                #if client id is 1; then its admin
                if($client_id == '1')
                $isadmin = true;                 
                if($isadmin){         
                    $q = $objModel->getRows("id_client= '{$_POST ['client_id']}' AND user= '{$_POST ['email']}'",'user_login');
                    if(count($q)== 1){                        
                        $this->_db->update ( 'user', $values, 'id=' . $q[0]['id'] ); 
                        
                    }  else {
                        echo 0; die();
                    }
    
                  
                }else{

                    #if not admin
                   
//                    print_r($_POST);
                    $q = $objModel->getRows("id_client= '{$_POST ['client_id']}' AND user= '{$_POST ['email']}' AND (id_licence = '0' OR id_licence = '{$_POST ['id_licence']}')",'user_login');
                    
                    # if record doesn't exist for this licence; show error.
                    if(count($q)==0){echo 0;die();}
                    
                    if(!isset($_POST['atype'])){
                       // email address return only one row, that is if licence is not 0 and client id exist

                       # check for paricipant

                       # if email address return 1 row 
                       if(count($q)==1){
                           #if licence number is 0 

                           if($q[0]['id_licence']==0){
                                # then its a client and we need to update user table
                                $this->_db->update ( 'user', $values, 'id' . '=' . $q[0]['id'] );      
                                
                           }
                           else{
                                # else it if licence is not zeto then its particiant and updade participant table
                                $this->_db->update ( 'program_participants', $values, 'id_participant' . '=' . $q[0]['id'] );    
                                
                           }
                        }

                       # if email address return 2 row 
                       if(count($q)==2){
                           #this is client; ask him which password to update. 
                           echo '2';
                           die();

                        }
                    }else{
                        #if a type is participant then update participant else update client 
                        if($_POST['atype']=='participant'){
                            $q = $objModel->getRows("id_client= '{$_POST ['client_id']}' AND user= '{$_POST ['email']}' AND id_licence = '{$_POST ['id_licence']}'",'user_login');                            
                            $this->_db->update ( 'program_participants', $values, 'id_participant' . '=' . $q[0]['id'] );  
                            
                        }else{
                            $q = $objModel->getRows("id_client= '{$_POST ['client_id']}' AND user= '{$_POST ['email']}' AND id_licence = '0'", 'user_login');
                            $this->_db->update ( 'user', $values, 'id' . '=' . $q[0]['id'] );    
                            
                        }
                    }

                }
                
                     #send email with password
                    try{                    
                        $mail = new Zend_Mail();
                        $body = "<p>Hello : {$q [0]['firstname']}, <br/>Your<strong>New Password</strong>is:  {$password}</p><br/><p>You can upadte this by visting your profile at any time.";
                        $mail->setBodyHtml($body)
                             ->setFrom('motive8@ad-inc.com.au','motive8')
                             ->addTo($_POST ['email'],"{$q [0]['firstname']}")
                             ->setSubject('New password for Motive 8')
                             ->send();
                    }  catch (Zend_Mail_Transport_Exception $e){
                        $e->getMessage();
                    }
                    echo 1;
            }
            
           
        }
        
        function rand_string( $length ) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            return substr(str_shuffle($chars),0,$length);
        }
}
