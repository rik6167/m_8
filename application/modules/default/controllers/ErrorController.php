<?php
/**
 * Manejo de errores ACL
 *
 * @author nmetaute
 */
class ErrorController extends Zend_Controller_Action {
	public function init() {
		$this->initView ();
	}
	public function indexAction() {
	}
	public function noaccessAction() {
	}
}
