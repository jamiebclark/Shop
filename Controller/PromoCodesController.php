<?php
class PromoCodesController extends ShopAppController {
	var $name = 'PromoCodes';
	
	function staff_index() {
		$promoCodes = $this->paginate();
		$this->set(compact('promoCodes'));
	}
	function staff_view($id = null) {
		$this->FormData->findModel($id);
	}
	
	function staff_add() {
		$this->FormData->addData(array('default' => array('PromoCode' => array('active' => 1))));
	}
	
	function staff_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	function staff_delete($id = null) {
		$this->FormData->deleteData($id);
	}
}
