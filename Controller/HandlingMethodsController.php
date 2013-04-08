<?php
class HandlingMethodsController extends ShopAppController {
	var $name = 'HandlingMethods';
	
	function staff_index() {
		$handlingMethods = $this->paginate();
		$this->set(compact('handlingMethods'));
	}
	function staff_view($id = null) {
		$this->FormData->findModel($id);
	}
	
	function staff_add() {
		$this->FormData->addData(array('default' => array('HandlingMethod' => array('active' => 1))));
	}
	
	function staff_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	function staff_delete($id = null) {
		$this->FormData->deleteData($id);
	}
}
