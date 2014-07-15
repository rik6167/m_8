<?php

class MyPluginAuth extends Zend_Controller_Plugin_Abstract
{
    private $_auth;
    private $_user_session;
    private $_noauth = array('module' => 'default',
                             'controller' => 'index',
                             'action' => 'login');

    private $_noacl = array('module' => 'default',
                            'controller' => 'index',
                            'action' => 'admin');
   
    public function __construct($auth)
    {
        
		$this->_auth = $auth;
        $this->_user_session = new Zend_Session_Namespace('acl');
    }

public function preDispatch($request)
{
	require_once 'Zend/Acl.php';
	require_once 'Zend/Session.php';
	
	$acl_data = new Zend_Acl();
	
	if ($this->_auth->hasIdentity()) 
	{
		if (!isset($acl_session))
		{
			require_once 'Zend/Db.php';
			$params = array ('host' => 'localhost',
                	 'username' => 'root',
              	  	 'password' => 'vertrigo',
             	     'dbname'   => 'cafe');
			$db = Zend_Db::factory('PDO_MYSQL', $params);
			$sql = $db->quoteInto(
    			 'select userroles.name as role_name, resources.name as resource_name, privileges.name 
				  as privilege_name from userroles inner join xref_usersUserRoles on 
				  userroles.id = xref_usersUserRoles.id_userrole inner join users on 
				  xref_usersUserRoles.id_user = users.id inner join xref_privileges_resources_userroles on
 				  userroles.id = xref_privileges_resources_userroles.id_role inner join resources on 
				  xref_privileges_resources_userroles.id_resource = resources.id inner join privileges on 
				  xref_privileges_resources_userroles.id_privileges = privileges.id where users.id = ?',
    			  $this->_auth->getIdentity()->id
				);
			
			//$sql = $db->quoteInto( "" );
			
			
			$result = $db->query($sql);
		
			 	// use the PDOStatement $result to fetch all rows as an array
			$rows = $result->fetchAll();
			exit;
			
			require_once 'Zend/Session.php';
			$userAcl_Session_Namespace = new Zend_Session_Namespace('Zend_Auth');
			$user_Session_Namespace->username = $this->_auth->Getidentity()->username;
			$user_Session_Namespace-> getIdentity()->id[roles] = Array();
			$Acl_Session_Namespace->getIdentity()->id[resources] = Array();
			
			
			/*
			
			
			foreach ($rows as $name => $val) {
				$Acl_Session_Namespace->getIdentity()->id[roles][] = $val['name'];
			}
			/*$role = $rows[0]['name'];
		//$role = new Zend_Acl_Role($auth->getIdentity()->username);
		

		$roleGuest = new Zend_Acl_Role('guest');

		$arr_roles = array();

		foreach($rows as $name => $val) 
		{
    		$this->addRole(new Zend_Acl_Role($val['name'])); 
    		$arr_roles[] = "'" . $val['name'] . "'";
		}
		$str_roles = implode(',',$arr_roles);

		$this->addRole(new Zend_Acl_Role('guest'));
		$this->addRole(new Zend_Acl_Role('rest')); 

		
		
	
		
		// use the PDOStatement $result to fetch all rows as an array
		$rows = $result->fetchAll();
		
		foreach($rows as $name => $val) 
		{
			$Acl_Session_Namespace->getIdentity()->id[resources][] = $val['name'];
    		//$this->add(new Zend_Acl_Resource($val['name']));  
		}
		
		*/
		}
		
		
		
		
		} else {
			if (!isset($this->_user_session->role_name))
			{
				require_once 'Zend/Session.php';
				//Zend_Session::start();
				//$this->_user_session->oed=1;

       			require_once 'Zend/Db.php';
				$params = array ('host' => 'localhost',
                	 'username' => 'root',
              	  	 'password' => 'vertrigo',
             	     'dbname'   => 'cafe');
				$db = Zend_Db::factory('PDO_MYSQL', $params);
				/*$sql = $db->quoteInto(
    			 'select userroles.name as role_name, resources.name as resource_name, privileges.name 
				  as privilege_name from userroles inner join xref_usersUserRoles on 
				  userroles.id = xref_usersUserRoles.id_userrole inner join users on 
				  xref_usersUserRoles.id_user = users.id inner join xref_privileges_resources_userroles on
 				  userroles.id = xref_privileges_resources_userroles.id_role inner join resources on 
				  xref_privileges_resources_userroles.id_resource = resources.id inner join privileges on 
				  xref_privileges_resources_userroles.id_privileges = privileges.id where userRoles.name = ?',
    			  'admins'
				);*/
				# consulta que trae los roles
				$sql = $db->quoteInto(
				 'SELECT DISTINCT t2.id_role, t1.name as role_name  
				  FROM userroles t1 inner join xref_privileges_resources_userroles t2
				  ON t1.id = t2.id_role'
				);
				
				$result = $db->query($sql);
				$rows = $result->fetchAll();
				echo "<pre>";
				//print_r($rows);
				echo "</pre>";
				$arrayPermisison = array();
				foreach($rows as $index => $value)
				{
					echo "id:". $value['role_name']. "<br />";
					$arrayPermisison[$index]['role_name']['name'] .= $value['role_name'];
					
					# trae las paginas por rol
					$sql = $db->quoteInto(
				     'SELECT DISTINCT t2.id_resource, t1.name as resource_name,t2.id_role  
				     FROM resources t1 inner join xref_privileges_resources_userroles t2
				     ON t1.id = t2.id_resource
				     where t2.id_role = ?', $value['id_role']
				    );				
				    $result = $db->query($sql);
				    $rowsp = $result->fetchAll();
				    foreach($rowsp as $indexp => $valuep)
			 	    {
			 	    	echo "id:". $valuep['resource_name']. "<br />";
			 	    	$arrayPermisison[$index]['role_name']['resource_name'][] = $valuep['resource_name'];
			 	    }
	
					
					
				}
				
				echo "<pre>";
				print_r($arrayPermisison);
				echo "</pre>";
				
				
				
				
				exit;
							
				//$sql = $db->quoteInto( "" );
			
			
				$result = $db->query($sql);
		
			 	// use the PDOStatement $result to fetch all rows as an array
				$rows = $result->fetchAll();
				echo "<pre>";
				print_r($rows);
				echo "</pre>";
			// insert into session
			}
		    $arr_existent_roles = Array();
			$this->_user_session->array = $rows;
    		/*foreach ($this->_user_session->array as $arr_next_value) {
    			if (in_array($arr_next_value['role_name']))
    				continue;
    			
    			//$this->add(new Zend_Acl_Resource($arr_next_value['role_name']));
    			$arr_existent_roles[] = $arr_next_value['role_name'];
    			echo "<br>";
    			foreach ($arr_next_value as $val1 => $val2) {
        			echo $val1 . "=>" . $val2;
        			echo "<br>";
        			//foreach($val1 as $key => $val) {
        		//		echo $key . "=>" . $val;
        			}
        		
        	}*/
    		exit;
			//echo($this->_user_session->oed);

			// insert from session to acl
			
				
        }
 /*    
    $controller = $request->controller;
    $action = $request->action;
    $module = $request->module;
	$resource = $controller;
	
	//echo $controller;
	//echo $action;

	//echo $resource;

   
    if ($acl->has($resource)) {
        $resource = null;
    } 
    
    
    

        if (!$this->_acl->isAllowed($role, $resource, $action)) {
            if (!$this->_auth->hasIdentity()) {
                $module = $this->_noauth['module'];
                $controller = $this->_noauth['controller'];
                $action = $this->_noauth['action'];
            } else {
            	$module = $this->_noacl['module'];
				$controller = $this->_noacl['controller'];
				$action = $this->_noacl['action'];
			}
		}

	$request->setModuleName($module);
	$request->setControllerName($controller);
	$request->setActionName($action);
	*/
}
}
            	
