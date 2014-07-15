<?php
class Admin_ClientController extends App_ZFDataGridController {
	
    public function indexAction() {
		$table = new Default_Model_Generico ();
		$this->view->client = $table->getRows ( "", "client" );
	}
        
	public function editclientAction() {
		$id = $this->_request->getParam ( "id" );
		$table = new Default_Model_Generico ();
		$objProg = new Default_Model_Program ();
		$this->view->countrys = $table->getLista_titles ( "Continent='Oceania'", "country", array (
				'Code',
				'LocalName' 
		), "LocalName" );
		$this->view->state = $table->getLista_titles ( "CountryCode='AUS'", "city", array (
				'DISTINCT(District)' 
		), "District" );
		$this->view->city = $table->getLista_titles ( "CountryCode='AUS'", "city", array (
				'District',
				'Name' 
		), "Name" );
		$this->view->status = $table->getLista_titles ( "type='Client'", "m8_status", array (
				'id_status',
				'status' 
		), "status" );
		$this->view->licences = $table->getLista_titles ( "active=1", "license_types", array (
				'id',
				'name' 
		), "Name" );
		if (! empty ( $id )) {
			$this->view->client_detail = $table->getRow ( "id_client=" . $id, "client" );
			$this->view->userList = $table->getRows_status ( "a.id_client=" . $id . " AND b.type='Client'", "user" );
			$this->view->programList = $objProg->getRows ( "a.id_client=" . $id . " AND b.type='Client'", "program" );
			$this->view->licencesList = $table->getRows ( "client_id=" . $id, "licenses" );
		} else {
			$this->view->client_detail = array ();
			$this->view->userList = array ();
			$this->view->programList = array ();
			$this->view->licencesList = array ();
		}
	}
}