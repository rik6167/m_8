<?php
/**
 * Management for catalog categories
 *
 */
class Participant_ProgramController extends Zend_Controller_Action {
	
	function init() {
		$this->view->assign ( 'baseUrl', $this->getRequest ()->getBaseUrl () );
		$this->initView ();
		$this->_user = App_edvSecurity::getInstance ();
		
		$this->_userId = $this->_user->userLoggued ()->id;
        $auth = Zend_Auth::getInstance();
        $id = $auth->getIdentity();
                $id_participant = $id['id'];
                $this->view->userinfo = $id;
                $this->view->id_participant = $id_participant;                
                if($id['profile_id']=='3'){                    
                    $ObjGen = new Default_Model_Generico ();
                    $tc = $ObjGen->getRow_select('id_participant='.$id_participant,'program_participants','tc_accepted');
                    $this->view->tc_accepted = $tc;
                }  
		$this->_helper->layout->setLayout ( 'layout_shop' );
	}
	
	public function indexAction() {    
		$ObjGen = new Default_Model_Generico ();
		$idLicence = $_SESSION['licence'];
		$this->view->page = $this->_request->getParam ( "pg" );		
		$program_info = $ObjGen->getRow ( "id_licence='" . $idLicence."'", "licenses" );
		$id_client = $program_info['client_id'];
		$this->view->id_licence = $idLicence;
		$this->view->licence_detail = $program_info;			
			if($program_info['status'] == 8){
				$this->_redirect('participant/program/error/l/'.$idLicence.'/c/'.$id_client);
			}			
		}
	
		public function errorAction() { 
			$this->_helper->layout->setLayout ( 'layout_register' ); 
			$ObjGen 					= new Default_Model_Generico ();
            $licence_id 				= $this->_request->getParam ( "l" );
			$clientId 					= $this->_request->getParam ( "c" );
			$client_info 				= $ObjGen->getRow("id_client='".$clientId."'", "client");
			$programAdmins_info 		= $ObjGen->getRows_join ( "b.id_licence=".$licence_id, "user", "licenses_user" , "a.*","a.id = b.id_user", "a.fullname");
			$this->view->programAdmins_info = $programAdmins_info;	
			$this->view->client     	= $client_info;
            $this->view->licence_info   = $ObjGen->getRow ( "id_licence=" . $licence_id, "licenses" );  
			$this->view->ln 			= $this->_request->getParam ( "ln" );
		}
        
