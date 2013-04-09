<?php
class HandlingMethodsController extends ShopAppController {
	var $name = 'HandlingMethods';
	
	function admin_index() {
		$handlingMethods = $this->paginate();
		$this->set(compact('handlingMethods'));
	}
	function admin_view($id = null) {
		$this->FormData->findModel($id);
	}
	
	function admin_add() {
		$this->FormData->addData(array('default' => array('HandlingMethod' => array('active' => 1))));
	}
	
	function admin_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
}
