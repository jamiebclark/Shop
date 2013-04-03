<?php
class ProductPromosController extends ShopAppController {
	var $name = 'ProductPromos';
	
	function staff_index() {
		$productPromos = $this->paginate();
		$this->set(compact('productPromos'));
	}
	function staff_view($id = null) {
		$this->FormData->findModel($id);
	}
	
	function staff_add() {
		$this->_saveData();
	}
	
	function staff_edit($id = null) {
		if ($this->_saveData() === null) {
			$this->request->data = $this->FormData->findModel($id);
		}
	}
	
	function staff_delete($id = null) {
		$this->_deleteData($id);
	}
}
