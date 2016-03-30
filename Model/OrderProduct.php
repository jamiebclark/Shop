<?php
class OrderProduct extends ShopAppModel {
	public $name = 'OrderProduct';
	public $actsAs = ['Shop.BlankDelete' => 'quantity'];
	public $belongsTo = [
		'Shop.Product', 
		/*'ParentProduct' => [
			'className' => 'Shop.Product',
			'foreignKey' => 'parent_product_id',
		],
		*/
		'ParentOrderProduct' => [
			'className' => 'Shop.OrderProduct',
			'foreignKey' => 'parent_id',
		],
		'Order' => [
			'className' => 'Shop.Order',
			'counterCache' => true,
		],
	//	'Shop.ProductInventory'
	];
	
	public $hasMany = [
		'OrderProductsShippingRule' => [
			'className' => 'Shop.OrderProductsShippingRule',
			'dependent' => true
		],
		'ChildOrderProduct' => [
			'className' => 'Shop.OrderProduct',
			'foreignKey' => 'parent_id',
			'dependent' => true,
		]
	];
	public $hasAndBelongsToMany = ['Shop.ShippingRule',];
	public $recursive = -1;
	
	public $validate = [
		'product_id' => [
			'rule' => 'notBlank',
			'message' => 'You must select a product to add to your shopping cart',
		],
		'quantity' => [
			'numeric' => [
				'rule' => 'numeric',
				'message' => 'Please enter only a number',
			],
			'notBlank' => [
				'rule' => 'notBlank',
				'message' => 'Please enter a quantity',
			]
		]
	];
	
	public $order = array(
		'IF($ALIAS.parent_id IS NULL, $ALIAS.id, $ALIAS.parent_id)' => 'ASC', 	//Sorts by parent_id
		'IF($ALIAS.parent_id IS NULL, 0, 1)' => 'ASC',							//Puts parents at the top of the list
	);		
	
	var $current;
	
	var $packageChild;
	var $updatedChild = [];
	
	public function beforeValidate($options = []) {
		$data =& $this->getData();
		
		if (empty($data)) {
			return parent::beforeValidate($options);
		}
		
		// If only catalog_item_id is passed, finds the appropriate product ID
		if (empty($data['product_id'])) {
			if (!empty($this->data['Product']['catalog_item_id'])) {
				if (!$this->setProductIdFromData($this->data)) {
					$this->invalidate('product_id', 'Please select all options');
					return false;
				}
			} else {
				$this->invalidate('product_id', 'Select a product first');
				return false;
			}
		}
		
		for ($i = 1; $i <= $this->Product->optionChoiceCount; $i++) {
			$field = 'product_option_choice_id_' . $i;
			if (isset($data[$field])) {
				if (empty($data[$field])) {
					$this->invalidate($field, 'Please select all options');
				}
			} else {
				break;
			}
		}
		
		if (!empty($data['parent_id'])) {
			$parent = $this->find('first', ['conditions' => [
				"{$this->alias}.id" => $data['parent_id']
			]]);
			//debug([$parent, $data['parent_id'], $data]);
			$data['quantity'] = $parent[$this->alias]['quantity'];
			if (isset($data['package_quantity'])) {
				$data['quantity'] *= $data['package_quantity'];
			}
		}
		
		// Checks if product already exists in the cart
		$this->quantityExists($this->data);
		
		/*
		//Stores package children options for later
		if (!empty($data['PackageChild'])) {
			$this->packageChild = $data['PackageChild'];
		}
		*/
		
		$inventoryConditions = [];
		$catalogItem = $this->Product->findCatalogItem($data['product_id']);
		if (!empty($catalogItem)) {
			/*if (!empty($data['id']) && $data['quantity'] == 0) {
				$this->create();
				$this->delete($data['id']);
				unset($data);
				return true;
			} else */if ($data['quantity'] < $catalogItem['CatalogItem']['min_quantity']) {
				$this->invalidate('quantity', 'Please enter a quantity of at least ' . $catalogItem['CatalogItem']['min_quantity']);
			}
		}
		
		//Makes sure there is enough inventory to handle the order
		if (!$this->Product->checkStock($data['product_id'], $data['quantity'])) {
			$this->invalidate('quantity', 'Sorry, there is not enough inventory to meet that order request');
		}
		
		return parent::beforeValidate($options);		
	}
	
