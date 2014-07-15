<?php
/**
 * Dodo - To-do list application
 *
 * License
 *
 * Simply put:
 * You can use or modify this software for any personal or commercial
 * applications with the following exception:
 *   - You cannot host this software using the Dodo name or any
 *      images from the Dodo website including any logos.
 *
 * @author    Greg Wessels (greg@threadaffinity.com)
 *
 * www.threadaffinity.com
 */
class App_AccessList
{
    const ROLE_GUEST = 'guest';
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    protected static $_instance = null;
    protected $_acl = null;
    protected static $_userRoles = array();

    /**
    * Singleton pattern implementation makes "new" unavailable
    *
    */
    private function __construct()
    {
        
        $this->_acl = new Zend_Acl();
        $rolesObj   = new Default_Model_Resources();
        $login      = Zend_Auth::getInstance();
        if ($login->hasIdentity()) {            
            $userId    = $login->getIdentity()->id;
        }else{
            return false;
        }
        $roles    = $rolesObj->getRoles($userId);
        //pprint_r($roles);
        #si hay roles creados los recorre
        
//          $_POST['resource']['admin/index'] = array();
//          $_POST['resource']['companies/index'] = array('add','del');
//          pprint_r($_POST['resource']);
//          $serializado = serialize($_POST['resource']);
//          echo $serializado . '<br>';

       $roleArray = array();
        if(false == empty($roles)){
            foreach($roles as $rol){
                
                $role        = $rol['pr_vvname'];
                $permissions = $rol['pr_permissions'];
                if(false == empty($rol['inherit_name'])){
                   // $this->_acl->addRole($role,$rol['inherit_name']);
                    $this->_acl->addRole(new Zend_Acl_Role($rol['pr_vvname']),$rol['inherit_name']);//hereda los permisos del padre
                }else{
                    //$this->_acl->addRole($role);
                    $this->_acl->addRole(new Zend_Acl_Role($rol['pr_vvname']));//asigna rol

                }
                 $roleArray[$rol['pr_viid']] = $role;

                 //si tiene permisos asignados, los asigna
                 if(false == empty($permissions)){
                     $permisos = unserialize($permissions);
                     //pprint_r($permisos);
                     foreach($permisos as $field => $actions){
                        //$resource = new Zend_Acl_Resource($field);
                         $resource = $this->_acl->add(new Zend_Acl_Resource($field));
                         //pprint_r($actions);
                         if(false == empty($actions)){
                             $this->_acl->allow($role, $field,$actions);//filtra por acciones
                         }else{
                             $this->_acl->allow($role, $field); //permite todo el modulo
                         }



                     }
                     
                 }
            }

            #recorre los permisos

            
        }

        // define our possible user groups
        //$this->_acl->addRole(new Zend_Acl_Role(self::ROLE_GUEST));
        $this->setRoles($roleArray);


    }

    /**
     * Returns an instance of App_AccessControl
     *
     */
    private static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function setRoles($userRoles)
    {
        
        self::$_userRoles =  $userRoles;
    }
    public function getRoles()
    {
        $inst = App_AccessList::getInstance();
        return $inst->_acl;//$this->_userRoles;
    }

    public static function getAcl()
    {
        $inst = App_AccessList::getInstance();
        return $inst->_acl;
    }

    /**
     * Determines if the given role is allowed access to the resource
     *
     */
    public static function isAllowed($role, $resource, $privilege)
    {
            $role = (false == empty(self::$_userRoles))? self::$_userRoles: $role;
      
            $inst = App_AccessList::getInstance();

            if (!$inst->_acl->has($resource)) {
                    $resource = null;
            }
            $result = false;
//            pprint_r($role);
            if(is_array($role)){
                foreach($role as $rol){
                    
                    if($rol == 'Admin'){
                        $result = true;
                        break;
                    }
                    $result = $inst->_acl->isAllowed($rol, $resource, $privilege);
                    #si devuelve un valor porsitivo termina el ciclo
                    if($result){
                       break;
                    }
                }
            }else{
                echo 'rol';
                if($rol == 'Admin'){
                    $result = true;
                    break;
                }else{
                    $result = $inst->_acl->isAllowed($role, $resource, $privilege);
                }
            }

            return $result;
    }

    /**
     * Allows access to the given resource
     *
     */
     public static function getResource($resource)
     {
	$inst = App_AccessList::getInstance();
        if ($inst->_acl->has($resource)){
            return $inst->_acl->get($resource);
        }
       //$userRoles = $inst->_acl->_userRoles;
        return null; // return null if not found
    }


}