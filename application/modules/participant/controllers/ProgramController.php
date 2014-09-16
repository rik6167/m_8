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
		
		if (! validate ( '2,1,3' )) {
			$this->_user->gotoLogin ();
		}
		
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
		$this->view->page = $this->_request->getParam ( "pg" );	
		$idLicence = $_SESSION['licence'];
		if(empty($idLicence)){
			$idLicence = $this->_request->getParam ( "l" );
		} 		
			
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
		$id_category 		= $this->_request->getParam ( "ct" );
		$id_subcategory 	= $this->_request->getParam ( "sct" );
		$sctname 			= $this->_request->getParam ( "sctname" );
		$idLicence = $this->_request->getParam ( "l" );

		$ObjGen 			= new Default_Model_Generico ();
		$program_info 		= $ObjGen->getRow ( "id_licence='" . $idLicence."'", "licenses" );
		$porcent 			= $program_info['porcentage_rewards'] != '' ? $program_info['porcentage_rewards'] : 10 ;
		$porcentageCatalogue = 100 + $porcent;
		
		if (empty ( $id_subcategory )) {
			$where = "a.id_category=" . $id_category . " and status='Enabled'";
			$this->view->sc_name = '';
		} else {
			$where = "a.id_subcategory=" . $id_subcategory . " AND status='Enabled'";
			$this->view->sc_name = '/' . $sctname;
		}
		
		$this->view->products_list = $ObjGen->getRows_join ( $where , "products", 'subcategories', array('a.*','b.gst',	
			'CASE WHEN b.gst = 10 
				THEN (((price * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.')) ) / 100) * '.$porcentageCatalogue.' 
				ELSE (price * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.'))
			END  AS points',
			
			'((freight_cost / 100 ) * 110) * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.')  AS freight_points'), 'b.id_subcategory = a.id_subcategory','price_nogst ASC' );
		
		$this->view->category_info = $ObjGen->getRow_select ( "id_category=" . $id_category, "categories", array (
				'a.*',
				'(SELECT COUNT(b.id_category) FROM products b  WHERE b.id_category= a.id_category GROUP BY b.id_category) AS qty' 
		) );
		
		$this->view->id_licence = $idLicence;
		$this->view->categories_list = $ObjGen->getLista_titles ( "a.id_category IN (SELECT c.id_category FROM subcategories AS c WHERE (SELECT COUNT(d.id_subcategory) FROM products d WHERE d.id_subcategory= c.id_subcategory GROUP BY d.id_subcategory) != '' AND c.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence}) GROUP BY c.id_category)", "categories AS a", array ('a.*',
				'(SELECT COUNT(b.id_category) FROM products b WHERE b.id_category= a.id_category AND b.id_subcategory IN (SELECT id_subcategory FROM program_catalogue WHERE id_licence = '.$idLicence.') GROUP BY b.id_category) AS qty' ), 'a.category ASC' );
		
		$this->view->subcategories_list = $ObjGen->getLista_titles ( "(SELECT COUNT(b.id_subcategory) FROM products b WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) != '' AND a.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence})", "subcategories AS a", array (	'a.*',
				'(SELECT COUNT(b.id_subcategory) FROM products b  WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) AS qty' 
		), 'a.subcategory ASC' );
		
		$this->view->search_list = $ObjGen->getLista_titles ( "status='Enabled'", "products", array (
				'id as value',
				'name as label',
				'image' 
		), 'name ASC' );
		
		$this->view->categoryId = $id_category;
		$this->view->program_info = $program_info;
		$this->view->id_licence = $idLicence;
		$this->view->search_list = $ObjGen->getLista_titles ( "status='Enabled'", "products", array (
				'id as value',
				'name as label',
				'image' 
		), 'name ASC' );
		
	}
	

	public function detailsAction() {
		$this->_helper->layout->disableLayout ();
		$id_product 					= $this->_request->getParam ( "product" );
		$idLicence 						= $this->_request->getParam ( "l" );
		$ObjGen 						= new Default_Model_Generico ();
		$this->_user 					= App_edvSecurity::getInstance ();		
		$this->_userId 					= $this->_user->userLoggued ()->id;
        $auth 							= Zend_Auth::getInstance();
        $user 							= $auth->getIdentity();
        $id_participant 				= $user['id'];		
		$this->view->profile 		= $user['profile_id'];
		$program_info 			= $ObjGen->getRow ( "id_licence='" . $idLicence."'", "licenses" );
		$porcent 				= $program_info['porcentage_rewards'] != '' ? $program_info['porcentage_rewards'] : 10 ;
		$porcentageCatalogue 	= 100 + $porcent;		
		
		$this->view->product_info 		= $ObjGen->getRow_join ( "a.id=" . $id_product, "products",'subcategories', array ('a.*','b.gst',
		'CASE WHEN b.gst = 10 
				THEN (((a.price * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.')) ) / 100) * '.$porcentageCatalogue.' 
				ELSE (a.price * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.'))
			END  AS points',
		'((freight_cost / 100 ) * 110) * (SELECT points FROM licenses WHERE id_licence = '.$idLicence.')  AS freight_points'), 'b.id_subcategory = a.id_subcategory','a.price_nogst ASC');		
		
		$this->view->categories_list 	= $ObjGen->getLista_titles ( "a.id_category IN (SELECT c.id_category FROM subcategories AS c WHERE (SELECT COUNT(d.id_subcategory) FROM products d WHERE d.id_subcategory= c.id_subcategory GROUP BY d.id_subcategory) != '' AND c.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence}) GROUP BY c.id_category)", "categories AS a", array ('a.*', '(SELECT COUNT(b.id_category) FROM products b WHERE b.id_category= a.id_category AND b.id_subcategory IN (SELECT id_subcategory FROM program_catalogue WHERE id_licence = '.$idLicence.') GROUP BY b.id_category) AS qty' ), 'a.category ASC' );		
		$this->view->subcategories_list = $ObjGen->getLista_titles ( "(SELECT COUNT(b.id_subcategory) FROM products b WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) != '' AND a.id_subcategory IN(SELECT id_subcategory FROM program_catalogue WHERE id_licence = {$idLicence})", "subcategories AS a", array ('a.*', '(SELECT COUNT(b.id_subcategory) FROM products b  WHERE b.id_subcategory= a.id_subcategory GROUP BY b.id_subcategory) AS qty' ), 'a.subcategory ASC' );		
		$this->view->search_list 		= $ObjGen->getLista_titles ( "status='Enabled'", "products", array ('id as value',	'name as label','image' ), 'name ASC' );		
		$points 						= $ObjGen->getRow_select ( "id_participant=" . $id_participant, "program_participants", array('(SELECT SUM(points) FROM program_points WHERE id_participant = a.id_participant) AS total_points', '(SELECT SUM(points * qty) FROM program_redemtion WHERE id_participant = a.id_participant AND `status` IN  (10, 12, 13)) AS total_spend') );
		$totalpoints 					=  ceil($points['total_points'] - $points['total_spend']);
		$this->view->totalpoints 		= $totalpoints ;
		$this->view->program_info 		= $program_info;
		$this->view->id_participant 	= $id_participant;
		$this->view->id_licence 		= $idLicence;
	}
	
	public function participantAction() {
        $this->_helper->layout->disableLayout ();
		$this->_user = App_edvSecurity::getInstance ();		
		$this->_userId = $this->_user->userLoggued ()->id;
        $auth = Zend_Auth::getInstance();
        $user = $auth->getIdentity();	
		
		if($user['profile_id']=='3'){                    
            $id_participant 		= $user['id'];
        } else {
			$id_participant 		= 0;
		} 
				
 		$ObjGen 					= new Default_Model_Generico ();
		$idLicence 					= $this->_request->getParam ( "l" );
		$program_info 				= $ObjGen->getRow ( "id_licence='" . $idLicence."'", "licenses" );
		$this->view->licence_detail = $program_info;		
		$points 					= $ObjGen->getRow_select ( "id_participant=" . $id_participant, "program_participants", array('(SELECT SUM(points) FROM program_points WHERE id_participant = a.id_participant) AS total_points', '(SELECT SUM(points * qty) FROM program_redemtion WHERE id_participant = a.id_participant AND `status` IN  (10, 12, 13)) AS total_spend') );
		$totalpoints 				=  $points['total_points'] - $points['total_spend'];
		$this->view->totalpoints 	= $totalpoints ;
		$this->view->points 		= $points ;			
	}
	
	public function wishlistAction() {
        $this->_helper->layout->disableLayout ();
		$this->_user = App_edvSecurity::getInstance ();		
		$this->_userId = $this->_user->userLoggued ()->id;
        $auth = Zend_Auth::getInstance();
        $user = $auth->getIdentity();	
        $id_participant 			= $user['id'];
		$ObjGen 					= new Default_Model_Generico ();
		$idLicence 					= $this->_request->getParam ( "l" );
		$wish_list 					= $ObjGen->getRows_join2Tables('a.id_participant = '.$id_participant.' AND a.id_licence = '.$idLicence.' AND a.status=15', 'program_redemtion', 'products', 'm8_status', array('a.*','b.name', 'c.status AS status_name', 'b.image'), 'a.id_product = b.id', 'a.status = c.id_status', 'a.id_redemption');
		$points 					= $ObjGen->getRow_select ("id_participant=" . $id_participant, "program_participants", array('(SELECT SUM(points) FROM program_points WHERE id_participant = a.id_participant) AS total_points', '(SELECT SUM(points * qty) FROM program_redemtion WHERE id_participant = a.id_participant AND `status` IN  (10, 12, 13)) AS total_spend') );
		$totalpoints 				=  $points['total_points'] - $points['total_spend'];
		$program_info 				= $ObjGen->getRow ( "id_licence='" . $idLicence."'", "licenses" );
		$this->view->licence_detail = $program_info;
		$this->view->wish_list 		= $wish_list;		
		$this->view->totalpoints 	= $totalpoints ;			
	}
	
	 public function poAction(){
        $this->_helper->viewRenderer->setNoRender ( true );
        $this->_helper->layout->disableLayout ();
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );            
		$id_participant = $this->_request->getParam ( "id_participant" );
		$comments		= $this->_request->getParam ( "comments" );
		$idLicence 		= $this->_request->getParam ( "id_licence" );
		$issuedate = date('d-m-Y H:m:s');	
		$form = ($_POST ['add']);
		$values = array('status' => 10, 'issue_date'=> $issuedate, 'comments' => $comments, 'id_participant' =>$id_participant, 'id_licence' => $idLicence );
		$this->_db->insert ('program_orders', $values );
		$id = $this->_db->lastInsertId ();
			
			foreach ( $form as $row => $values ) {
				$data = array('status' => 10, 'qty' => $_POST['qty'][$row], 'issue_date' => $issuedate,'order_number' => $id );
					if (!empty($id)) {						
						$this->_db->update ('program_redemtion', $data, 'id_redemption='.$row );
						$value = $id;
					} else {
						$value = 0;
					}
			}
			echo $value;
        }
		
			
	 public function deletewishlistAction(){
        $this->_helper->viewRenderer->setNoRender ( true );
        $this->_helper->layout->disableLayout ();
		$this->_db = Zend_Controller_Front::getInstance ()->getParam ( "bootstrap" )->getResource ( "db" );            
		$productid = $this->_request->getParam ( "productid" );
			if (!empty($productid)) {						
						$this->_db->delete('program_redemtion', 'id_redemption='.$productid);
						echo 1;
					} else {
						echo 0;
					}
        }
		
	 public function ordersAction() {
		$ObjGen 				= new Default_Model_Generico ();
		$idLicence 				= $this->_request->getParam ( "l" );
		$this->_user = App_edvSecurity::getInstance ();		
		$this->_userId = $this->_user->userLoggued ()->id;
        $auth = Zend_Auth::getInstance();
        $user = $auth->getIdentity();	
		
		if($user['profile_id']=='3'){                    
            $id_participant 		= $this->_request->getParam ( "p" );
        } else {
			$id_participant 		= 0;
		} 

		
		$orders_list 			= $ObjGen->getRows_status_select('a.id_participant='.$id_participant, 'program_orders', array('(SELECT SUM(points * qty) FROM program_redemtion WHERE order_number = a.id) as totalpoints','a.*','b.status as status_name'));
		$program_info 			= $ObjGen->getRow ( "id_licence='" . $idLicence."'", "licenses" );
		$this->view->licence_detail = $program_info;
		$this->view->id_licence = $idLicence;
		$this->view->ordersList = $orders_list;	
		$this->view->id_participant = $id_participant;
	}
	
		public function orderdetailsAction() {
        $this->_helper->layout->disableLayout ();
		$id							= $this->_request->getParam ( "id" );
		$id_participant				= $this->_request->getParam ( "p" );
		$ObjGen 					= new Default_Model_Generico ();
		
		$this->_user = App_edvSecurity::getInstance ();		
		$this->_userId = $this->_user->userLoggued ()->id;
        $auth = Zend_Auth::getInstance();
        $user = $auth->getIdentity();	
		
		$idLicence 					= $this->_request->getParam ( "l" );
		$orders_list 				= $ObjGen->getRow_status('a.id_participant='.$id_participant.' AND id ='.$id, 'program_orders');
		$wish_list 					= $ObjGen->getRows_join2Tables('a.id_participant = '.$id_participant.' AND a.order_number = '.$id.' AND a.status IN (14, 10, 12, 13)', 'program_redemtion', 'products', 'm8_status', array('a.*','b.name', 'c.status AS status_name', 'b.image'), 'a.id_product = b.id', 'a.status = c.id_status', 'a.id_redemption');
		$program_info 			= $ObjGen->getRow ( "id_licence='" . $idLicence."'", "licenses" );
		$this->view->licence_detail = $program_info;
		$this->view->wish_list 		= $wish_list;
		$this->view->ordersList 	= $orders_list;
		$this->view->id_licence 	= $idLicence;
		$this->view->profile 		= $user['profile_id'];
		}
		
	public function earnAction() {
		$ObjGen 				= new Default_Model_Generico ();
		$idLicence 				= $this->_request->getParam ( "l" );
		$id_participant 		= $this->_request->getParam ( "p" );
		$points_list 			= $ObjGen->getRows('id_participant='.$id_participant, 'program_points');
		$this->view->id_licence = $idLicence;
		$this->view->points_list = $points_list;	
		$this->view->id_participant = $id_participant;
	}
		
		
}