	public function beforeSave($options = []) {
		$data =& $this->getData();
		if (!empty($data['id']) && in_array($data['id'], $this->updatedChild)) {
			$this->data = [];
		}
		return parent::beforeSave($options);
	}
	
	public function invalidate($field, $message = true) {
		if (!empty($this->debug)) {
			debug($message);
		}
		return parent::invalidate($field, $message);
	}
	
	public function afterSave($created, $options = []) {
		$id = $this->id;

		$result = $this->find('first', [
			'fields' => '*',
			'link' => ['Shop.ParentOrderProduct' => [
				'conditions' => ['ParentOrderProduct.id = ' . $this->alias . '.parent_id']
			]],
			'conditions' => [$this->alias . '.id' => $id]
		]);
		
		// Finds order if it's a package child
		if (empty($result[$this->alias]['order_id']) && !empty($result[$this->alias]['parent_id'])) {
			$this->updateAll(
				[$this->alias . '.order_id' => $result['ParentOrderProduct']['order_id']],
				[$this->alias . '.id' => $id]
			);
		}
		$order = $this->Order->find('first', [
			'fields' => ['Order.*', 'Invoice.*'],
			'link' => ['Shop.OrderProduct', 'Shop.Invoice'],
			'conditions' => ['OrderProduct.id' => $id]
		]);
		// Updates information from Product
		if (empty($order['Order']['archived'])) {
			// Updates Order total, provided auto-pricing has not been turned off
			if ($order['Order']['auto_price']) {
				$this->productSync($id);
			}
			// Updates Shipping, provided auto-shipping has not been turned off
			if ($order['Order']['auto_shipping']) {
				$this->updateShipping($id);
			}
		}
		$this->updateProductStock($id);
		$this->updatePackageChildren($id);
		$this->updateTotal($id);
		
		return parent::afterSave($created);
	}
	
	public function beforeDelete($cascade = true) {
		$this->current = $this->read(null, $this->id);
		return parent::beforeDelete($cascade);
	}
	
	public function afterDelete() {
		if (!empty($this->current[$this->alias]['order_id'])) {
			$this->Order->updateTotal($this->current[$this->alias]['order_id']);
		}
		//$this->Product->updateStock($this->current[$this->alias]['product_id']);
		$this->updateProductStock($this->id);
		
		//If deleted item as a package, deletes all package elements
		$this->deleteAll([$this->alias . '.parent_id' => $this->id]);
		return parent::afterDelete();
	}
	
	/**
	 * Updates Order Line Item based on information from Product
	 *
	 **/
	public function productSync($id) {
		$conditions = [
			$this->alias . '.id' => $id,
			$this->alias . '.archived' => 0,
		];
		$result = $this->find('first', [
			'fields' => ["{$this->alias}.*", 'Product.*', 'CatalogItem.*'],
			'link' => ['Shop.Product' => [
				'conditions' => ["{$this->alias}.product_id = Product.id"],
				'Shop.CatalogItem'
			]],
		] + compact('conditions'));
		
		if (empty($result)) {
			return false;
		}
		$title = $result['Product']['title'];
		$cost = $result['CatalogItem']['cost'];
		
		if ($result[$this->alias]['parent_id']) {
			$price = 0;
		} else if (isset($result['CatalogItem']['sale']) && $result['CatalogItem']['sale'] > 0) {
			$price = $result['CatalogItem']['sale'];
		} else {
			$price = $result['CatalogItem']['price'];
		}
		$this->create();
		return $this->save(compact('id', 'title', 'price', 'cost'), ['callbacks' => false, 'validate' => false]);
	}
	
