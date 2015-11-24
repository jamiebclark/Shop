<?php
App::uses('OrderEmail', 'Shop.Network/Email');
App::uses('ShopAppModel', 'Shop.Model');
class Order extends ShopAppModel {
	public $name = 'Order';
	public $displayField = 'title';
	public $actsAs = [
		'Layout.DateValidate',
		'Location.Mappable' => ['validate' => true],
		'Shop.InvoiceSync' => [
			'title' => 'Store Order',
			'fields' => [
			//	'paid',
				'total' => 'amt',
			],
		],
		'Shop.EmailTrigger' => [
			'send_shipped_email' => 'sendShippedEmail',
		//	'send_paid_email' => 'sendPaidEmail',
		],
	];	
	public $order = ['Order.created' => 'DESC'];
	public $recursive = -1;
	public $virtualFields = array('title' => 'CONCAT("Order #", $ALIAS.id)');
	
	public $hasMany = [
		'OrderProduct' => [
			'className' => 'Shop.OrderProduct',
			'dependent' => true
		],
		'OrdersHandlingMethod' => [
			'className' => 'Shop.OrdersHandlingMethod',
			'dependent' => true
		],
		'OrdersPromoCode' => [
			'className' => 'Shop.OrdersPromoCode',
			'dependent' => true
		],
	];
	public $belongsTo = [
		'Shop.Invoice', 
		'ShippingMethod' => ['className' => 'Shop.ShippingMethod'],
	];
	public $hasAndBelongsToMany = [
		'Shop.PromoCode', 
		'HandlingMethod' => [
			'className' => 'Shop.HandlingMethod',
			'with' => 'Shop.OrdersHandlingMethod',
		]
	];

	public $validate = [
		'first_name' => [
			'rule' => 'notEmpty',
			'message' => 'Please enter a first name',
		],
		'last_name' => [
			'rule' => 'notEmpty',
			'message' => 'Please enter a last name',
		]
	];
	
	//Tracks from beforeSave to afterSave whether a confirmation email should be sent
	private $sendShippedEmail = false;	
	
	const DELETE_EMPTY_DEADLINE = '-2 months';
	
	public function beforeFind($query) {
		$oQuery = $query;
		if (!empty($query['fields']) && ($key = array_search('id', $query['fields'])) !== false) {
			unset($query['fields'][$key]);
		}

		if ($oQuery != $query) {
			return $query;
		}

		return parent::beforeFind($query);
	}

	public function beforeSave($options = []) {
		$data =& $this->getData();
		if (empty($data['country'])) {
			$data['country'] = 'US';
		}
		return parent::beforeSave($options);
	}
	
	
	public function afterSave($created, $options = []) {
		$id = $this->id;
		$this->updateTotal($id);
		
		$order = $this->read(null, $id);
		//Updates invoice with billing address if set
		if (!empty($order[$this->alias]['same_billing'])) {
			$this->setSameBilling($id);
		}
		$this->updateArchived($id);
		$this->updateProductStock($id);
		
		$this->read(null, $id);

		return parent::afterSave($created);
	}
	
	public function beforeDelete($cascade = true) {
		$this->_deletedOrderProducts = $this->OrderProduct->find('list', [
			'fields' => [
				'OrderProduct.id',
				'OrderProduct.product_id',
			],
			'conditions' => ['OrderProduct.order_id' => $this->id]
		]);
		return parent::beforeDelete($cascade);
	}
	
	public function afterDelete() {
		//Makes sure to update product stocks after being deleted
		if (!empty($this->_deletedOrderProducts)) {
			foreach ($this->_deletedOrderProducts as $orderProductId => $productId) {
				$this->OrderProduct->Product->updateStock($productId);
			}
		}
		return parent::afterDelete();
	}
	
	public function afterCopyInvoiceToModel($id, $invoiceId) {
		$this->updateArchived($id);
		$this->updateProductStock($id);
		return parent::afterCopyInvoiceToModel($id, $invoiceId);
	}
	
	public function addPromoCode($id, $promoCodeId) {
		$promoCode = $this->PromoCode->findActiveCode($promoCodeId);
		if (empty($promoCode)) {
			throw new Exception('Invalid promo code');
			return false;
		}
		$promoData = [
			'order_id' => $id,
			'promo_code_id' => $promoCode['PromoCode']['id'],
		];

		$result = $this->OrdersPromoCode->find('first', [
			'conditions' => [
				'OrdersPromoCode.promo_code_id' => $promoCode['PromoCode']['id'],
				'OrdersPromoCode.order_id' => $id,
			]
		]);
		if (!empty($result)) {
			$promoData['id'] = $result['OrdersPromoCode']['id'];
		}
		$promoData = $this->OrdersPromoCode->getCopyData($promoCode, $promoData);

		$data = [
			'Order' => compact('id'),
			'OrdersPromoCode' => $promoData,
		];

		$success = $this->OrdersPromoCode->save($promoData, ['validate' => false]);

		if (!$success) {
			throw new Exception('Could not save promo code');
		} else {
			$this->updateTotal($id);
			return $success;
		}
	}


