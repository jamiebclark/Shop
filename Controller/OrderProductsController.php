<?php
class OrderProductsController extends ShopAppController {
	var $name = 'OrderProducts';
	var $components = array('Shop.ShoppingCart',);
	var $helpers = array('Shop.Order');
	
	function add() {
		$redirect = true;
		$msg = null;
		$invalid = array();
		if (!empty($this->request->data)) {
			//Finds Product ID
			if (!$this->OrderProduct->setProductIdFromData($this->request->data)) {
				$invalid['OrderProduct.quanitity'] = 'Please select all product options';
			} else {			
				//Checks if product already exists in the cart
				$this->OrderProduct->quantityExists($this->request->data);
				$this->OrderProduct->Order->validate = array();
			}
			
			if (!empty($invalid) || !$this->OrderProduct->saveAll($this->request->data)) {
				//$this->PersistValidation->store('OrderProduct');
				$msg = 'Sorry, there was an error adding the product to your order';
				foreach ($invalid as $key => $errorMsg) {
					$this->OrderProduct->invalidate($key, $errorMsg);
				}
				//debug($this->OrderProduct->invalidFields());
			} else {
				$order = $this->OrderProduct->Order->find('first', array(
					'link' => array('Shop.OrderProduct'),
					'conditions' => array('OrderProduct.id' => $this->OrderProduct->id)
				));
				
				$this->ShoppingCart->setCart($order['Order']['id']);
				//return true;
				$redirect = array(
					'controller' => 'orders',
					'action' => 'view',
					$order['Order']['id']
				);
			}
		} else {
			$this->_redirectMsg(array('controller' => 'products', 'action' => 'index'));
		}
		//debug(compact('redirect', 'msg'));
		$this->_redirectMsg($redirect, $msg);
	}
	
	//Removes an item from a cart
	function delete($id = null) {
		//Makes sure the current user has the shopping cart id in their session
		$cartId = $this->ShoppingCart->getCart();
		if (!empty($cartId)) {
			$order = $this->OrderProduct->Order->find('first', array(
				'link' => array('Shop.OrderProduct'),
				'conditions' => array('OrderProduct.id' => $id, 'Order.id' => $cartId)
			));
			if (!empty($order)) {
				$this->FormData->deleteData($id);
			}
		}
		$this->_redirectMsg(true, 'There was an error deleting the item. Please try again');
	}
	
	function staff_add($orderId = null, $productId = null) {
		if (!empty($this->request->data['OrderProduct']['order_id'])) {
			$orderId = $this->request->data['OrderProduct']['order_id'];
			$productId = $this->request->data['OrderProduct']['product_id'];
		} else {
			$firstLoad = true;
			$orderId = $this->_paramCheck('order_id', $orderId);
			$productId = $this->_paramCheck('product_id', $productId);
			if (!empty($orderId)) {
				$this->request->data['OrderProduct']['order_id'] = $orderId;
			}
			if (!empty($productId)) {
				$this->request->data['OrderProduct']['product_id'] = $productId;
			}
		}
		if (empty($firstLoad)) {
			$this->_saveData(null, array(
				'success' => array(
					'redirect' => array('controller' => 'orders', 'action' => 'view', $orderId)
				)
			));
		}
		//Shop Order Info
		$order = $this->OrderProduct->Order->findById($this->request->data['OrderProduct']['order_id']);
		if (empty($order)) {
			$this->_redirectMsg(
				array('controller' => 'orders', 'action' => 'index'), 
				'Could not find shop order'
			);
		}

		//Product Info
		if (empty($this->request->data['OrderProduct']['product_id'])) {
			$this->request->data['OrderProduct']['product_id'] = null;
		}
		
		$product = $this->OrderProduct->Product->findById($this->request->data['OrderProduct']['product_id']);
		if (empty($product)) {
			$this->_redirectMsg(
				array('controller' => 'orders', 'action' => 'view', $order['Order']['id']), 
				'Please select a product first'
			);
		}
		$productOptions = $this->OrderProduct->Product->ProductOption->findProductOptions($this->request->data['OrderProduct']['product_id']);
		
		$this->set(compact('order', 'product', 'productOptions'));
	}
	
	function staff_edit($id = null) {
		if (!$this->_saveData(null, array(
			'success' => array(
				'redirect' => array(
					'controller' => 'orders', 
					'action' => 'view', 
					$this->request->data['OrderProduct']['order_id']
				)
			)
		))) {
			$this->request->data = $this->OrderProduct->findById($id);
			
			if (empty($this->request->data)) {
				$this->_redirectMsg(array('controller' => 'orders', 'action' => 'index'), 'Could not find shop order');
			}
		}
		

		
		//Shop Order Info
		$order = $this->OrderProduct->Order->findById($this->request->data['OrderProduct']['order_id']);
		//Product Info
		$product = $this->OrderProduct->Product->findById($this->request->data['OrderProduct']['product_id']);
		$productOptions = $this->OrderProduct->Product->ProductOption->findProductOptions($this->request->data['OrderProduct']['product_id']);

		$this->set(compact('order', 'product', 'productOptions'));
	}
	
	function staff_delete($id = null) {
		$this->FormData->deleteData($id);
	}
}
