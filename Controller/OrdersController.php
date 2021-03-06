<?php
App::uses('OrderEmail', 'Shop.Network/Email');
App::uses('InvoiceEmail', 'Shop.Network/Email');
class OrdersController extends ShopAppController {
	public $name = 'Orders';
	
	public $components = [
		//'FindFilter', 
		'Shop.ShoppingCart'
	];
	
	public $helpers = [
		'Shop.Invoice',
		'Shop.CatalogItem', 
		'Shop.PaypalForm',
		'Shop.AmazonForm',
		'Layout.AddressBook',
		'Layout.AddressBookForm',
		'Layout.Crumbs' => [
			'controllerCrumbs' => [[
				'Store',
				['controller' => 'catalog_items','action' => 'index'],
			]]
		],
	];
	
	public $paginate = [
		'fields' => '*',
		'link' => ['Shop.Invoice'],
	];
	
	/*
	function beforeFilter() {
		parent::beforeFilter();
		$this->FindFilter->filter = [
			'shipped' => ['options' => ['' => ' -- Either -- ', 1 => 'Shipped', 0 => 'Not Shipped']],
			'paid' => ['options' => ['' => ' -- Either -- ', 1 => 'Paid', 0 => 'Not Paid']],
			'canceled' => ['type' => 'checkbox', 'default' => 0],
			'email' => ['type' => 'text', 'label' => 'Email Address'],
			'name' => ['type' => 'text'],
		];
	}
	*/
	
	public function view($id = null) {
		if (empty($id)) {
			$id = $this->ShoppingCart->getCartId();
		}
		$this->request->data = $this->FormData->findModel($id);
	}
	

	public function edit($id = null) {
		$redirect = ['action' => 'view', $id];

		if (!empty($this->request->data['Order']['add_promo_code'])) {
			$promoCode = $this->request->data['Order']['add_promo_code'];
			try {
				$this->Order->addPromoCode($this->request->data['Order']['id'], $promoCode);
			} catch (Exception $e) {
				$this->Flash->error($e->getMessage(), compact('redirect'));
			}
			$this->Flash->alert('Added promo code: ' . $promoCode);
			$this->redirect($redirect);
		}

		$saveAttrs = [
			'success' => [
				'redirect' => ['action' => 'view', 'ID'],
				'message' => 'Updated cart',
			],
			'fail' => [
				'message' => 'There was an error updating your cart',
				'redirect' => ['action' => 'view', 'ID'],
			]
		];
		$saveOptions = [];

		$this->Order->validate = [];
		if (isset($this->request->data['checkout'])) {
			$this->FormData->setSuccessRedirect(['action' => 'checkout', 'ID']);
		} else if (isset($this->request->data['update'])) {
		}
		
		$this->FormData->editData($id, $saveAttrs, $saveOptions);
		$this->redirect($redirect);
	}
	
	public function invoice($id = null) {
		Configure::write('debug', 0);
		$this->FormData->findModel($id);
	}
	
	public function shipping($id = null) {
		$this->FormData->setSuccessMessage('Successfully updated shipping information for your Order');
		$this->FormData->setSuccessRedirect(['action' => 'checkout', 'ID']);
		$this->FormData->editData($id, null, ['contain' => 'Invoice']);
		//$this->set('states', $this->Order->State->selectList());
		//$this->set('countries', $this->Order->Country->selectList());
	}
	
	public function checkout($id = null) {
		$order = $this->FormData->editData($id);
		//Before displaying checkout screen, checks if order is complete
		if (empty($order['Order']['addline1']) || empty($order['Order']['invoice_id'])) {
			//Shipping information has not been entered yet
			$this->redirect(['action' => 'shipping', $id]);
		} else if (!empty($order['Invoice']['paid'])) {
			//Order has been paid already
			$this->redirect(['action' => 'view', $id]);
		}
		$this->set('isArchived', $order['Order']['archived']);
	}
	
	//Performs a temporary fix to adjust those orders that hadn't been marked as archived when they were paid
	public function admin_fix_archived() {
		$orders = $this->Order->find('all', [
			'link' => ['Shop.Invoice'],
			'conditions' => [
				'Order.archived' => 0,
				'NOT' => ['Invoice.paid' => null],
			]
		]);
		
		debug(sprintf('Found %d orders not marked as archived', count($orders)));
		
		foreach ($orders as $order) {
			$id = $order['Order']['id'];
			
			$this->Order->create();
			$this->Order->updateArchived($id);
			$this->Order->updateProductStock($id);
		}
		
	}
	public function admin_index() {
		$this->Order->deleteOldEmptyOrders();
		if ($deletedCount = $this->Order->getAffectedRows()) {
			$this->Flash->alert(sprintf('Deleted %d old empty orders', $deletedCount));
		}
		
		if (!empty($this->request->data['Order']['id'])) {
			$order = $this->Order->findById($this->request->data['Order']['id']);
			if (!empty($order)) {
				$this->redirect(['action' => 'view', $this->request->data['Order']['id']]);
			} else {
				$this->redirectMsg(true, 'Could not find Order #' . $this->request->data['Order']['id']);
			}
		}

		$this->paginate = $this->_findFilter($this->paginate);
		$orders = $this->paginate();
		$this->set(compact('orders'));
	}
	
