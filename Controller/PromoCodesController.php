<?php
class PromoCodesController extends ShopAppController {
	var $name = 'PromoCodes';
	
	function admin_index() {
		$promoCodes = $this->paginate();
		$this->set(compact('promoCodes'));
	}
	function admin_view($id = null) {
		$this->FormData->findModel($id);
	}
	
	function admin_add() {
		$this->FormData->addData(array('default' => array('PromoCode' => array('active' => 1))));
	}
	
	function admin_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
}
