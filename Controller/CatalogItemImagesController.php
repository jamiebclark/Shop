<?php
class CatalogItemImagesController extends ShopAppController {
	var $name = 'CatalogItemImages';
	var $helpers = array('Shop.CatalogItem');

	function index($catalogItemId = null) {
		$catalogItemImages = $this->CatalogItemImage->find('all', array(
			'fields' => '*',
			'link' => array('Shop.CatalogItem'),
			'conditions' => array('CatalogItem.id' => $catalogItemId)
		));
		$catalogItem = $this->CatalogItemImage->CatalogItem->findById($catalogItemId);
		
		if (empty($catalogItemImages)) {
			$this->_redirectMsg(true, 'No images found for that catalogItem');
		} else if (count($catalogItemImages) == 1) {
			$this->redirect(array('action' => 'view', $catalogItemImages[0]['CatalogItemImage']['id']));
		}
		$this->set(compact('catalogItemImages', 'catalogItem'));
	}
	
	function view($id = null) {
		$catalogItemImage = $this->FormData->findModel($id);
		$catalogItemImages = $this->CatalogItemImage->find('all', array(
			'fields' => '*',
			'link' => array('Shop.CatalogItem'),
			'conditions' => array(
				'CatalogItem.id' => $catalogItemImage['CatalogItemImage']['catalog_item_id'],
			)
		));
		$this->set(compact('catalogItemImages'));
	}
	
	function staff_index($catalogItemId = null) {
		$fields = '*';
		$link = array('Shop.CatalogItem');
		$conditions = array();
		if (!empty($catalogItemId)) {
			$conditions['CatalogItem.id'] = $catalogItemId;
		}
		$this->paginate = compact('fields', 'link', 'conditions');
		$catalogItemImages = $this->paginate();
		$this->set(compact('catalogItemImages'));
		$this->set('catalogItem', $this->CatalogItemImage->CatalogItem->findById($catalogItemId));
	}
	
	function staff_view($id = null) {
		$this->FormData->findModel($id);
	}
	
	function staff_add($catalogItemId = null) {
		$this->FormData->addData(array(
			'default' => array('CatalogItemImage' => array('catalog_item_id' => $catalogItemId))
		));
		$this->set('catalogItem', $this->CatalogItemImage->CatalogItem->findById($catalogItemId));
	}
	
	function staff_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	function staff_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	function _setFormElements() {
		$this->set('catalogItems', $this->CatalogItemImage->CatalogItem->selectList());
	}
}
