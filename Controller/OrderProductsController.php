<?php
class OrderProductsController extends ShopAppController {
	var $name = 'OrderProducts';
	var $components = array('Shop.ShoppingCart',);
	var $helpers = array('Shop.Order');
	
	function _invalidDisplay($errors = null) {
		$out = '';
		if (empty($errors)) {
			$errors = $this->OrderProduct->validationErrors;
		}
		foreach ($errors as $field => $msg) {
			$out .= '<li>';
			if (!is_numeric($field)) {
				$out .= '<strong>' . $field . ':</strong> ';
			}
			if (is_array($msg)) {
				if (count($msg) == 1 && isset($msg[0]) && !is_array($msg[0])) {
					$out .= $msg[0];
				} else {
					$out .= $this->_invalidDisplay($msg);
				}
			} else {
				$out .= $msg;
			}
			$out .= '</li>';
		}
		return "<ul>$out</ul>\n";
	}
	
	function add() {
		$redirect = true;
		$msg = null;
		$invalid = array();
		

		if (!empty($this->request->data)) {
			/*
			//Finds Product ID
			if (!$this->OrderProduct->setProductIdFromData($this->request->data)) {
				$invalid['OrderProduct.quanitity'] = 'Please select all product options';
			} else {			
				//Checks if product already exists in the cart
				$this->OrderProduct->quantityExists($this->request->data);
				$this->OrderProduct->Order->validate = array();
			}
			*/
			$success = null;
			if (!empty($invalid) || !$this->OrderProduct->saveAll($this->request->data)) {
				//$this->PersistValidation->store('OrderProduct');
				//debug($this->OrderProduct->validationErrors);
				$success = false;
				$msg = 'Sorry, there was an error adding the product to your order';
				foreach ($invalid as $key => $errorMsg) {
					$this->OrderProduct->invalidate($key, $errorMsg);
				}
				$msg .= $this->_invalidDisplay();
			} else {
				$success = false;
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
		$this->_redirectMsg($redirect, $msg, $success);
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
	
	function admin_add($orderId = null, $productId = null) {
		$this->_paramCheck('order_id', $orderId);
		$this->_paramCheck('product_id', $productId);
		
		$default = array('OrderProduct' => array('order_id' => $orderId, 'product_id' => $productId));
		$this->FormData->addData(compact('default'));
	}
	
	function admin_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	function _setFormElements() {
		if ($orderId = $this->_paramCheck('order_id')) {
			$this->set('order', $this->OrderProduct->Order->findById($orderId));
		}
		if ($productId = $this->_paramCheck('product_id')) {
			$this->set(array(
				'catalogItem' => $this->OrderProduct->Product->findCatalogItem($productId),
			));
		}
		$this->set('products', $this->OrderProduct->Product->selectList());
	}
	
	function _paramCheck($varName, &$var = null) {
		if (empty($var)) {
			if (isset($this->request->data['OrderProduct'][$varName])) {
				$var = $this->request->data['OrderProduct'][$varName];
			} else if (isset($this->request->named[$varName])) {
				$var = $this->request->named[$varName];
			} else if (isset($this->request->query[$varName])) {
				$var = $this->request->query[$varName];
			}
		}
		return $var;
	}
}
