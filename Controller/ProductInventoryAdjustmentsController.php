<?php
class ProductInventoryAdjustmentsController extends ShopAppController {
	var $name = 'ProductInventoryAdjustments';

	function admin_index() {
		$this->redirect(array('controller' => 'products'));
	}
	
	function admin_add($productId = null) {
		$default = array(
			'ProductInventoryAdjustment' => array('product_id' => $productId)
		);
		$this->FormData->addData(compact('default'));
	}

	function _setFormElements() {
		$productId = $this->request->data['ProductInventoryAdjustment']['product_id'];
		$product = $this->ProductInventoryAdjustment->Product->find('first', array(
			'conditions' => array('Product.id' => $productId)
		));
		$this->_setCrumbs($product);
		$this->set(compact('product'));
	}
	
	function admin_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	function admin_view($id = null) {
		$result = $this->FormData->findModel($id);
		if (!empty($result)) {
			$this->redirect(array('controller' => 'products', 'action' => 'view', $result['Product']['id']));
		} else {
			$this->_redirectMsg(array('controller' => 'products', 'action' => 'index'), 'Could not locate product inventory');
		}
	}
	
	function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}

	
	function _setCrumbs($product) {
		$this->helpers['Layout.Crumbs']['controllerCrumbs'] = false;
		$this->helpers['Layout.Crumbs']['baseCrumbs'] = array(
			array('Shop', array('controller' => 'catalog_items', 'action' => 'index')),
			array($product['CatalogItem']['title'], array(
					'controller' => 'catalog_items', 
					'action' => 'view', 
					$product['CatalogItem']['id']
				)
			),
			array($product['Product']['sub_title'], array(
					'controller' => 'products',
					'action' => 'view',
					$product['Product']['id'],
				)
			)
		);
	}
}