	public function updateShipping($id = null) {
		$shipping = 0;
		
		$result = $this->read(null, $id);
		if (empty($result[$this->alias]['parent_id'])) {
			//This selectes the FIRST rule found, not multiple
			$shippingRule = $this->ShippingRule->find('first', array(
				'fields' => array(
					'ShippingRule.amt + ShippingRule.per_item * OrderProduct.quantity + ShippingRule.pct * (OrderProduct.quantity * OrderProduct.price) AS shipping',
				),
				'link' => ['Shop.OrderProduct'],
				'conditions' => array(
					'OrderProduct.id' => $id,
					'ShippingRule.active' => 1,
					'(ShippingRule.min_quantity <= OrderProduct.quantity OR ShippingRule.min_quantity IS NULL)',
					'(ShippingRule.max_quantity >= OrderProduct.quantity OR ShippingRule.max_quantity IS NULL)',
				),
				'order' => [
					'ShippingRule.max_quantity DESC', 
					'ShippingRule.min_quantity DESC'
				]
			));
			if (!empty($shippingRule)) {
				$shipping = $shippingRule[0]['shipping'];
			}
		}
		return $this->updateAll(compact('shipping'), [$this->alias . '.id' => $id]);
	}
	
	function updateTotal($id = null) {
		$fields = $this->read(['parent_product_id', 'quantity', 'price', 'shipping'], $id);
		//Non-Shipping Total
		if (!empty($fields[$this->alias]['parent_product_id'])) {
			$sub_total = 0;
			$total = 0;
		} else {
			$sub_total = $fields[$this->alias]['quantity'] * $fields[$this->alias]['price'];
			$total = $sub_total + $fields[$this->alias]['shipping'];
		}
		$this->updateAll(compact('sub_total', 'total'), [$this->alias . '.id' => $id]);
		
		$this->Order->updateTotal($this->field('order_id'));
	}
	
	function findProductTotal($productId) {
		$result = $this->find('first', array(
			'fields' => array("SUM({$this->alias}.quantity) AS total"),
			'link' => ['Shop.Order'],
			'conditions' => [
				$this->alias . '.product_id' => $productId,
				'Order.canceled' => 0,
				'Order.archived' => 1,
			]
		));
		$total = 0;
		if (!empty($result[0]['total'])) {
			$total = $result[0]['total'];
		}
		return $total;
	}
	
	/**
	 * Before adding a new item to the cart, check to see if that same item type exists already
	 * If yes, then it will add the new quantity to the old quantity
	 *
	 **/
	function &quantityExists(&$oData) {
		if (isset($oData[$this->alias])) {
			$data =& $oData[$this->alias];
		} else {
			$data =& $oData;
		}
		if (empty($data['order_id']) && !empty($oData['Order']['id'])) {
			$data['order_id'] = $oData['Order']['id'];
		}
		if (!empty($data['order_id']) && empty($data['id'])) {
			$result = $this->find('first', array(
				'conditions' => array(
					$this->alias . '.order_id' => $data['order_id'],
					$this->alias . '.product_id' => $data['product_id'],
					$this->alias . '.parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null,
				)
			));
			if (!empty($result)) {
				$this->id = $result[$this->alias]['id'];
				$data['id'] = $result[$this->alias]['id'];
				$data['quantity'] += $result[$this->alias]['quantity'];

				if (isset($oData['ChildOrderProduct'])) {
					foreach ($oData['ChildOrderProduct'] as &$childData) {
						$childData['parent_id'] = $data['id'];
						$childData['quantity'] = $data['quantity'];
						if (!empty($childData['package_quantity'])) {
							$childData['quantity'] *= $childData['package_quantity'];
						}
						$this->quantityExists($childData);
					}
				}
			}
		}
		return $oData;
	}