	//Checks if an order should be marked as archived
	public function updateArchived($id) {
		$order = $this->find('first', array(
			'fields' => '*',
			'link' => ['Shop.Invoice'],
			'conditions' => array($this->escapeField('id') => $id)
		));
		
		//Archives or Un-Archives order products based on payment and shipping status
		$archived = round(!empty($order['Invoice']['paid']) || !empty($order['Order']['shipped']));

		$this->updateAll(compact('archived'), array($this->escapeField('id') => $id));
		$this->OrderProduct->updateAll(compact('archived'), ['OrderProduct.order_id' => $id]);
		return true;
	}
	
	public function updateProductStock($id) {
		$orderProducts = $this->OrderProduct->find('all', array(
			'fields' => 'OrderProduct.product_id',
			'link' => [$this->alias],
			'conditions' => array($this->escapeField('id') => $id),
			'group' => 'OrderProduct.product_id',
		));
		foreach ($orderProducts as $orderProduct) {
			$this->OrderProduct->Product->updateStock($orderProduct['OrderProduct']['product_id']);
		}
	}
	
	public function updateTotal($id = null) {
		//Finds sub-total first
		$subTotal = $this->findSubTotal($id);

		$result = $this->read(null, $id);
		
		if (empty($result[$this->alias]['archived']) && !empty($result[$this->alias]['auto_handling'])) {
			$this->updateHandling($id);
		}
		
		$data = [];
		//Product Totals
		$query = array(
			'fields' => array(
				'SUM(OrderProduct.sub_total) AS sub_total',
				'SUM(OrderProduct.shipping) AS shipping',
			),
			'link' => ['Shop.OrderProduct'],
			'group' => $this->escapeField('id'),
		);
		$query['conditions']['Order.id'] = $id;
		$result = $this->find('first', $query);
		$data += $result[0];
		
		$query = array(
			'fields' => array(
				'SUM(OrdersHandlingMethod.amt + OrdersHandlingMethod.pct * ' . $subTotal . ') AS handling',
				'-1 * SUM(OrdersPromoCode.amt + OrdersPromoCode.pct * ' . $subTotal . ') AS promo_discount',
			),
			'link' => ['Shop.OrdersHandlingMethod', 'Shop.OrdersPromoCode'],
			'group' => $this->escapeField('id'),
		);
		$query['conditions']['Order.id'] = $id;
		$result = $this->find('first', $query);
		//debug($result);
		$data += $result[0];
		

		$data['sub_total'] = $subTotal;

		$total = array_sum($data);
		$data['total'] = $total;
		$data[$this->primaryKey] = $id;
		foreach ($data as $k => $v) {
			if (empty($v)) {
				$data[$k] = 0;
			}
		}

		
		$this->create();
		$this->save($data, ['callbacks' => false]);

		// Updates Invoice
		$this->copyModelToInvoice($id);
		return true;
	}
	
	public function findProductOptions($id = null) {
		$products = $this->OrderProduct->Product->find('list', array(
			'link' => ['Shop.OrderProduct' => 'Shop.' . $this->alias],
			'conditions' => array($this->escapeField('id') => $id)
		));
		return $products;
	}
	
	public function updateHandling($id = null) {
		//Removes de-activated or deleted handling rules
		$this->OrdersHandlingMethod->removeUnused();
		
		$handlingIds = $this->OrdersHandlingMethod->find('list', [
			'fields' => ['HandlingMethod.id', 'OrdersHandlingMethod.id'],
			'link' => ['Shop.HandlingMethod'],
			'conditions' => ['OrdersHandlingMethod.order_id' => $id]
		]);

		$handlingMethods = $this->HandlingMethod->find('all', [
			'conditions' => ['HandlingMethod.active' => 1]
		]);
		$data = [];
		foreach ($handlingMethods as $handlingMethod) {
			$insert = [
				'handling_method_id' => $handlingMethod['HandlingMethod']['id'],
				'order_id' => $id,
				'title' => $handlingMethod['HandlingMethod']['title'],
				'amt' => $handlingMethod['HandlingMethod']['amt'],
				'pct' => $handlingMethod['HandlingMethod']['pct'],
			];
			if (!empty($handlingIds[$handlingMethod['HandlingMethod']['id']])) {
				$insert['id'] = $handlingIds[$handlingMethod['HandlingMethod']['id']];
			}
			$data[] = $insert;
		}
		return $this->OrdersHandlingMethod->saveAll($data);
	}
	
