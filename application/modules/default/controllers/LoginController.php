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
		// alidate form if post
		if ($this->getRequest ()->isPost ()) {
			
			$isajax = false;
			if ($this->getRequest ()->isXmlHttpRequest ()) {
				$this->_helper->layout->disableLayout ();
				$isajax = true;
			}
			
			$filters = array (
					"*" => "StringTrim" 
			);
			$validators = array (
					"username" => array (
							"NotEmpty" 
					),
					"password" => array (
							"NotEmpty" 
					) 
			);
			
			$input = new Zend_Filter_Input ( $filters, $validators, $this->getRequest ()->getPost () );
			if (false === $input->isValid ()) {
				if ($isajax) {
					echo "Incorrect username or password";
					exit ();
				} else {
					$this->view->message = "<div class=status>Incorrect username or password</div>";
					return $this->render ();
				}
			}
			
			$auth = App_Auth::getInstance ();
			$auth->setTable ( 'user' );
			$auth->setCredentialColumn ( 'password' );
			$auth->setIdentityColumn ( 'user' );
			
			if (false !== $auth->authenticate ( $input->username, $input->password, array () )) {
				$session = new Zend_Session_Namespace ();
				$auth = Zend_Auth::getInstance ();
				$user = $auth->getIdentity ();
				$objusers = new Default_Model_LogSesion ();
				$data = array (
						'usuario_id' => $user->id,
						'fecha_ini' => date ( 'Y-m-d H:i:s', time () ),
						'ip_ingreso' => $_SERVER ['REMOTE_ADDR'] 
				);
				$objusers->insert ( $data );
				// heck the profile of the user
				$uri = 'forms/';
				if (false == empty ( $user->profile_id )) {
					$objGen = new Default_Model_Generico ();
					$profile = $objGen->getRow ( "alias = '{$user->profile_id}'", "profile" );
					$uri = $profile ['url'];
				}
				
				if (false == empty ( $session->next )) {
					echo "<script>document.location.href ='$session->next'; </script>";
				} else {
					
					if ($isajax) {
						echo "
                                              <i>Redireccionando..</i>
                                              <script>
                                                     // top.location = '" . Zend_Registry::get ( 'siteurl' ) . '/' . $uri . "';                                                    
                                               </script>";
						exit ();
					} else {
						$this->_redirect ( $uri );
					}
				}
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
		$view = Zend_Layout::getMvcInstance ()->getView ();
	}
	function checkuserAction() {
		$this->_helper->layout->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
	}
	function logoutAction() {
		Zend_Auth::getInstance ()->clearIdentity ();
		session_destroy ();
		$this->_redirect ( 'login' );
	}
}
