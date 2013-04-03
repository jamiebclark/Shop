<?php
class CatalogItemImagesController extends ShopAppController {
	var $name = 'CatalogItemImages';
	var $helpers = array('Shop.CatalogItem');

	function index($catalogItemId = null) {
		$catalogItemImages = $this->CatalogItemImage->find('all', array(
			'fields' => '*',
			'link' => array('CatalogItem'),
			'conditions' => array(
				'CatalogItem.id' => $catalogItemId,
			)
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
			'link' => array('CatalogItem'),
			'conditions' => array(
				'CatalogItem.id' => $catalogItemImage['CatalogItemImage']['catalog_item_id'],
			)
		));
		$this->set(compact('catalogItemImages'));
	}
	
	function staff_index($catalogItemId = null) {
		$fields = '*';
		$link = array('CatalogItem');
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
		$catalogItem = $this->FormData->findModel($id);
		$this->set(compact('catalogItem'));
	}
	
	function staff_add($catalogItemId = null) {
		if ($this->_saveData() === null) {
			$this->request->data['CatalogItemImage']['catalog_item_id'] = $catalogItemId;
		}
		$this->set('catalogItems', $this->CatalogItemImage->CatalogItem->selectList());
		$this->set('catalogItem', $this->CatalogItemImage->CatalogItem->findById($catalogItemId));
	}
	
	function staff_edit($id = null) {
		if ($this->_saveData() === null) {
			$this->CatalogItemImage->recursive = 1;
			$this->request->data = $this->CatalogItemImage->findById($id);
		}
		$this->set('catalogItems', $this->CatalogItemImage->CatalogItem->selectList());
	}
	
	function staff_delete($id = null) {
		$this->_deleteData($id);
	}
}
