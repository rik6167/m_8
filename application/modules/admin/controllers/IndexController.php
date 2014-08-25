<?php
/**
 * Controlador que maneja las pantallas administrativas
 *
 */
class Admin_IndexController extends Zend_Controller_Action {
	private $_userId;
	private $_user;
	
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
		$table = new Default_Model_Generico ();
		
		$this->view->licencesList = $table->getRows_status_select ( "", "licenses", 
                        array(
                            'a.id_licence',
                            'a.name',
                            'a.date_from',
                            'a.points',
                            'a.date_to',
                            'a.subdomain',
                            'b.status AS status_name',
                            'b.id_status',
                            '(SELECT SUM(points) AS total FROM program_points WHERE id_licence=a.id_licence ) AS totalAllocated',
                            '(SELECT SUM(points * qty) AS total FROM program_redemtion WHERE id_licence=a.id_licence ) AS totalRedemption',
                            '(SELECT NAME AS n FROM `license_types` WHERE id=a.`license_types_id` ) AS licenceType',
                            '(SELECT `client` AS cl FROM `client` WHERE id_client=a.`client_id` ) AS clientNAme'
                            
                             ) 
                        );
                
       
	}
}
