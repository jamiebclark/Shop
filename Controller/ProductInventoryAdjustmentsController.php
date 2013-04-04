<<<<<<< HEAD
<?php
class ProductInventoryAdjustmentsController extends ShopAppController {
	var $name = 'ProductInventoryAdjustments';
	var $helpers = array('Shop.Product', 'Layout.Crumbs');

	function staff_index() {
		$this->redirect(array('controller' => 'product_inventories'));
	}
	
	function staff_add($productInventoryId = null) {
		$default = array(
			'ProductInventoryAdjustment' => array(
				'product_inventory_id' => $productInventoryId,
			)
		);
		$this->FormData->addData(compact('default'));
	}

	function _setFormElements() {
		$productInventoryId = $this->request->data['ProductInventoryAdjustment']['product_inventory_id'];
		$productInventory = $this->ProductInventoryAdjustment->ProductInventory->find('first', array(
			'conditions' => array('ProductInventory.id' => $productInventoryId)
		));
		$this->_setCrumbs($productInventory);
		$this->set(compact('productInventory'));
	}
	
	function staff_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	function staff_view($id = null) {
		$result = $this->FormData->findModel($id);
		if (!empty($result)) {
			$this->redirect(array('controller' => 'product_inventories', 'action' => 'view', $result['ProductInventory']['id']));
		} else {
			$this->_redirectMsg(array('controller' => 'product_inventories', 'action' => 'index'), 'Coult not location product inventory');
		}
	}
	
	function staff_delete($id = null) {
		$this->_deleteData($id);
	}

	
	function _setCrumbs($productInventory) {
		$this->helpers['Crumbs']['controllerCrumbs'] = false;
		$this->helpers['Crumbs']['baseCrumbs'] = array(
			array('Products', array('controller' => 'products', 'action' => 'index')),
			array($productInventory['Product']['title'], array(
					'controller' => 'products', 
					'action' => 'view', 
					$productInventory['Product']['id']
				)
			),
			array($productInventory['ProductInventory']['title'], array(
					'controller' => 'product_inventories',
					'action' => 'view',
					$productInventory['ProductInventory']['id'],
				)
			)
		);
	}
}
=======
<?php
class ProductInventoryAdjustmentsController extends ShopAppController {
	var $name = 'ProductInventoryAdjustments';
	var $helpers = array('Shop.Product', 'Layout.Crumbs');

	function staff_index() {
		$this->redirect(array('controller' => 'product_inventories'));
	}
	
	function staff_add($productInventoryId = null) {
		$default = array(
			'ProductInventoryAdjustment' => array(
				'product_inventory_id' => $productInventoryId,
			)
		);
		$this->FormData->addData(compact('default'));
	}

	function _setFormElements() {
		$productInventoryId = $this->request->data['ProductInventoryAdjustment']['product_inventory_id'];
		$productInventory = $this->ProductInventoryAdjustment->ProductInventory->find('first', array(
			'conditions' => array('ProductInventory.id' => $productInventoryId)
		));
		$this->_setCrumbs($productInventory);
		$this->set(compact('productInventory'));
	}
	
	function staff_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	function staff_view($id = null) {
		$result = $this->FormData->findModel($id);
		if (!empty($result)) {
			$this->redirect(array('controller' => 'product_inventories', 'action' => 'view', $result['ProductInventory']['id']));
		} else {
			$this->_redirectMsg(array('controller' => 'product_inventories', 'action' => 'index'), 'Coult not location product inventory');
		}
	}
	
	function staff_delete($id = null) {
		$this->_deleteData($id);
	}

	
	function _setCrumbs($productInventory) {
		$this->helpers['Crumbs']['controllerCrumbs'] = false;
		$this->helpers['Crumbs']['baseCrumbs'] = array(
			array('Products', array('controller' => 'products', 'action' => 'index')),
			array($productInventory['Product']['title'], array(
					'controller' => 'products', 
					'action' => 'view', 
					$productInventory['Product']['id']
				)
			),
			array($productInventory['ProductInventory']['title'], array(
					'controller' => 'product_inventories',
					'action' => 'view',
					$productInventory['ProductInventory']['id'],
				)
			)
		);
	}
}
>>>>>>> 7f1010ba1dfec77e6fe69120dbda39b9bea5eb76
