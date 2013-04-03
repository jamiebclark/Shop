<?php
class OrderProduct extends ShopAppModel {
	var $name = 'OrderProduct';
	var $actsAs = array('Shop.BlankDelete' => 'quantity');
	var $belongsTo = array(
		'Shop.Product', 
		'ParentProduct' => array(
			'className' => 'Shop.Product',
			'foreignKey' => 'parent_product_id',
		),
		/*
		'ParentOrder' => array(
			'className' => 'OrderProduct',
			'foreignKey' => 'parent_id',
		),
		*/
		'Order' => array(
			'className' => 'Shop.Order',
			'counterCache' => true,
		),
		'Shop.ProductInventory'
	);
	
	var $hasMany = array(
		'OrderProductsShippingRule' => array(
			'className' => 'Shop.OrderProductsShippingRule',
			'dependent' => true
		),
		/*
		'ChildOrder' => array(
			'className' => 'OrderProduct',
			'foreignkey' => 'parent_id',
		)
		*/
	);
	var $hasAndBelongsToMany = array('Shop.ShippingRule',);
	var $recursive = -1;
	
	var $validate = array(
		'product_id' => array(
			'rule' => 'notEmpty',
			'message' => 'You must select a product to add to your shopping cart',
		),
		'quantity' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please enter only a number',
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a quantity',
			)
		)
	);
	
	var $current;
	
	var $packageChild;
	var $updatedChild = array();
	
	function beforeValidate() {
		$data =& $this->getData();
		//debug($data);
		//debugTrace($this->data);
		
		if (empty($data)) {
			return parent::beforeValidate();
		}
		if (empty($data['product_id'])) {
			$this->invalidate('product_id', 'Select a product first');
			return false;
		}
		
		//Stores package children options for later
		if (!empty($data['PackageChild'])) {
			$this->packageChild = $data['PackageChild'];
		}
		

		$inventoryConditions = array();
		$catalogItem = $this->Product->findCatalogItem($data['product_id']);
		if (!empty($catalogItem)) {
			if (!empty($data['id']) && $data['quantity'] == 0) {
				$this->create();
				$this->delete($data['id']);
				unset($data);
				return true;
			} else if ($data['quantity'] < $catalogItem['CatalogItem']['min_quantity']) {
				$this->invalidate('quantity', 'Please enter a quantity of at least ' . $catalogItem['CatalogItem']['min_quantity']);
			}
		}
		
		//Makes sure all options are selected
		$productOptions = $this->Product->ProductOption->find('all', array(
			'link' => array('Shop.Product'),
			'conditions' => array('Product.id' => $data['product_id'])
		));
		//$this->debug = true;
		if (!empty($productOptions)) {
			foreach ($productOptions as $productOption) {
				$key = 'product_option_choice_id_' . $productOption['ProductOption']['index'];
				if (empty($data[$key])) {
					$this->invalidate($key, 'Please select a ' . $productOption['ProductOption']['title']);
				} else {
					$inventoryConditions[$key] = $data[$key];
				}
			}
		}
		
		//Makes sure there is enough inventory to handle the order
		if (!$this->Product->ProductInventory->checkQuantity($data['product_id'], $data['quantity'], $inventoryConditions)) {
			$this->invalidate('quantity', 'Sorry, there is not enough inventory to meet that order request');
		} 
		return parent::beforeValidate();		
	}
	
	function beforeSave() {
		$data =& $this->getData();
		if (!empty($data['id']) && in_array($data['id'], $this->updatedChild)) {
			$this->data = array();
		}
		return parent::beforeSave();
	}
	
	function invalidate($field, $message) {
		if (!empty($this->debug)) {
			debug($message);
		}
		return parent::invalidate($field, $message);
	}
	
	function afterSave($created) {
		$id = $this->id;
		
		$order = $this->Order->find('first', array(
			'fields' => array('Order.*', 'Invoice.*'),
			'link' => array('Shop.OrderProduct', 'Shop.Invoice'),
			'conditions' => array('OrderProduct.id' => $id)
		));

		//Updates information from Product
		if (empty($order['Order']['archived'])) {
			if ($order['Order']['auto_price']) {
				$this->productSync($id);
			}
			//Updates Shipping, provided auto-shipping has not been turned off
			if ($order['Order']['auto_shipping']) {
				$this->updateShipping($id);
			}
		}
		$this->updateProductInventory($id);
		$this->updatePackageChildren($id);
		$this->updateTotal($id);

		return parent::afterSave($created);
	}
	
	function beforeDelete() {
		$this->current = $this->read(null, $this->id);
		return parent::beforeDelete();
	}
	
	function afterDelete() {
		if (!empty($this->current[$this->alias]['order_id'])) {
			$this->Order->updateTotal($this->current[$this->alias]['order_id']);
		}
		$this->ProductInventory->rebuildQuantity($this->current[$this->alias]['product_inventory_id']);
		$this->updateProductInventory($this->id);
		
		//If deleted item as a package, deletes all package elements
		$this->deleteAll(array($this->alias . '.parent_id' => $this->id));
		return parent::afterDelete();
	}
	
	/**
	 * Updates Order Line Item based on information from Product
	 *
	 **/
	function productSync($id) {
		$conditions = array(
			$this->alias . '.id' => $id,
			$this->alias . '.archived' => 0,
		);
		$result = $this->find('first', array(
			'fields' => array('Product.*', 'CatalogItem.*'),
			'link' => array('Shop.Product' => array('Shop.CatalogItem')),
		) + compact('conditions'));
		
		if (empty($result)) {
			return false;
		}
		$title = $result['Product']['title'];
		$cost = $result['CatalogItem']['cost'];
		if (isset($result['CatalogItem']['sale']) && $result['CatalogItem']['sale'] > 0) {
			$price = $result['CatalogItem']['sale'];
		} else {
			$price = $result['CatalogItem']['price'];
		}
		$this->create();
		return $this->save(compact('id', 'title', 'price', 'cost'));
	}
	
	function updateShipping($id = null) {
		$shipping = 0;
		
		//This selectes the FIRST rule found, not multiple
		$productShippingRule = $this->ProductShippingRule->find('first', array(
			'fields' => array(
				'ProductShippingRule.amt + ProductShippingRule.per_item * OrderProduct.quantity + ProductShippingRule.pct * (OrderProduct.quantity * OrderProduct.price) AS shipping',
			),
			'link' => array('Product' => array('OrderProduct')),
			'conditions' => array(
				'OrderProduct.id' => $id,
				'ProductShippingRule.active' => 1,
				'(ProductShippingRule.min_quantity <= OrderProduct.quantity OR ProductShippingRule.min_quantity IS NULL)',
				'(ProductShippingRule.max_quantity >= OrderProduct.quantity OR ProductShippingRule.max_quantity IS NULL)',
			),
			'order' => array(
				'ProductShippingRule.max_quantity DESC', 
				'ProductShippingRule.min_quantity DESC'
			)
		));
		
		if (!empty($productShippingRule)) {
			$shipping = $productShippingRule[0]['shipping'];
		}
		$this->updateAll(compact('shipping'), array($this->alias . '.id' => $id));
	}
	
	function updateTotal($id = null) {
		$fields = $this->read(array('parent_product_id', 'quantity', 'price', 'shipping'), $id);
		//Non-Shipping Total
		if (!empty($fields[$this->alias]['parent_product_id'])) {
			$sub_total = 0;
			$total = 0;
		} else {
			$sub_total = $fields[$this->alias]['quantity'] * $fields[$this->alias]['price'];
			$total = $sub_total + $fields[$this->alias]['shipping'];
		}
		$this->updateAll(compact('sub_total', 'total'), array($this->alias . '.id' => $id));
		
		$this->Order->updateTotal($this->field('order_id'));
	}
	
	function findProductTotal($productId) {
		$result = $this->find('first', array(
			'fields' => array('SUM('. $this->alias .'.quantity) AS total'),
			'link' => array('Shop.Order'),
			'conditions' => array(
				$this->alias . '.productId' => $productId,
				'Order.cancelled' => 0,
				'Order.archived' => 1,
			)
		));
		$total = 0;
		debug($result);
		if (!empty($result[$this->alias]['total'])) {
			$total = $result[$this->alias]['total'];
		}
		return $total;
	}
	
	/**
	 * Before adding a new item to the cart, check to see if that same item type exists already
	 * If yes, then it will add the new quantity to the old quantity
	 *
	 **/
	function quantityExists(&$data) {
		
		if (!empty($data['Order'])) {
			$data[$this->alias]['order_id'] = $data['Order'];
		}
		
		if (empty($data[$this->alias]['id'])) {
			$checkCols = array(
				'order_id', 
				'product_id', 
				'product_option_choice_id_1',
				'product_option_choice_id_2',
				'product_option_choice_id_3',
				'product_option_choice_id_4'
			);
			$conditions = array();
			foreach ($checkCols as $col) {
				$conditions[$col] = !empty($data[$this->alias][$col]) ? $data[$this->alias][$col] : null;
			}
			$result = $this->find('first', compact('conditions'));
			if (!empty($result)) {
				$this->id = $result[$this->alias]['id'];
				$data[$this->alias]['id'] = $result[$this->alias]['id'];
				$data[$this->alias]['quantity'] += $result[$this->alias]['quantity'];
			}
		}
		return $data;
	}

	function updatePackageChildren($id) {
		$result = $this->read(null, $id);
		$quantity = $result[$this->alias]['quantity'];
		
		
		//Finds any children already in the order
		$orderChildren = $this->find('all', array(
			'conditions' => array(
				$this->alias . '.parent_id' => $id,
			)
		));
		
		if (!empty($orderChildren)) {
			$existing = array();
			foreach ($orderChildren as $orderChild) {
				$key = $orderChild[$this->alias]['product_id'];
				$existing[$key] = $orderChild[$this->alias]['id'];
				$i = 1;
				while(isset($orderChild[$this->alias]['product_option_choice_id_' . $i])) {
					$field = 'product_option_choice_id_' . $i;
					if (!isset($this->packageChild[$key][$field])) {
						$this->packageChild[$key][$field] = $orderChild[$this->alias][$field];
					}
					$i++;
				}
			}
		}
		$productChildren = $this->Product->findPackageChildren($result[$this->alias]['product_id']);
		if (!empty($productChildren)) {
			$data = array();
			foreach ($productChildren as $productChild) {
				$entry = array();
				$key = $productChild['ProductChild']['id'];
				if (!empty($existing[$key])) {
					$entry['id'] = $existing[$key];
					unset($existing[$key]);
				} else {
					$entry['id'] = null;
				}
				$entry['parent_id'] = $id;
				$entry['product_id'] = $productChild['ProductChild']['id'];
				$entry['order_id'] = $result[$this->alias]['order_id'];
				$entry['parent_product_id'] = $result[$this->alias]['product_id'];
				$entry['quantity'] = $quantity * $productChild['ProductPackageChild']['quantity'];
				
				if (!empty($this->packageChild[$key])) {
					foreach ($this->packageChild[$key] as $childField => $childVal) {
						$entry[$childField] = $childVal;
					}
				}
				$data[] = $entry;
			}
			if (!empty($data)) {
				foreach ($data as $orderProductData) {
					$this->create();
					if ($this->save($orderProductData)) {
						$this->updatedChild[] = $this->id;
					}
				}
			}
		}
		
		//Existing entries not found in the package anymore are removed
		if (!empty($existing)) {
			$this->deleteAll(array($this->alias . '.id' => $existing));
		}
	}
	
	function updateProductInventory($id = null) {
		if (!empty($id)) {
			$conditions = array(
				$this->alias . '.id' => $id,
			);
		} else {
			$conditions = array();
		}
		
		$options = array(
			'fields' => array('*'),
			'link' => array(
				'ProductInventory' => array(
					'conditions' => array(
						$this->alias . '.product_id = ProductInventory.product_id',
						'(('.$this->alias.'.product_option_choice_id_1 IS NULL AND ProductInventory.product_option_choice_id_1 IS NULL) OR ' . $this->alias . '.product_option_choice_id_1 = ProductInventory.product_option_choice_id_1)',
						
						'(('.$this->alias.'.product_option_choice_id_2 IS NULL AND ProductInventory.product_option_choice_id_2 IS NULL) OR ' . $this->alias . '.product_option_choice_id_2 = ProductInventory.product_option_choice_id_2)',
						
						'(('.$this->alias.'.product_option_choice_id_3 IS NULL AND ProductInventory.product_option_choice_id_3 IS NULL) OR ' . $this->alias . '.product_option_choice_id_3 = ProductInventory.product_option_choice_id_3)',
						
						'(('.$this->alias.'.product_option_choice_id_4 IS NULL AND ProductInventory.product_option_choice_id_4 IS NULL) OR ' . $this->alias . '.product_option_choice_id_4 = ProductInventory.product_option_choice_id_4)',
					)
				)
			),
			'conditions' => $conditions
		);
		
		$result = $this->find('all', $options);
		
		$productInventories = array();
		if (!empty($result)) {
			foreach ($result as $row) {
				$productInventoryId = $row['ProductInventory']['id'];
				$modelId = $row[$this->alias]['id'];
				$this->updateAll(array(
					$this->alias . '.product_inventory_id' => $productInventoryId,
				), array(
					$this->alias . '.id' => $modelId,
				));
				if (empty($productInventories[$productInventoryId])) {
					$this->ProductInventory->rebuildQuantity($productInventoryId);
					$productInventories[$productInventoryId] = 1;
				}
			}
		} else {
			return null;
		}
	}
}
