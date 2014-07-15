<?
class MyAcl extends Zend_Acl
{
    public function __construct(Zend_Auth $auth)
    {
		$identity = $auth->getIdentity()->id;
		//echo $identity->username;
		//echo ($auth->getIdentity()->role);

		if ($auth->hasIdentity()) 
		{
			require_once 'Zend/Db.php';
			$params = array ('host' => 'localhost',
                	 'username' => 'root',
              	  	 'password' => 'vertrigo',
             	     'dbname'   => '');
			$db = Zend_Db::factory('PDO_MYSQL', $params);
			$sql = $db->quoteInto(
    			 'select ur.name from userRoles as ur
				 join xref_usersUserRoles as x
				 on (ur.id = x.id_userRole)
				 join users as u
				 on (u.id = x.id_user)
				 where u.id = ?',
    			 $auth->getIdentity()->id
				);
			$result = $db->query($sql);
		
			 	// use the PDOStatement $result to fetch all rows as an array
			$rows = $result->fetchAll();
			$role = $rows[0]['name'];
		//$role = new Zend_Acl_Role($auth->getIdentity()->username);
		}

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

	$sql = "select re.name from resources as re
			join xref_privileges_resources_userRoles as x
			on (re.id = x.id_resource)
			join userRoles as ur
			on (ur.id = x.id_role)
			where ur.name in ($str_roles);";
		
		
	$result = $db->query($sql);
		
	// use the PDOStatement $result to fetch all rows as an array
	$rows = $result->fetchAll();
		
	foreach($rows as $name => $val) 
	{
    	$this->add(new Zend_Acl_Resource($val['name']));  
	}
	
     $this->allow('guest', 'index','login');
     $this->allow('rest', 'client', 'add'); 
     $this->deny('rest', 'client', 'edit'); 
     $this->allow('rest', 'index');
     $this->allow('rest', 'admin');
        
     $this->allow('admins', 'admin');
     $this->allow('admins', 'index');
     $this->allow('admins', 'client');
    }
}
?>
