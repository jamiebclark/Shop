<?php
class ProductsController extends ShopAppController {
	var $name = 'Products';
	var $components = array('Layout.Activate', 'Layout.Table');
	
	//Anticipates bad links of /products/view/$catalogItemId
	function view ($catalogItemId) {
		$this->redirect(array('controller' => 'catalog_items', 'action' => 'view', $catalogItemId));
	}
	
	function admin_index($catalogItemId = null) {
		$this->paginate = array(
			'fields' => '*',
			'recursive' => 0,
			'conditions' => array(),
			'limit' => 50,
			'order' => array(
				'CatalogItem.active' => 'desc',
				'Product.title' => 'asc',
				'Product.id' => 'asc',
				'ProductOptionChoice1.id' => 'asc',
			),
		);
		if (!empty($catalogItemId)) {
			$paginate = $this->paginate;
			$paginate['conditions'] = array('CatalogItem.id' => $catalogItemId);
			$this->paginate = $paginate;
			$this->set('catalogItem', $this->Product->CatalogItem->findById($catalogItemId));
		}
		$products = $this->paginate();
		$this->set(compact('products'));
		
		//TODO: Turning this off for now because it's creating blank options in current catalog items
		// Perhaps add "active" field to catalog_item_options?
		// $this->Product->updateMissingCatalogItemOptions();

		$this->Product->combine();
	}
	
	function admin_update_title() {
		$result = $this->Product->find('list');
		foreach ($result as $id => $title) {
			$this->Product->updateTitle($id);
		}
	}
	
	function admin_view($id = null) {
		$this->paginate = array(
			'ProductInventoryAdjustment' => array(
				'fields' => '*',
				'link' => array('Shop.Product' => array('Shop.CatalogItem')),
				'conditions' => array('Product.id' => $id)
			)
		);
		
		$product = $this->Product->find('first', array(
			'fields' => '*',
			'recursive' => 1,
			'link' => array('Shop.CatalogItem'),
			'conditions' => array('Product.id' => $id)
		));
		
		//Finds other product options
		$products = $this->Product->find('all', array(
			'conditions' => array('Product.catalog_item_id' => $product['CatalogItem']['id'])
		));
		
		$productInventoryAdjustments = $this->paginate('ProductInventoryAdjustment');
		$this->set(compact('productInventoryAdjustments', 'product', 'products'));
	}
	
	function admin_add($catalogItemId = null) {
		$this->FormData->addData(array(
			'default' => array('Product' => array('id' => $catalogItemId))
		));
	}

	function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	function _setFormElements() {
		$catalogItemId = $this->request->data['CatalogItem']['id'];
		$catalogItem = $this->Product->CatalogItem->findById($catalogItemId);
		if (empty($catalogItem)) {
			$this->redirectMsg(true, 'Could not find product');
		}
		$this->set(compact('catalogItem'));
		$this->set('catalogItemOptions', $this->Product->CatalogItem->CatalogItemOption->findCatalogItemOptions($catalogItemId));
	}
}
