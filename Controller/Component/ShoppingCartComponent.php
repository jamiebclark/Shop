<?php
/**
 * Component used for the persistent managment of a Shopping Cart across multiple pages
 *
 **/
class ShoppingCartComponent extends Component {
	public $name = 'ShoppingCart';
	public $controller;
	public $components = array(
		'Session',
		'Shop.Constants',
	);
	
	private $_isBlocked = false;		//Tracks if cart ID should be blocked during the current page view
	
	const SESSION_NAME = 'SessionCart.id';
	const VAR_NAME = 'shoppingCartId';
	
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->settings = $settings;
		parent::__construct($collection, $settings);
	}

	public function initialize(Controller $controller) {
		$this->controller =& $controller;
		$this->Constants->setConstantsInit();
		
		if (!empty($this->controller->request->named['unset_cart'])) {
			$this->unsetCart();
			$this->Session->setFlash('Reset Shopping Cart', 'default', array('class' => 'alert-info'));
			$this->controller->redirect(array('controller' => 'catalog_items', 'action' => 'index', 'shop' => true));
		}
		
		//Prevents storing cart information if user is in an admin page
		if (!empty($this->controller->request->params['prefix']) && $this->controller->request->params['prefix'] == 'admin') {
			$this->blockCart();
		} else {
			$this->getCart();
		}
	}
	
	public function getCart($cartId = null) {
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

	public function getCartId() {
		$cartId = null;
		if (!$this->_isBlocked && $this->Session->check(self::SESSION_NAME)) {
			$cartId = $this->Session->read(self::SESSION_NAME);
		}
		return $cartId;
	}

	public function setCart($cartId) {
		if (!$this->_isBlocked) {
			$this->controller->Session->write(self::SESSION_NAME, $cartId);
			$this->controller->set(self::VAR_NAME, $cartId);
		}
	}
	
	public function unsetCart() {
		$this->controller->Session->delete(self::SESSION_NAME);
		$this->controller->set(self::VAR_NAME, null);
	}
	
	//Used to block storing and loading the cart for that specific view
	//Useful if you are on an admin page and you don't want to load the viewed order into your session as an active cart
	public function blockCart() {
		$this->_isBlocked = true;
		$this->controller->set(self::VAR_NAME, null);	
	}
}