        public function tandcAction(){
        $this->_helper->viewRenderer->setNoRender ( true );
        $this->_helper->layout->disableLayout ();            
        $objModel = new Default_Model_Generico ();
        // signa los datos pasados por la url a variables
        $f = new Zend_Filter_StripTags ();
        $field_value = $f->filter ( $this->_request->getParam ( 'idval' ) );
        $dbtable = $f->filter ( $this->_request->getParam ( 'tabla' ) );
        $fieldname = $f->filter ( $this->_request->getParam ( 'idname' ) );

        
        if (! empty ( $field_value )) {
                        $datos = $objModel->getGeneric ( $fieldname . '=' . $field_value, $dbtable );
                }
                
        if ($this->getRequest ()->isPost ()) {
            
                $this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );
                $form = ($_POST ['fields']);

               $update = $this->_db->update ( $dbtable, array('tc_accepted' => 1), $fieldname .'=' . $field_value );
             
               echo  json_encode($update); // return 1 


               
        } else {
            echo 0;
        }
        
        
        }
	
  public function rewardsAction() {
		$ObjGen = new Default_Model_Generico ();
		$idLicence = $this->_request->getParam ( "l" );
		$this->view->id_licence = $idLicence;
		$this->view->page = 3;
		$this->view->categories_list = $ObjGen->getLista_titles ( "a.id_category IN (SELECT c.id_category FROM subcategories AS c WHERE (SELECT COUNT(d.id_subcategory) FROM products d WHERE d.id_subcategory= c.id_subcategory GROUP BY d.id_subcategory) != '' AND c.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence}) GROUP BY c.id_category)", "categories AS a", array ('a.*',
				'(SELECT COUNT(b.id_category) FROM products b WHERE b.id_category= a.id_category AND b.id_subcategory IN (SELECT id_subcategory FROM program_catalogue WHERE id_licence = '.$idLicence.') GROUP BY b.id_category) AS qty' ), 'a.category ASC' );
		
		$this->view->subcategories_list = $ObjGen->getLista_titles ( "(SELECT COUNT(b.id_subcategory) FROM products b WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) != '' AND
a.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence})", "subcategories AS a", array (
				'a.*',
				'(SELECT COUNT(b.id_subcategory) FROM products b  WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) AS qty' 
		), 'a.subcategory ASC' );
	}
	
	public function shopAction() {
		$id_category = $this->_request->getParam ( "ct" );
		$id_subcategory = $this->_request->getParam ( "sct" );
		$sctname = $this->_request->getParam ( "sctname" );
		$idLicence = $_SESSION['licence'];
		$this->view->id_licence = $idLicence;
		$ObjGen = new Default_Model_Generico ();
		
		if (empty ( $id_subcategory )) {
			$this->view->products_list = $ObjGen->getLista_titles ( "id_category=" . $id_category . " and status='Enabled'", "products", array('*','ROUND((rrp * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.'))) AS points'), 'id_subcategory ASC' );
			$this->view->sc_name = '';
		} else {
			$this->view->products_list = $ObjGen->getLista_titles ( "id_subcategory=" . $id_subcategory . " AND status='Enabled'", "products", array('*','ROUND((rrp * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.'))) AS points'), "" );
			$this->view->sc_name = '/' . $sctname;
		}
		$this->view->category_info = $ObjGen->getRow_select ( "id_category=" . $id_category, "categories", array (
				'a.*',
				'(SELECT COUNT(b.id_category) FROM products b  WHERE b.id_category= a.id_category GROUP BY b.id_category) AS qty' 
		) );
		
		$idLicence = $_SESSION['licence'];
		$this->view->id_licence = $idLicence;
		$this->view->categories_list = $ObjGen->getLista_titles ( "a.id_category IN (SELECT c.id_category FROM subcategories AS c WHERE (SELECT COUNT(d.id_subcategory) FROM products d WHERE d.id_subcategory= c.id_subcategory GROUP BY d.id_subcategory) != '' AND c.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence}) GROUP BY c.id_category)", "categories AS a", array ('a.*',
				'(SELECT COUNT(b.id_category) FROM products b WHERE b.id_category= a.id_category AND b.id_subcategory IN (SELECT id_subcategory FROM program_catalogue WHERE id_licence = '.$idLicence.') GROUP BY b.id_category) AS qty' ), 'a.category ASC' );
		
		$this->view->subcategories_list = $ObjGen->getLista_titles ( "(SELECT COUNT(b.id_subcategory) FROM products b WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) != '' AND
a.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence})", "subcategories AS a", array (
				'a.*',
				'(SELECT COUNT(b.id_subcategory) FROM products b  WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) AS qty' 
		), 'a.subcategory ASC' );
		
		$this->view->search_list = $ObjGen->getLista_titles ( "status='Enabled'", "products", array (
				'id as value',
				'name as label',
				'image' 
		), 'name ASC' );
		
		$this->view->categoryId = $id_category;
		
		$this->view->search_list = $ObjGen->getLista_titles ( "status='Enabled'", "products", array (
				'id as value',
				'name as label',
				'image' 
		), 'name ASC' );
		
	}
	
	public function detailsAction() {
		$this->_helper->layout->disableLayout ();
		$id_product = $this->_request->getParam ( "product" );
		$idLicence 	= $_SESSION['licence'];
		$ObjGen 		= new Default_Model_Generico ();
		$this->_user = App_edvSecurity::getInstance ();		
		$this->_userId = $this->_user->userLoggued ()->id;
        $auth = Zend_Auth::getInstance();
        $user = $auth->getIdentity();
        $id_participant = $user['id'];
		
		$this->view->id_licence = $idLicence;
		$this->view->id_participant = $id_participant;
		$this->view->product_info = $ObjGen->getRow_select ( "id=" . $id_product, "products", array ('a.*','ROUND((a.rrp * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.'))) AS points') );
			$idLicence = $_SESSION['licence'];
		$this->view->id_licence = $idLicence;
		$this->view->categories_list = $ObjGen->getLista_titles ( "a.id_category IN (SELECT c.id_category FROM subcategories AS c WHERE (SELECT COUNT(d.id_subcategory) FROM products d WHERE d.id_subcategory= c.id_subcategory GROUP BY d.id_subcategory) != '' AND c.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence}) GROUP BY c.id_category)", "categories AS a", array ('a.*',
				'(SELECT COUNT(b.id_category) FROM products b WHERE b.id_category= a.id_category AND b.id_subcategory IN (SELECT id_subcategory FROM program_catalogue WHERE id_licence = '.$idLicence.') GROUP BY b.id_category) AS qty' ), 'a.category ASC' );
		
		$this->view->subcategories_list = $ObjGen->getLista_titles ( "(SELECT COUNT(b.id_subcategory) FROM products b WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) != '' AND
a.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence})", "subcategories AS a", array (
				'a.*',
				'(SELECT COUNT(b.id_subcategory) FROM products b  WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) AS qty' 
		), 'a.subcategory ASC' );
		
		$this->view->search_list = $ObjGen->getLista_titles ( "status='Enabled'", "products", array (
				'id as value',
				'name as label',
				'image' 
		), 'name ASC' );
	}
	
	public function participantAction() {
        $this->_helper->layout->disableLayout ();
		$this->_user = App_edvSecurity::getInstance ();		
		$this->_userId = $this->_user->userLoggued ()->id;
        $auth = Zend_Auth::getInstance();
        $id = $auth->getIdentity();
        $id_participant = $id['id'];
		$ObjGen 		= new Default_Model_Generico ();
		$points 		= $ObjGen->getRow_select ( "id_participant=" . $id_participant, "program_participants", array('(SELECT SUM(points) FROM program_points WHERE id_participant = a.id_participant) AS total_points', '(SELECT SUM(points) FROM program_redemtion WHERE id_participant = a.id_participant AND `status` != 11) AS total_spend') );
		$totalpoints =  $points['total_points'] - $points['total_spend'];
		$this->view->totalpoints = $totalpoints ;
			
	}
}
