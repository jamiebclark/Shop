<?php
class CatalogItemCategoriesController extends ShopAppController {
	var $name = 'CatalogItemCategories';
	var $helpers = array('Layout.CollapseList');
	
	function admin_index() {
		$catalogItemCategories = $this->CatalogItemCategory->find('threaded');
		$this->set(compact('catalogItemCategories'));
	}
	
	function admin_view($id = null) {
		$this->FormData->findModel($id);
		//Catalog Items inside the Category
		$catalogItems = $this->CatalogItemCategory->findCatalogItems($id);
		
		//Path back to root
		$catalogItemCategoryPath = $this->CatalogItemCategory->getPath($id);
		$children = $this->CatalogItemCategory->findChildren($id, false);
		$this->set(compact('catalogItems', 'catalogItemCategoryPath', 'children'));		
	}
	
	function admin_add() {
		$this->FormData->addData();
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
