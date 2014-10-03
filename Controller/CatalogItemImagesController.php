<?php
class CatalogItemImagesController extends ShopAppController {
	var $name = 'CatalogItemImages';
	var $components = array('Layout.Table');
	var $helpers = array('Shop.CatalogItem');

	function index($catalogItemId = null) {
		$catalogItemImages = $this->CatalogItemImage->find('all', array(
			'fields' => '*',
			'contain' => array('CatalogItem'),
			'conditions' => array('CatalogItemImage.catalog_item_id' => $catalogItemId)
		));
		$catalogItem = $this->CatalogItemImage->CatalogItem->findById($catalogItemId);
		
		if (empty($catalogItemImages)) {
			$this->redirectMsg(true, 'No images found for that catalogItem');
		} else if (count($catalogItemImages) == 1) {
			$this->redirect(array('action' => 'view', $catalogItemImages[0]['CatalogItemImage']['id']));
		}
		$this->set(compact('catalogItemImages', 'catalogItem'));
	}
	
	function view($id = null) {
		$catalogItemImage = $this->FormData->findModel($id);
		$catalogItemImages = $this->CatalogItemImage->find('all', array(
			'fields' => '*',
			'contain' => array('CatalogItem'),
			'conditions' => array(
				'CatalogItemImage.catalog_item_id' => $catalogItemImage['CatalogItemImage']['catalog_item_id'],
			)
		));

		if (count($catalogItemImages) > 0):
			$ids = Hash::extract($catalogItemImages, '{n}.CatalogItemImage.id');
			$count = count($ids);

			if (($key = array_search($id, $ids)) !== false) {
				$prevKey = $key - 1;
				$nextKey = $key + 1;
				if ($prevKey < 0) {
					$prevKey = $count - 1;
				}
				if ($nextKey >= $count) {
					$nextKey = 0;
				}
				$nextId = $ids[$nextKey];
				$prevId = $ids[$prevKey];
			}
		endif;
		$this->set(compact('catalogItemImages', 'nextId', 'prevId'));
	}
	
	function admin_index($catalogItemId = null) {
		$fields = '*';
		$contain = array('CatalogItem');
		$conditions = array();
		if (!empty($catalogItemId)) {
			$conditions['CatalogItemImage.catalog_item_id'] = $catalogItemId;
		}
		$this->paginate = compact('fields', 'contain', 'conditions');
		$catalogItemImages = $this->paginate();
		$this->set(compact('catalogItemImages'));
		$this->set('catalogItem', $this->CatalogItemImage->CatalogItem->findById($catalogItemId));
	}
	
	function admin_view($id = null) {
		$this->FormData->findModel($id);
	}
	
	function admin_add($catalogItemId = null) {
		$this->FormData->addData(array(
			'default' => array('CatalogItemImage' => array('catalog_item_id' => $catalogItemId))
		));
		$this->set('catalogItem', $this->CatalogItemImage->CatalogItem->findById($catalogItemId));
	}
	
	function admin_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	function _setFormElements() {
		$this->set('catalogItems', $this->CatalogItemImage->CatalogItem->selectList());
	}
}