	public function admin_filter() {
		$this->render('/FindFilters/filter');
	}
	
	public function admin_view($id = null) {
		$order = $this->FormData->editData($id);
		$this->set([
			'archived' => $order['Order']['archived'],
			'canceled' => $order['Order']['canceled'],
		]);
	}
	
	public function admin_edit ($id = null) {
		$order = $this->FormData->editData($id);
	}
	
	public function admin_add() {
		$default = [
			'Order' => [
				'auto_shipping' => 1,
				'auto_price' => 1,
				'auto_handling' => 1,
				'sub_total' => 0,
				'shipping' => 0,
			]
		];
		$handlingMethods = $this->Order->HandlingMethod->find('all', [
			'conditions' => ['HandlingMethod.active' => 1]
		]);
		foreach ($handlingMethods as $handlingMethod) {
			$handlingMethod = $handlingMethod['HandlingMethod'];
			$handlingMethod['handling_method_id'] = $handlingMethod['id'];
			unset($handlingMethod['id']);
			$default['OrdersHandlingMethod'][] = $handlingMethod;
		}
		$this->FormData->addData(compact('default'));
	}
	
	public function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	public function admin_total() {
		$orders = $this->Order->find('all', array(
			'fields' => array(
				'SUBSTRING(Invoice.paid, 1, 10) AS paid_day',
				'SUM(Order.total) AS total',
				'IF(MONTH(Invoice.paid) BETWEEN 1 AND 2, YEAR(Invoice.paid), YEAR(Invoice.paid) + 1) AS year',
			),
			'link' => ['Shop.Invoice'],
			'conditions' => [],
			'group' => 'paid_day',
			'order' => 'Invoice.paid DESC',
		));
		$totals = [];
		$stats = [
			'min' => 0,
			'max' => 0,
			'min_day' => null,
			'max_day' => null,
		];
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
				$totals[$year] = [
					'day' => [],
					'total' => 0,
				];
			}
			$totals[$year]['day'][$day] = $total;
			$totals[$year]['total'] += $total;
		}
		$this->set(compact('totals', 'stats'));
	}
	
	function _findFilter($options = []) {
		$search = ['shipped', 'paid', 'canceled'];
		$named = $this->request->named;
		if (isset($named['canceled'])) {
			$options['conditions']['Order.canceled'] = round($named['canceled']);
		}
		if (isset($named['paid'])) {
			$options['link']['Shop.Invoice'] = [];
			if ($named['paid']) {
				$options['conditions']['NOT']['Invoice.paid'] = null;
			} else {
				$options['conditions']['Invoice.paid'] = null;
			}
		}
		if (isset($named['shipped'])) {
			if ($named['shipped']) {
				$options['conditions']['NOT']['Order.shipped'] = null;
			} else {
				$options['conditions']['Order.shipped'] = null;
			}
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
	
	function _setFindModelAttrs($defaults = []) {
		return array_merge($defaults, [
			'method' => 'findOrder',
			'passIdToMethod' => true,
		]);
	}
	
	function _setFindModelOptions($options = []) {
		return array_merge([
			'fields' => '*',
			'link' => ['Shop.Invoice'],
			'postContain' => [
				'PromoCode',
				'OrderProduct' => [
					'link' => ['Shop.Product'],
				]
			],
		], $options);
	}
	
	function _setFormElements() {
		$states = $this->Order->State->selectList();
		$countries = $this->Order->Country->selectList();
		$this->set(compact('states', 'countries'));
		
		if (!empty($this->request->params['prefix']) && $this->request->params['prefix'] == 'admin') {
			$this->set('products', $this->Order->OrderProduct->Product->selectList());
			$this->set('shippingMethods', $this->Order->ShippingMethod->selectList());
			$this->set('invoicePaymentMethods', $this->Order->Invoice->InvoicePaymentMethod->selectList());
		}
	}
	
	function _beforeFindModel($options = []) {
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
		} else {
			$this->redirectHome();
		}
		return $result;
	}
}