<?php
App::uses('OrderEmail', 'Shop.Network/Email');
App::uses('ShopAppModel', 'Shop.Model');
class Order extends ShopAppModel {
	var $name = 'Order';
	var $displayField = 'title';
	var $actsAs = array(
		'Layout.DateValidate',
		'Location.Mappable',
		'Shop.InvoiceSync' => array(
			'title' => 'Store Order',
			'fields' => array(
			//	'paid',
				'total' => 'amt',
			),
		),
	);	
	var $order = array('Order.created DESC');
	var $recursive = -1;
	var $virtualFields = array('title' => 'CONCAT("Order #", $ALIAS.id)');
	
	var $hasMany = array(
		'OrderProduct' => array(
			'className' => 'Shop.OrderProduct',
			'dependent' => true
		),
		'OrdersHandlingMethod' => array(
			'className' => 'Shop.OrdersHandlingMethod',
			'dependent' => true
		),
		'OrdersPromoCode' => array(
			'className' => 'Shop.OrdersPromoCode',
			'dependent' => true
		),
	);
	var $belongsTo = array(
		'Shop.Invoice', 
		'ShippingMethod' => array(
			'className' => 'Shop.ShippingMethod',
		),
	);
	var $hasAndBelongsToMany = array(
		'Shop.PromoCode', 
		'HandlingMethod' => array(
			'className' => 'Shop.HandlingMethod',
			'with' => 'Shop.OrdersHandlingMethod',
		)
	);

	var $validate = array(
		'first_name' => array(
			'rule' => 'notEmpty',
			'message' => 'Please enter a first name',
		),
		'last_name' => array(
			'rule' => 'notEmpty',
			'message' => 'Please enter a last name',
		)
	);
	
	//Tracks from beforeSave to afterSave whether a confirmation email should be sent
	private $sendShippedEmail = false;	
	
	function beforeSave() {
		$data =& $this->getData();
		if (empty($data['country'])) {
			$data['country'] = 'US';
		}
		
		if (!empty($data['send_shipped_email'])) {
			$this->sendShippedEmail = true;
		}

		return parent::beforeSave();
	}
	
	
	function afterSave($created) {
		$this->updateTotal($this->id);
		
		$order = $this->find('first', array(
			'fields' => '*',
			'link' => array('Shop.Invoice'),
			'conditions' => array($this->alias . '.id' => $this->id)
		));
		
		//Updates invoice with billing address if set
		if (!empty($order[$this->alias]['same_billing'])) {
			$this->setSameBilling($this->id);
		}

		//Archives or Un-Archives order products based on payment and shipping status
		$archived = round(!empty($order['Invoice']['paid']) || !empty($order['Order']['shipped']));
		$this->updateAll(compact('archived'), array($this->alias . '.id' => $this->id));
		$this->OrderProduct->updateAll(compact('archived'), array('OrderProduct.order_id' => $this->id));
		$this->updateProductStock($this->id);
		
		if ($this->sendShippedEmail && !empty($order['Order']['shipped'])) {
			$this->sendShippedEmail($this->id);
		}
		return parent::afterSave($created);
	}
	
	function updateProductStock($id) {
		$orderProducts = $this->OrderProduct->find('all', array(
			'fields' => 'OrderProduct.product_id',
			'link' => array($this->alias),
			'conditions' => array($this->alias . '.id' => $id),
			'group' => 'OrderProduct.product_id',
		));
		foreach ($orderProducts as $orderProduct) {
			$this->OrderProduct->Product->updateStock($orderProduct['OrderProduct']['product_id']);
		}
	}
	
	function updateTotal($id = null) {
		//Finds sub-total first
		$subTotal = $this->findSubTotal($id);

		$result = $this->read(null, $id);
		
		if (empty($result[$this->alias]['archived']) && !empty($result[$this->alias]['auto_handling'])) {
			$this->updateHandling($id);
		}
		
		$totals = array();
		//Product Totals
		$options = array(
			'fields' => array(
				'SUM(OrderProduct.sub_total) AS sub_total',
				'SUM(OrderProduct.shipping) AS shipping',
			),
			'link' => array('Shop.OrderProduct'),
			'group' => $this->alias . '.id',
		);
		$options['conditions']['Order.id'] = $id;
		$result = $this->find('first', $options);
		$totals += $result[0];
		
		$options = array(
			'fields' => array(
				'SUM(OrdersHandlingMethod.amt + OrdersHandlingMethod.pct * ' . $subTotal . ') AS handling',
				'-1 * SUM(OrdersPromoCode.amt + OrdersPromoCode.pct * ' . $subTotal . ') AS promo_discount',
			),
			'link' => array('Shop.OrdersHandlingMethod', 'Shop.OrdersPromoCode'),
			'group' => $this->alias . '.id',
		);
		$options['conditions']['Order.id'] = $id;
		$result = $this->find('first', $options);
		//debug($result);
		$totals += $result[0];
		
		$totals['sub_total'] = $subTotal;

		$total = array_sum($totals);
		$totals['total'] = $total;
		
		return $this->updateAll($totals, array($this->alias . '.id' => $id));
	}
	
