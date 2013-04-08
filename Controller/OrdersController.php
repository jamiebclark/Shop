<?php
class OrdersController extends ShopAppController {
	var $name = 'Orders';
	
	var $components = array(		//'FindFilter', 		'Shop.ShoppingCart'	);	
	var $helpers = array(
		'Shop.Invoice',
		'Shop.CatalogItem', 
		'Shop.PaypalForm',
		'Layout.AddressBook',
		'Layout.Crumbs' => array(
			'controllerCrumbs' => array(array(
				'Store',
				array('controller' => 'catalog_items','action' => 'index'),
			))
		),				
	);
	
	var $paginate = array(
		'fields' => '*',
		'link' => array('Shop.Invoice'),
	);
		/*
	function beforeFilter() {
		parent::beforeFilter();
		$this->FindFilter->filter = array(
			'shipped' => array('options' => array('' => ' -- Either -- ', 1 => 'Shipped', 0 => 'Not Shipped')),
			'paid' => array('options' => array('' => ' -- Either -- ', 1 => 'Paid', 0 => 'Not Paid')),
			'canceled' => array('type' => 'checkbox', 'default' => 0),
			'email' => array('type' => 'text', 'label' => 'Email Address'),
			'name' => array('type' => 'text'),
		);
	}
	*/	
	function view($id = null) {
		$this->request->data = $this->FormData->findModel($id);
	}
	

	function edit() {
		$saveAttrs = array(
			'success' => array(
				'redirect' => array('action' => 'view', 'ID'),
				'message' => 'Updated cart',
			),
			'fail' => array(
				'message' => 'There was an error updating your cart',
				'redirect' => array('action' => 'view', 'ID'),
			)
		);
		$saveOptions = array();

		if (isset($this->request->data['checkout'])) {
			$saveAttrs['success']['redirect'] = array('action' => 'checkout', 'ID');
		} else if (isset($this->request->data['update'])) {
		}
		
		$this->FormData->addData(null, $saveAttrs, $saveOptions);
		$this->redirect(array('action' => 'view', $id));
	}
	
	function print_invoice($id = null) {
		Configure::write('debug', 0);
		$this->header = false;
		$this->_findOrder($id);
	}
	
	function shipping($id = null) {
		if (!empty($this->request->data['Order']['same_billing'])) {
			$cols = array('first_name', 'last_name', 'addline1', 'addline2', 'city', 'state', 'zip', 'country');
			foreach ($cols as $col) {
				$this->request->data['Invoice'][$col] = !empty($this->request->data['Order'][$col]) ? $this->request->data['Order'][$col] : null;
			}
		}

		$this->FormData->editData($id, null, array('contain' => 'Invoice'), array(
			'success' => array(
				'messages' => 'Successfully updated shipping information for your Order',
				'redirect' => array('action' => 'checkout', 'ID')
			)
		));
		//$this->set('states', $this->Order->State->selectList());
		//$this->set('countries', $this->Order->Country->selectList());
	}
	
	function checkout($id = null) {
		$order = $this->FormData->editData($id);
		//Before displaying checkout screen, checks if order is complete
		if (empty($order['Order']['addline1']) || empty($order['Order']['invoice_id'])) {
			//Shipping information has not been entered yet
			$this->redirect(array('action' => 'shipping', $id));
		} else if (!empty($order['Invoice']['paid'])) {
			//Order has been paid already
			$this->redirect(array('action' => 'view', $id));
		}
		$this->set('isArchived', $order['Order']['archived']);
	}
	
	function staff_index() {
		if (!empty($this->request->data['Order']['id'])) {
			$order = $this->Order->findById($this->request->data['Order']['id']);
			if (!empty($order)) {
				$this->redirect(array('action' => 'view', $this->request->data['Order']['id']));
			} else {
				$this->_redirectMsg(true, 'Could not find Order #' . $this->request->data['Order']['id']);
			}
		}

		$this->paginate = $this->_findFilter($this->paginate);
		$orders = $this->paginate();
		$this->set(compact('orders'));
	}
	
	function staff_filter() {
		$this->render('/FindFilters/filter');
	}
	
	function staff_view($id = null) {
		if ($this->_saveData() === null) {
			$this->request->data = $this->FormData->findModel($id);
		} else {
			$id = $this->request->data['Order']['id'];
			$this->FormData->findModel($id);
		}
		$this->Order->query('UPDATE webdb.order_products SET sub_total = price * quantity');

		$this->set('products', $this->Order->OrderProduct->Product->selectList());
		$this->set('orderShippingMethods', $this->Order->ShippingMethod->selectList());
		$this->set('invoicePaymentMethods', $this->Order->Invoice->InvoicePaymentMethod->selectList());

	}
	
	function staff_edit($id = null) {
		$this->FormData->editData($id);
		/*
		null, array(), array('validate' => false)) === null) {
			$this->request->data = $this->Order->find('first', array(
				'fields' => '*',
				'link' => array('Invoice'),
				'conditions' => array(
					'Order.id' => $id
				),
				'postContain' => array(
					'OrderProduct',
					'OrdersProductHandling',
				)
			));
		}
		*/
		$this->set('products', $this->Order->OrderProduct->Product->selectList());
		$this->set('states', $this->Order->State->selectList());
		$this->set('countries', $this->Order->Country->selectList());
		$this->set('invoicePaymentMethods', $this->Order->Invoice->InvoicePaymentMethod->selectList());
		
		/*$productOptionsResult = $this->Order->OrderProduct->Product->ProductOption->find('all', array(
			'fields' => '*',
			'link' => array('Product' => array('OrderProduct' => array('Order'))),
			'postContain' => array('ProductOptionChoice'),
			'conditions' => array(
				'Order.id' => $id,
			),
			'group' => 'Product.id'
		));
		$productOptions = array();
		foreach ($productOptionsResult as $productOption) {
			$productOptions[$productOption['Product']['id']][] = array(
				'ProductOption' => $productOption['ProductOption'],
				'ProductOptionChoice' => $productOption['ProductOptionChoice']
			);
		}*/
		$productOptions = $this->Order->findProductOptions($id);
		$this->set(compact('productOptions'));
		$this->set('orderShippingMethods', $this->Order->OrderShippingMethod->selectList());
	}
	
	function staff_add() {
		$this->FormData->addData();
	}
	
	function staff_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	function staff_total() {
		$orders = $this->Order->find('all', array(
			'fields' => array(
				'SUBSTRING(Invoice.paid, 1, 10) AS paid_day',
				'SUM(Order.total) AS total',
				'IF(MONTH(Invoice.paid) BETWEEN 1 AND 2, YEAR(Invoice.paid), YEAR(Invoice.paid) + 1) AS year',
			),
			'link' => array('Shop.Invoice'),
			'conditions' => array(),
			'group' => 'paid_day',
			'order' => 'Invoice.paid DESC',
		));
		$totals = array();
		$stats = array(
			'min' => 0,
			'max' => 0,
			'min_day' => null,
			'max_day' => null,
		);
		foreach ($orders as $order) {
			$year = $order[0]['year'];
			$total = $order[0]['total'];
			$day = $order[0]['paid_day'];
			
			if (empty($year)) {
				continue;
			}
			
			list($y, $m, $d) = explode('-', $day);
			$dayKey = (2 - ($year - $y)) * 10000 + (100 * $m) + $d;
			if (empty($stats['min_day']) || $dayKey < $stats['min_day']) {
				$stats['min_day'] = $dayKey;
			} else if (empty($stats['max_day']) || $dayKey > $stats['max_day']) {
				$stats['max_day'] = $dayKey;
			}
			if ($total > $stats['max']) {
				$stats['max'] = $total;
			} else if ($total < $stats['min']) {
				$stats['min'] = $total;
			}
			if (empty($totals[$year])) {
				$totals[$year] = array(
					'day' => array(),
					'total' => 0,
				);
			}
			$totals[$year]['day'][$day] = $total;
			$totals[$year]['total'] += $total;
		}
		$this->set(compact('totals', 'stats'));
	}
	
	function _findFilter($options = array()) {
		$search = array('shipped', 'paid', 'canceled');
		$named = $this->request->named;
		if (isset($named['canceled'])) {
			$options['conditions']['Order.canceled'] = round($named['canceled']);
		}
		if (isset($named['paid'])) {
			$options['link']['Shop.Invoice'] = array();
			if ($named['paid']) {
				$options['conditions']['NOT']['Invoice.paid'] = null;
			} else {
				$options['conditions']['Invoice.paid'] = null;
			}
		}
		if (isset($named['shipped'])) {
			$options['conditions']['Order.shipped'] = round($named['shipped']);
		}
	
		if (isset($this->findFilterVal['email'])) {
			$options['conditions']['Invoice.email LIKE'] = trim($this->findFilterVal['email']);
		}
		if (isset($this->findFilterVal['name'])) {
			$options['conditions'][]['OR'] = array(
				'Order.first_name LIKE' => trim($this->findFilterVal['name']) . '%',
				'Order.last_name LIKE' => trim($this->findFilterVal['name']) . '%',
				'CONCAT(Order.first_name," ",Order.last_name) LIKE' => trim($this->findFilterVal['name']) . '%',
			);
		}
		return $options;
	}
	
	function _setFindModelAttrs($defaults = array()) {
		return array_merge($defaults, array(
			'method' => 'findOrder',
			'passIdToMethod' => true,
		));
	}
	
	function _setFindModelOptions($options = array()) {
		return array_merge(array(
			'fields' => '*',
			'link' => array('Shop.Invoice'),
			'postContain' => array(
				'OrderProduct' => array(
					'link' => array('Shop.Product'),
				)
			),
		), $options);
	}
	
	function _beforeFindModel($options = array()) {
		if (empty($this->FormData->id)) {
			if (!empty($this->request->data['Order']['id'])) {
				$this->FormData->id = $this->request->data['Order']['id'];
			} else {
				$this->FormData->id = $this->ShoppingCart->getCartId();
			}
		}
		return $options;
	}
	
	function _afterFindModel($result = null) {
		if (!empty($result)) {
			$this->ShoppingCart->setCart($result['Order']['id']);
		}
		return $result;
	}
}