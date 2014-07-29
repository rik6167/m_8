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
}
