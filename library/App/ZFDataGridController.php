<?php
/**
 * Controlador encargado de el manejo del datagrid
 *
 * @author Natalia Metaute
 * @since 09-01-2010
 */
class App_ZFDataGridController  extends Zend_Controller_Action
{
    protected $_db;
    protected $_user;
    protected $_userId;


    function preDispatch()
    {
        $this->_user = App_edvSecurity::getInstance();
        if(!$this->_user->isLogged()) {
            $this->_user->gotoLogin();
        }
        $this->_userId = $this->_user->userLoggued()->id;
        $this->_helper->layout->setLayout('layout_admin');
    }

//    /**
//     * [EN]If a action don't exist, just redirect to the basic
//     *
//     * @param string $name
//     * @param array $var
//     */
//
//    function __call($name, $var) {
//       $this->_redirect ( '' );
//       return false;
//    }

    function init()
    {
        $this->view->assign('baseUrl', $this->getRequest()->getBaseUrl());
        $this->initView();

        //$this->_db = Zend_Registry::get ( 'db' );
        $this->_db = Zend_Controller_Front::getInstance()->getParam( "bootstrap" )->getResource( "db" );
        
    }

    /**
     * @return string the associated title report
     */
    public function titleReport()
    {
        return 'LISTA DE REGISTROS';
    }

    /**
     * @return query basic
     */
    public function queryView() {
        return $this->_db->select ()->from (  $this->tableName() );
    }

    /**
     * Same as __call
     *
     */
    function indexAction() {
        $export = $this->getRequest ()->getParam ( 'export' );
        if (null === $export) {
            $this->_forward ( 'admin' );
        } else {
            $this->_forward ( 'basic' );
        }
    }

    /**
     * The 'most' basic example.
     */
    function basicAction() {

        $grid = $this->grid ( );

        $grid->query ( $this->queryView() );

        $this->view->pages = $grid->deploy ();
        $this->render ( 'index' );
    }


    /**
     * [EN] Simplify the datagrid creation process
     * [EN] Instead of having to write "long" lines of code we can simplify this.
     * [EN] In fact if you have a Class that extends the Zend_Controller_Action
     * [EN] It's not a bad idea put this piece o code there. May be very useful
     *
     *
     * @return $grid
     */
    function grid($export = null) {

        if (null === $export) {
            $export = $this->getRequest ()->getParam ( 'export' );
        }
        //$db = Zend_Registry::get ( 'db' );
        $db = Zend_Controller_Front::getInstance()->getParam( "bootstrap" )->getResource( "db" );
        switch ($export) {
            case 'odt' :
                    $grid = new Bvb_Grid_Deploy_Odt ( $db, $this->titleReport(), '../data/media/temp', array ('download' ) );
                    break;
            case 'ods' :
                    $grid = new Bvb_Grid_Deploy_Ods ( $db, $this->titleReport(), '../data/media/temp', array ('download' ) );
                    break;
            case 'xml' :
                    $grid = new Bvb_Grid_Deploy_Xml ( $db, $this->titleReport(), '../data/media/temp', array ('download' ) );
                    break;
            case 'csv' :
                    $grid = new Bvb_Grid_Deploy_Csv ( $db, $this->titleReport(), '../data/media/temp', array ('download' ) );
                    break;
            case 'excel' :
                    $grid = new Bvb_Grid_Deploy_Excel ( $db, $this->titleReport(), '../data/media/temp', array ('download' ) );
                    break;
            case 'word' :
                    $grid = new Bvb_Grid_Deploy_Word ( $db, $this->titleReport(), '../data/media/temp', array ('download' ) );
                    break;
            case 'wordx' :
                    $grid = new Bvb_Grid_Deploy_Wordx ( $db, $this->titleReport(), '../data/media/temp', array ('download' ) );
                    break;
            case 'pdf' :
                    $grid = new Bvb_Grid_Deploy_Pdf ( $db, $this->titleReport(), '../data/media/temp', array ('download' ) );
                    break;
            case 'graph' :
                    $grid = new Bvb_Grid_Deploy_Graph ( $db, $this->titleReport(), '../data/media/temp', array ('download' ) );
                    break;
            case 'print' :
                    $grid = new Bvb_Grid_Deploy_Print ( $db, $this->titleReport(), '../data/media/temp', array ('download' ) );
                    break;
             case 'ofc':
                  $config = array('files' => array('json'  => 'public/ofc/js/json/json2.js',
                                                     'js'    => 'public/ofc/js/swfobject.js',
                                                     'flash' => 'public/ofc/open-flash-chart.swf',
                                                    ),
                                    'options' => array('set_bg_colour' => '#FFFFFF')

                                   );
                    $grid = new Bvb_Grid_Deploy_Ofc ( array() );
                    $grid->setChartType('pie');
                    $grid->setXLabels('column');//Any column from the query
                    $grid->setTitle('My Title');
                    $grid->setFilesLocation(array('js'=>'public/ofc/js/swfobject.js','json'=>'public/ofc/js/json/json2.js','flash'=> 'public/ofc/open-flash-chart.swf'));
                    $grid->setChartOptions($config); //Just a wrapper for original OFC methods.
                    //$grid->addValues('column',array('options' => array('1' =>)));
                    break;
            default :
                    $grid = new Bvb_Grid_Deploy_Table ( $db, $this->titleReport(), '../data/media/temp', array ('download' ) );
                    break;
        }

        $grid->escapeOutput ( false );
        $grid->addTemplateDir ( 'My/Template/Table', 'My_Template_Table', 'table' );
        $grid->addElementDir ( 'My/Validate', 'My_Validate', 'validator' );
        $grid->addElementDir ( 'My/Filter', 'My_Filter', 'filter' );
        $grid->addFormatterDir ( 'My/Formatter', 'My_Formatter' );
        $grid->imagesUrl = $this->getRequest ()->getBaseUrl () . '/zfdatagrid/images/';
        $grid->cache     = array ('use' => 0, 'instance' => Zend_Registry::get ( 'cache' ), 'tag' => 'grid' );
        $grid->export    = array ('pdf', 'word', 'wordx', 'excel', 'print', 'xml', 'csv');

        return $grid;
    }



    /**
     * Please check the $pdf array to see how we can configure the template header and footer.
     * If you are exporting to PDF you can even choose between a letter format or A4 format and set the page orientation
     * landascape or '' (empty) for vertical
     *
     */
    function pdfAction() {

            $grid = $this->grid (  );
            $grid->query ( $this->_db->select ()->from ( 'City' ) );

            $pdf = array ('logo' => 'public/images/logo.png', 'baseUrl' => '/grid/', 'title' => '', 'subtitle' => '', 'footer' => " ", 'size' => 'a4', #letter || a4
'orientation' => 'landscape', # || ''
'page' => 'Page N.' );

            $grid->setTemplate ( 'pdf', 'pdf', $pdf );

            $this->view->pages = $grid->deploy ();
            $this->render ( 'index' );
    }
    
}
