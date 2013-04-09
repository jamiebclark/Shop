<?php
class CatalogItemCategoriesController extends ShopAppController {
	var $name = 'CatalogItemCategories';
	
	function admin_index() {
		$this->CatalogItemCategory->recover('tree');
		$catalogItemCategories = $this->CatalogItemCategory->find('threaded');
		$this->set(compact('catalogItemCategories'));
		
		$saveOptions = array(
			'success' => array(
				'redirect' => array('action' => 'index'),
			)
		);
		if ($this->FormData->saveData(null, $saveOptions) === null) {
			if (!empty($this->request->params['named']['edit'])) {
				$this->request->data = $this->FormData->findModel($this->request->params['named']['edit']);
			}
		}
		$this->_setFormElements();
		$this->set('isAjax', round($this->request->is('ajax')));
	}
	
	function admin_view($id = null) {
		$this->FormData->findModel($id);
	}
	
	function admin_add() {
		$this->FormData->addData();
		$this->set('catalogItems', $this->CatalogItemCategory->CatalogItem->find('list'));
	}
	
	function admin_edit($id = null) {		$this->FormData->editData($id);
	}
	
	function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	function _beforeFindModel($options = array()) {
		$this->set('parents', $this->CatalogItemCategory->generateTreeList());		return $options;
	}
	
	function _setFormElements() {
		$this->set('catalogItems', $this->CatalogItemCategory->CatalogItem->find('list'));
	}
}
