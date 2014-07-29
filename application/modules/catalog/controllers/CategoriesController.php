<?php
/**
 * Management for catalog categories
 *
 */
class Catalog_CategoriesController extends Zend_Controller_Action
{    
   private $_userId;
    private $_user;
    function init()
    {
        $this->view->assign('baseUrl', $this->getRequest()->getBaseUrl());
        $this->initView();
        $this->_user = App_edvSecurity::getInstance();

        if(!$this->_user->isLogged()) {
            $this->_user->gotoLogin();
        }
        if(!validate('1')){
            $this->_user->gotoLogin();
        }
        $this->_userId = $this->_user->userLoggued()->id;
        $this->_helper->layout->setLayout('layout_admin');
    }
	
    public function indexAction()
    {
       	$table = new Default_Model_Generico();
        $this->view->category_list = $table->getRows("", "categories");  

    }
	
	    public function subcategoriesAction()
    {
       	$table = new Default_Model_Generico();
        $this->view->subcat_list = $table->getRows_join("", "subcategories", "categories", array('a.*','b.category'), 'a.id_category = b.id_category');
		 $this->view->cat_list = $table->getRows("", "categories");  

    }
	
	public function categoriesAction()
    {
       $table = new Default_Model_Generico();
       $this->view->cat_list = $table->getRows("", "categories");   

    }

	
	
}