	function updatePackageChildren($id) {
		$result = $this->read(null, $id);
		$result = $result[$this->alias];
		
		return $this->updateAll([
			$this->alias . '.quantity' => "{$this->alias}.package_quantity * {$result['quantity']}",
		], [
			$this->alias . '.parent_id' => $id,
		]);
		return true;
		
		
		$result = $this->find('first', [
			'fields' => '*',
			'link' => ['Shop.Product' => ['Shop.CatalogItem']],
			'conditions' => [$this->alias . '.id' => $id],
		]);
		$quantity = $result[$this->alias]['quantity'];

		//Finds any children already in the order
		$orderProductChildren = $this->find('all', [
			'fields' => '*',
			'link' => ['Shop.Product' => ['Shop.CatalogItem']],
			'conditions' => [$this->alias . '.parent_id' => $id]
		]);
		
		$existing = $existingTotals = [];
		if (!empty($orderProductChildren)) {
			foreach ($orderProductChildren as $orderChild) {
				$catalogItemId = $orderChild['CatalogItem']['id'];
				$productId = $orderChild['Product']['id'];
				$existing[$catalogItemId][$productId] = $orderChild[$this->alias]['id'];
				if (empty($existingTotals[$catalogItemId])) {
					$existingTotals[$catalogItemId] = 0;
				}
				$existingTotals[$catalogItemId] += $orderChild[$this->alias]['quantity'];
			}
		}
		$catalogItemChildren = $this->Product->CatalogItem->findPackageChildren($result['CatalogItem']['id']);
		
		if (!empty($catalogItemChildren)) {
			$data = [];
			foreach ($catalogItemChildren as $catalogItemChild) {
				$catalogItemId = $catalogItemChild['CatalogItem']['id'];
				$entry = [
					'parent_id' => $id,
					'parent_catalog_tem_id' => $catalogItemId,
					'quantity' => $quantity * $catalogItemChild['CatalogItemPackageChild']['quantity'],
				];
				if (!empty($existing[$catalogItemId])) {
					foreach ($existing[$catalogItemId] as $productId => $orderProductId) {
						$packageQty = $catalogItemChild['CatalogItemPackageChild']['quantity'];
						$totalFraction = $packageQty * $quantity / $existingTotals[$catalogItemId];
						$data[] = array(
							'id' => $orderProductId,
							'product_id' => $productId,
							'quanity' => ($quantity * $packageQty * $totalFraction),
						) + $entry;
					}
					unset($existing[$catalogItemId]);
				} else {
					$data[] = $entry;
				}
				if (!empty($this->packageChild[$catalogItemId])) {
					foreach ($this->packageChild[$catalogItemId] as $childField => $childVal) {
						$entry[$childField] = $childVal;
					}
				}
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
			$this->deleteAll([$this->alias . '.id' => $existing]);
		}
	}
	
	/**
	 * Finds the product ID based on catalog item ID and option choices
	 * Updates the passed data file with the new ID
	 *
	 * @param array $data The request data
	 * @return boolean On success
	 */
	public function setProductIdFromData(&$data) {
		if (isset($data[$this->alias])) {
			$modelData =& $data[$this->alias];
		} else {
			$modelData =& $data;
		}
		if (!empty($data['Product']) && $productId = $this->Product->findProductIdFromData($data['Product'])) {
			$modelData['product_id'] = $productId;
			$data['Product']['id'] = $productId;
			if (!empty($data['ChildOrderProduct'])) {
				foreach ($data['ChildOrderProduct'] as &$child) {
					if ($childProductId = $this->Product->findProductIdFromData($child)) {
						$child['product_id'] = $childProductId;
					} else {
						return false;
					}
				}
			}
			return true;
		}
		return false;
	}
	
	function updateProductStock($id) {
		if ($result = $this->read('product_id', $id)) {
			return $this->Product->updateStock($result[$this->alias]['product_id']);
		}
		return null;
	}
}
