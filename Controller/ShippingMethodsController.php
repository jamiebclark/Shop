<?php
class ShippingMethodsController extends ShopAppController {
	var $name = 'ShippingMethods';
	var $helpers = array('Text');
	
	function admin_index() {
		$shippingMethods = $this->paginate();
		$this->set(compact('shippingMethods'));
	}
	
	function admin_view($id = null) {
		$this->redirect(array('action' => 'index'));
	}
	
	function admin_add() {
		$this->FormData->addData();
	}
	
	function admin_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
}