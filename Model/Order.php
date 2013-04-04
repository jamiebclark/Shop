<?php
class Order extends ShopAppModel {
	var $name = 'Order';
	/*
	var $actsAs = array(
		'Location',
		'InvoiceSync' => array(
		//	'paid',
			'total' => 'amt',
		),
	);
	*/
	
	var $order = array('Order.created DESC');
	var $recursive = -1;
	
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
	var $hasAndBelongsToMany = array('Shop.PromoCode', 'Shop.HandlingMethod');

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
	
	function beforeSave() {
		$data =& $this->getData();
		if (empty($data['country'])) {
			$data['country'] = 'US';
		}
		return parent::beforeSave();
	}
	
	function afterSave($created) {
		$this->updateTotal($this->id);
		
		$order = $this->find('first', array(
			'fields' => '*',
			'link' => array('Invoice'),
			'conditions' => array('Order.id' => $this->id)
		));

		//Archives or Un-Archives order products based on payment and shipping status
		$archived = round(!empty($order['Invoice']['paid']) || !empty($order['Order']['shipped']));
		$this->updateAll(compact('archived'), array($this->alias . '.id' => $this->id));
		$this->OrderProduct->updateAll(compact('archived'), array('OrderProduct.order_id' => $this->id));
		$this->updateProductInventory($this->id);
		
		return parent::afterSave($created);
	}
	
	function updateProductInventory($id) {
		$orderProducts = $this->OrderProduct->find('list', array(
			'link' => array($this->alias),
			'conditions' => array(
				$this->alias . '.id' => $id,
			)
		));
		foreach ($orderProducts as $orderProductId => $orderProductTitle) {
			$this->OrderProduct->updateProductInventory($orderProductId);
		}
	}
	
	function updateTotal($id = null) {
		$result = $this->findSubTotal($id);
		//Finds sub-total first
		$subTotal = !empty($result[0]['sub_total']) ? round($result[0]['sub_total']) : 0;

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
			'link' => array('OrderProduct'),
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
			'link' => array('OrdersHandlingMethod', 'OrdersPromoCode'),
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
		$productOptions = array();
		foreach ($products as $productId => $productTitle) {
			$productOptions[$productId] = $this->OrderProduct->Product->ProductOption->findProductOptions($productId);
		}
		return $productOptions;
	}
	
	function updateHandling($id = null) {
		//Removes de-activated or deleted handling rules
		$this->OrdersHandlingMethod->deleteAll(array(
			'OrdersHandlingMethod.order_id' => $id,
			'OR' => array('HandlingMethod.active' => 0, 'HandlingMethod.id' => null)
		));
		
		$handlingIds = $this->OrdersHandlingMethod->find('list', array(
			'fields' => array('HandlingMethod.id', 'OrdersHandlingMethod.id'),
			'link' => array('HandlingMethod'),
			'conditions' => array('OrdersHandlingMethod.order_id' => $id)
		));

		$handlingMethods = $this->HandlingMethod->find('all', array(
			'conditions' => array('HandlingMethod.active' => 1)
		));
		$data = array();
		foreach ($handlingMethods as $handlingMethod) {
			$insert = array(
				'product_handling_id' => $handlingMethod['HandlingMethod']['id'],
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
	
	function findSubTotal($id = null) {
		$options = array(
			'fields' => array(
				'Order.id', 
				'SUM(OrderProduct.sub_total) AS sub_total',
			),
			'link' => array('Shop.Order'),
			'group' => 'Order.id'
		);
		if (!empty($id)) {
			$options['conditions']['Order.id'] = $id;
		}
		return $this->OrderProduct->find(!empty($id) ? 'first' : 'all', $options);
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