	function findProductOptions($id = null) {
		$products = $this->OrderProduct->Product->find('list', array(
			'link' => array('Shop.OrderProduct' => 'Shop.' . $this->alias),
			'conditions' => array($this->alias . '.id' => $id)
		));
		return $products;
	}
	
	function updateHandling($id = null) {
		//Removes de-activated or deleted handling rules
		$this->OrdersHandlingMethod->removeUnused();
		
		$handlingIds = $this->OrdersHandlingMethod->find('list', array(
			'fields' => array('HandlingMethod.id', 'OrdersHandlingMethod.id'),
			'link' => array('Shop.HandlingMethod'),
			'conditions' => array('OrdersHandlingMethod.order_id' => $id)
		));

		$handlingMethods = $this->HandlingMethod->find('all', array(
			'conditions' => array('HandlingMethod.active' => 1)
		));
		$data = array();
		foreach ($handlingMethods as $handlingMethod) {
			$insert = array(
				'handling_method_id' => $handlingMethod['HandlingMethod']['id'],
				'order_id' => $id,
				'title' => $handlingMethod['HandlingMethod']['title'],
				'amt' => $handlingMethod['HandlingMethod']['amt'],
				'pct' => $handlingMethod['HandlingMethod']['pct'],
			);
			if (!empty($handlingIds[$handlingMethod['HandlingMethod']['id']])) {
				$insert['id'] = $handlingIds[$handlingMethod['HandlingMethod']['id']];
			}
			$data[] = $insert;
		}
		return $this->OrdersHandlingMethod->saveAll($data);
	}
	
	function findSubTotal($id) {
		$options = array(
			'fields' => array(
				'Order.id', 
				'SUM(OrderProduct.sub_total) AS sub_total',
			),
			'conditions' => array('Order.id' => $id),
			'link' => array('Shop.Order'),
			'group' => 'Order.id'
		);
		$result = $this->OrderProduct->find(!empty($id) ? 'first' : 'all', $options);
		return !empty($result) ? $result[0]['sub_total'] : 0;
	}

	function findOrder($id) {
		return $this->find('first', array(
			'fields' => '*',
			'contain' => array(
				'Invoice', 'ShippingMethod',
				'OrdersHandlingMethod', 'OrdersPromoCode',
				'OrderProduct' => array(
					'Product' => array('CatalogItem'),
				)
			),
			'conditions' => array('Order.id' => $id)
		));
	}
	
	public function setSameBilling($id) {
		$fields = array(
			'first_name', 'last_name', 'addline1', 'addline2', 'city', 'state', 'zip', 'country',
			'email', 'phone',
		);
		$result = $this->read(null, $id);
		if (!empty($result[$this->alias]['same_billing'])) {
			return $this->copyModelToInvoice($id, $fields);
		}
		return null;
	}
	
	function sendShippedEmail($id) {
		$order = $this->findOrder($id);
		$Email = new OrderEmail();
		if ($Email->sendShipped($order) !== false) {
			$this->sendShippedEmail = false;
			return $this->updateAll(
				array("{$this->alias}.shipped_email" => 'NOW()'), 
				array("{$this->alias}.id" => $id)
			);
		}
		return false;
	}

	/*OLD FIND ORDER
	function findOrder($id) {
		$order = $this->find('first', array(
			'fields' => '*',
			'link' => array('Shop.Invoice', 'Shop.ShippingMethod'),
			'postContain' => array('OrdersHandlingMethod', 'OrdersPromoCode'),				
			'conditions' => array('Order.id' => $id)
		));
		if (empty($order)) {
			return false;
		}
		$orderProducts = $this->OrderProduct->find('all', array(
			'contain' => array(
				'Product' => array('CatalogItem'
				'ParentProduct',
				'ProductOptionChoice1', 'ProductOptionChoice2', 'ProductOptionChoice3', 'ProductOptionChoice4',
			),
			'conditions' => array(
				'OrderProduct.order_id' => $id,
			)
		));
		foreach ($orderProducts as $k => $orderProduct) {
			$order['OrderProduct'][$k] = $orderProduct['OrderProduct'];
			unset($orderProduct['OrderProduct']);
			$order['OrderProduct'][$k] += $orderProduct;
		}
		return $order;
	}
	
	*/
}