	public function findSubTotal($id) {
		$options = array(
			'fields' => array(
				'Order.id', 
				'SUM(OrderProduct.sub_total) AS sub_total',
			),
			'conditions' => ['Order.id' => $id],
			'link' => ['Shop.Order'],
			'group' => 'Order.id'
		);
		$result = $this->OrderProduct->find(!empty($id) ? 'first' : 'all', $options);
		return !empty($result) ? $result[0]['sub_total'] : 0;
	}

	public function findOrder($id) {
		$query = [
			'fields' => '*',
			'contain' => [
				'Invoice', 
				'ShippingMethod',
				'OrdersHandlingMethod', 'OrdersPromoCode',
				'OrderProduct' => ['Product' => ['CatalogItem']],
				'PromoCode',
			],
			'conditions' => [$this->escapeField('id') => $id]
		];
		return $this->find('first', $query);
	}
	
	public function setSameBilling($id) {
		$fields = [
			'first_name', 'last_name', 'addline1', 'addline2', 'city', 'state', 'zip', 'country',
			'email', 'phone',
		];
		$result = $this->read(null, $id);
		if (!empty($result[$this->alias]['same_billing'])) {
			return $this->copyModelToInvoice($id, $fields);
		}
		return null;
	}
	
	public function sendShippedEmail($id) {
		$result = $this->findOrder($id);
		$Email = new OrderEmail();
		if (!empty($result[$this->alias]['shipped']) && ($Email->sendShipped($result) !== false)) {
			return $this->updateAll(
				array($this->escapeField('shipped_email') => 'NOW()'), 
				array($this->escapeField('id') => $id)
			);
		}
		return false;
	}
	
	public function sendPaidEmail($id) {
		$result = $this->findOrder($id);
		$Email = new OrderEmail();
		if (!empty($result['Invoice']['paid']) && ($Email->sendPaid($result) !== false)) {
			return $this->updateAll(
				array($this->escapeField('paid_email') => 'NOW()'), 
				array($this->escapeField('id') => $id)
			);
		}
		return false;	
	}

	public function deleteOldEmptyOrders() {
		$db = $this->Invoice->getDataSource();
		$orders = $this->find('all', array(
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => $db->fullTableName($this->Invoice),
					'alias' => 'Invoice',
					'conditions' => array($this->escapeField('invoice_id') . ' = Invoice.id'),
				),
			),
			'conditions' => ['Invoice.model <>' => 'Shop.Model']
		));
		if (!empty($orders)) {
			$ids = Hash::extract($orders, 'Order.{n}.id');
		}
		$this->updateAll(
			array($this->escapeField('invoice_id') => null), 
			array($this->escapeField() => $ids)
		);


		return $this->deleteAll(array(
			$this->escapeField('paid') => null,
			$this->escapeField('shipped') => null,
			'OR' => array(
				'AND' => array(
					// Abandoned Orders
					$this->escapeField('total') . ' >' => 0,
					$this->escapeField('created') . ' <' => date('Y-m-d H:i:s', strtotime(self::DELETE_EMPTY_DEADLINE))
				),
				'AND' => array(
					// Empty Orders
					$this->escapeField('total') => 0,
					$this->escapeField('created') . ' <' => date('Y-m-d H:i:s', strtotime('-2 days'))
				)
			)
		));
	}
	
	/*OLD FIND ORDER
	function findOrder($id) {
		$order = $this->find('first', [
			'fields' => '*',
			'link' => ['Shop.Invoice', 'Shop.ShippingMethod'],
			'postContain' => ['OrdersHandlingMethod', 'OrdersPromoCode'],				
			'conditions' => ['Order.id' => $id]
		]);
		if (empty($order)) {
			return false;
		}
		$orderProducts = $this->OrderProduct->find('all', [
			'contain' => [
				'Product' => ['CatalogItem'
				'ParentProduct',
				'ProductOptionChoice1', 'ProductOptionChoice2', 'ProductOptionChoice3', 'ProductOptionChoice4',
			],
			'conditions' => [
				'OrderProduct.order_id' => $id,
			]
		]];
		foreach ($orderProducts as $k => $orderProduct) {
			$order['OrderProduct'][$k] = $orderProduct['OrderProduct'];
			unset($orderProduct['OrderProduct']);
			$order['OrderProduct'][$k] += $orderProduct;
		}
		return $order;
	}
	
	*/
}
