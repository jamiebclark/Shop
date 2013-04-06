<?php
/**
 * Component used for the persistent managment of a Shopping Cart across multiple pages
 *
 **/
class ShoppingCartComponent extends Component {
	var $name = 'ShoppingCart';
	var $controller;
	
	var $components = array('Session');
	
	function __construct(ComponentCollection $collection, $settings = array()) {
		$this->settings = $settings;
		parent::__construct($collection, $settings);
	}

	function initialize(&$controller) {
		$this->controller =& $controller;
		$this->getCart();
	}
	
	function getCart($cartId = null) {
		if (empty($cartId)) {
			$cartId = $this->getCartId();
		}
		$Order = ClassRegistry::init('Shop.Order');
		$shoppingCart = $Order->find('first', array(
			'joins' => array(
				array(
					'table' => 'invoices',
					'alias' => 'Invoice',
					'type' => 'LEFT',
					'conditions' => array('Invoice.id = Order.invoice_id'),
				),
			),
			'conditions' => array('Invoice.paid' => null, 'Order.id' => $cartId)
		));
		if (empty($shoppingCart)) {
			$this->unsetCart();
			return false;
		} else {
			$this->controller->set(compact('shoppingCart'));
			$this->setCart($cartId);
		}
		return $cartId;
	}

	function getCartId() {
		$cartId = null;
		if ($this->Session->check('ShoppingCart.id')) {
			$cartId = $this->Session->read('ShoppingCart.id');
		}
		return $cartId;
	}

	function setCart($cartId) {
		$this->controller->Session->write('ShoppingCart.id', $cartId);
		$this->controller->set('shoppingCartId', $cartId);
	}
	
	function unsetCart() {
		$this->controller->Session->delete('ShoppingCart.id');
		$this->controller->set('shoppingCartId', null);
	}
}
