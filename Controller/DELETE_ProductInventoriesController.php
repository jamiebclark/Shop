<?php
class ProductInventoriesController extends ShopAppController {
	var $name = 'ProductInventories';
	var $helpers = array('Shop.Product');
	
	function admin_index($productId = null) {
		$this->paginate = array(
			'fields' => '*',
			'recursive' => 1,
			'order' => array('Product.active DESC', 'Product.title', 'Product.id', 'ProductOptionChoice1.id'),
			'limit' => 50,
		);
		if (!empty($productId)) {
			$this->set('product', $this->ProductInventory->Product->findById($productId));
			$this->paginate['conditions']['Product.id'] = $productId;
		}
		$productInventories = $this->paginate();
		//debug($productInventories);
		$this->set(compact('productInventories'));
	}
	
	function admin_update_title() {
		$result = $this->ProductInventory->find('list');
		foreach ($result as $id => $title) {
			$this->ProductInventory->updateTitle($id);
		}
	}
	
	function admin_view($id = null) {
		$this->paginate = array(
			'ProductInventoryAdjustment' => array(
				'fields' => '*',
				'link' => array('Shop.ProductInventory'),
				'conditions' => array('ProductInventory.id' => $id)
			)
		);
		$productInventory = $this->ProductInventory->find('first', array(
			'fields' => '*',
			'recursive' => 1,
			'link' => array(
				'Product', 
			),
			'conditions' => array(
				'ProductInventory.id' => $id,
			)
		));
		
		//Finds other product options
		$productInventories = $this->ProductInventory->find('all', array(
			'conditions' => array(
				'ProductInventory.product_id' => $productInventory['Product']['id'],
			)
		));
		
		$productInventoryAdjustments = $this->paginate('ProductInventoryAdjustment');
		//debug($productInventoryAdjustments);
		$this->set(compact('productInventoryAdjustments', 'productInventory', 'productInventories'));
	}
	
	function admin_add($productId = null) {
		if ($this->_saveData() === null) {
			$this->request->data['Product']['id'] = $productId;
		}
		$product = $this->ProductInventory->Product->findById($this->request->data['Product']['id']);
		if (empty($product)) {
			$this->redirectMsg(true, 'Could not find product');
		}
		$this->set(compact('product'));
		$this->set('productOptions', $this->ProductInventory->Product->ProductOption->findProductOptions($this->request->data['Product']['id']));
	}
	function admin_delete($id = null) {
		$this->_deleteData($id);
	}
	
	function admin_rebuild($id = null) {
		$this->ProductInventory->rebuildQuantity($id);
		$this->redirectMsg(array('action' => 'view', $id), 'Rebuilt inventory');
	}
}
