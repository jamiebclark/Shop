<?php
class Product extends ShopAppModel {
	var $name = 'Product';
	var $actsAs = array('Shop.SelectList');
	var $hasMany = array (
		'Shop.OrderProduct',
		'ProductInventoryAdjustment' => array(
			'className' => 'Shop.ProductInventoryAdjustment',
			'dependent' => true,
		)		
	);
	var $belongsTo = array('Shop.CatalogItem');
	
	// The amount of different types of catalog item options there can be for a single catalog item
	public $optionChoiceCount = 4;
	
	// Tracks the Catalog Item ID of a deleted Product from beforeDelete to afterDelete
	private $deletedProductCatalogItemId;
	
	function __construct($id = false, $table = null, $ds = null) {
		if (!empty($this->optionChoiceCount)) {
			for ($i = 1; $i <= $this->optionChoiceCount; $i++) {
				$this->belongsTo['ProductOptionChoice' . $i] = array(
					'className' => 'Shop.ProductOptionChoice',
					'foreignKey' => 'product_option_choice_id_' . $i,
				);
			}
		}
		parent::__construct($id, $table, $ds);
	}
	
	function afterSave($created, $options = array()) {
		if ($created) {
			$id = $this->id;
			$this->updateTitle($id);
			$this->read(null, $id);
		}
		return parent::afterSave($created);
	}
	
	function beforeDelete($cascade = true) {
		// Finds the Catalog Item ID to be updated after the deletion
		$result = $this->read('catalog_item_id', $this->id);
		$this->deletedProductCatalogItemId = $result[$this->alias]['catalog_item_id'];
		return parent::beforeDelete($cascade);
	}
	
	function afterDelete() {
		// Updates the stock of the Catalog Item now Product has been deleted
		if (!empty($this->deletedProductCatalogItemId)) {
			$this->CatalogItem->updateStock($this->deletedProductCatalogItemId);
		}
		return parent::afterDelete();
	}

	function findCatalogItem($id, $options = array()) {
		$options['link'] = array('Shop.Product');
		$options['conditions']['Product.id'] = $id;
		return $this->CatalogItem->find('first', $options);
	}

/**
 * Finds the product ID based on a catalog item ID and option choices
 * passed through data
 *
 * @param array $data the request data containing catalog_item_id and additional option choices
 * @param boolean $overwrite If false and product_id already exists, it will return that
 * @return int/boolean ID if found, false if not
 **/
	public function findProductIdFromData($data, $overwrite = false) {
		if (!empty($data['product_id']) && !$overwrite) {
			return $data['product_id'];
		}
		$conditions = array(
			$this->escapeField('catalog_item_id') => $data['catalog_item_id'],
			$this->escapeField('active') => 1,
		);
		for ($i = 1; $i <= $this->optionChoiceCount; $i++) {
			$key = 'product_option_choice_id_' . $i;
			$conditions["{$this->alias}.$key"] = isset($data[$key]) ? $data[$key] : null;
		}
		$result = $this->find('first', 
			array('fields' => $this->alias.'.*') + compact('conditions'));
		return !empty($result) ? $result[$this->alias]['id'] : false;
	}
	
	function adjustStock($id, $amt) {
		return $this->updateAll(
			array($this->escapeField('stock') => $this->escapeField('stock') . ' + ' . $amt),
			array($this->escapeField('id') => $id)
		);
	}
	
	function checkStock($id, $quantity = 1) {
		$this->updateStock($id);	//Temporary
		$result = $this->read(null, $id);
		$catalogItemId = $result[$this->alias]['catalog_item_id'];
		/*
		//If the product is a package, check all the child elements in the package
		$catalogItemChildren = $this->CatalogItem->findPackageChildren($catalogItemId);
		if (!empty($catalogItemChildren)) {
//			debug($catalogItemChildren);

			foreach ($catalogItemChildren as $key => $catalogItemChild) {
			debug(array(
				'Found Children',
				$catalogItemChild['CatalogItemChild']['id'], 
				$catalogItemChild['CatalogItemPackageChild']['quantity'] * $quantity
			));
				if (!$this->checkStock(
					$catalogItemChild['CatalogItemChild']['id'], 
					$catalogItemChild['CatalogItemPackageChild']['quantity'] * $quantity
				)) {
					//$this->invalidate('ProductChild.' . $key . '.quantity', 'Not 
					return false;
				}
			}
			return true;
		}
		*/
		$result = $this->CatalogItem->find('first', array(
			'fields' => '*', //$this->alias . '.quantity',
			'link' => array('Shop.Product'),
			'conditions' => array(
				'CatalogItem.id' => $catalogItemId,
				'OR' => array(
					'AND' => array('Product.active' => 1, 'Product.id' => $id),
					'CatalogItem.unlimited' => 1,
				)
			)
		));
		if (empty($result[$this->alias]['stock'])) {
			$result[$this->alias]['stock'] = 0;
		}
		$checkQuantity = $result[$this->alias]['stock'] - $quantity;
		if (!empty($result['CatalogItem']['unlimited'])) {
			return true;
		} else if (!empty($checkQuantity)) {
			return $checkQuantity > 0;
		} else {
			return false;
		}
	}

	function updateStock($id) {
		$added = $this->ProductInventoryAdjustment->findProductTotal($id);
		$bought = $this->OrderProduct->findProductTotal($id);
		$stock = $added - $bought;
		$this->create();
		$success = $this->save(compact('id', 'stock'));
		// Updates parent CatalogItem stock total
		$result = $this->read('catalog_item_id', $id);
		$this->CatalogItem->updateStock($result[$this->alias]['catalog_item_id']);
		return $success;
		/*
		$this->find('first', array(
			'link' => array(
				'Shop.ProductInventoryAdjustment' => array(
					'type' => 'LEFT',
					'conditions' => array(
						'ProductInventoryAdjustment.product_id = ' . $this->alias . '.id',
						'ProductInventoryAdjustment.available <=' => date('Y-m-d H:i:s'),
					)
				),
				'Shop.ProductOrder' => array(
					'type' => 'LEFT',
					'Shop.Order' => array(
						'type' => 'INNER',
						'conditions' => array(
							'Order.id = ProductOrder.order_id',
							'Order.canceled' => 0
				),
			),
			'conditions' => array(
				$this->alias . '.id' => $id,
			),
		));
		$result = $this->ProductInventory->find('first', array(
			'fields' => 'SUM(IF(ProductInventory.quantity < 0, 0, ProductInventory.quantity)) AS stock',
			'joins' => array(
				array(
					'table' => 'products',
					'type' => 'LEFT',
					'alias' => 'ProductChild',
					'conditions' => array('ProductChild.id = ProductInventory.product_id'),
				), array(
					'table' => 'product_packages',
					'type' => 'LEFT',
					'alias' => 'ProductPackageChild',
					'conditions' => array('ProductPackageChild.product_child_id = ProductChild.id'),
				),
			),
			'conditions' => array(
				'OR' => array(
					'ProductPackageChild.product_parent_id' => $id,
					'ProductChild.id' => $id,
				)
			),
			//'group' => 'Product.id',
		));
		$stock = !empty($result) ? $result[0]['stock'] : 0;
		return $this->updateAll(array(
			$this->alias . '.stock' => $stock,
		), array(
			$this->alias. '.id' => $id,
		));
		*/
	}

/**
 * Finds instances of existing product option choices with missing catalog item options
 *
 **/
	function updateMissingCatalogItemOptions() {
		//Finds missing Product Option Choice
		$options = array('fields' => '*', 'recursive' => -1);
		for ($i = 1; $i <= $this->optionChoiceCount; $i++) {
			$class = 'ProductOptionChoice' . $i;
			$key = "{$this->alias}.product_option_choice_id_$i";
			$options['joins'][] = array(
				'type' => 'LEFT',
				'alias' => $class,
				'table' => 'product_option_choices',
				'conditions' => array("$class.id = $key"),
			);
			$options['conditions']['OR'][] = array($class . '.id' => null, "$key <>" => null);
		}
		if ($result = $this->find('all', $options)) {
			$data = array();
			foreach ($result as $row) {
				for ($i = $this->optionChoiceCount; $i >=1; $i--) {
					$key = $row['Product']['catalog_item_id'] . '-' . $i;
					$index = "product_option_choice_id_$i";
					$id = $row[$this->alias][$index];
					
					if (!empty($id)) {
						if (empty($data[$key])) {
							$data[$key] = array(
								'CatalogItemOption' => array(
									'catalog_item_id' => $row['Product']['catalog_item_id'],
									'index' => $i,
								)
							);
						}
						if (isset($row[$this->alias][$index])) {
							$data[$key]['ProductOptionChoice'][$id] = compact('id');
							break;
						}
					}
				}
			}
			$data = array_values($data);
			foreach ($data as &$val) {
				$val['ProductOptionChoice'] = array_values($val['ProductOptionChoice']);
			}
			if (!empty($data)) {
			$this->CatalogItem->CatalogItemOption->saveAll($data, array('callbacks' => false, 'validate' => false, 'deep' => true));
			}
		}	
		
		// Finds missing CatalogItemOptions
		$data = array();
		$options = array('fields' => '*', 'recursive' => -1);
		for ($i = 1; $i <= $this->optionChoiceCount; $i++) {
			$class = 'ProductOptionChoice' . $i;
			$optionClass = 'CatalogItemOption' . $i;
			$options['joins'][] = array(
				'type' => 'LEFT',
				'alias' => $class,
				'table' => 'product_option_choices',
				'conditions' => array($class . '.id = ' . $this->alias . '.product_option_choice_id_' . $i),
			);
			$options['joins'][] = array(
				'type' => 'LEFT',
				'alias' => $optionClass,
				'table' => 'catalog_item_options',
				'conditions' => array("$optionClass.id = $class.catalog_item_option_id"),
			);
			$options['conditions']['OR'][] = array($class . '.id <>' => null, $optionClass . '.id' => null);
		}
		
		if ($result = $this->find('all', $options)) {
			foreach ($result as $row) {
				for ($i = $this->optionChoiceCount; $i >=1; $i--) {
					if (isset($row['ProductOptionChoice' . $i]['id'])) {
						$id = $row['ProductOptionChoice' . $i]['catalog_item_option_id'];
						$data[$id] = array(
							'catalog_item_id' => $row['Product']['catalog_item_id'],
							'id' => $id
						);
						break;
					}
				}
			}
			if (!empty($data)) {
				return $this->CatalogItem->CatalogItemOption->saveAll(array_values($data));
			}
		}
		return null;
	}
	
	function updateTitle($id) {
		$fields = array('CatalogItem.title');
		$link = array('Shop.CatalogItem');
		$conditions = array($this->alias . '.id' => $id);
		$classes = $joins = array();
		for ($i = 1; $i <= $this->optionChoiceCount; $i++) {
			$class = 'ProductOptionChoice' . $i;
			$fields[] = $class . '.title';
			$joins[] = array(
				'type' => 'LEFT',
				'alias' => $class,
				'table' => 'product_option_choices',
				'conditions' => array($class . '.id = ' . $this->alias . '.product_option_choice_id_' . $i),
			);
			$classes[] = $class;
		}
		$result = $this->find('first', compact('fields', 'joins', 'link', 'conditions'));
		$title = $result['CatalogItem']['title'];
		$subTitle = '';
		foreach ($classes as $class) {
			if (!empty($result[$class]['title'])) {
				if (!empty($subTitle)) {
					$subTitle .= ', ';
				}
				$subTitle .= $result[$class]['title'];
			}
		}
		if (!empty($subTitle)) {
			$title .= ': '. $subTitle;
		}
		$this->create();
		$data = compact('id', 'title') + array('sub_title' => $subTitle);
		if ($success = $this->save($data)) {
			$this->OrderProduct->updateAll(
				array('OrderProduct.title' => $this->getDataSource()->value($title)),
				array('OrderProduct.product_id' => $id, 'OrderProduct.archived' => 0)
			);
		}
		return $success;
	}

/**
 * Looks for duplicate products with the exact same production option choice and combines them
 *
 **/
	function combine($catalogItemId = null) {
		$options = array('order' => $this->alias . '.catalog_item_id');
		if (!empty($catalogItemId)) {
			$options['conditions'] = array($this->alias . '.catalog_item_id' => $catalogItemId);
		}
		$result = $this->find('all', $options);
		$combine = array();
		foreach ($result as $row) {
			$key = $row[$this->alias]['catalog_item_id'];
			for ($i = 1; $i <= $this->optionChoiceCount; $i++) {
				$key .= '-' . $row[$this->alias]['product_option_choice_id_' . $i];
			}
			$combine[$key][] = $row[$this->alias]['id'];
		}
		foreach ($combine as $key => $ids) {
			if (count($ids) > 1) {
				$keepId = array_pop($ids);
				foreach ($this->hasMany as $model => $attrs) {
					if ($this->{$model}->updateAll(
						array("$model.product_id" => $keepId),
						array("$model.product_id" => $ids)
					)) {
						$this->{$model}->deleteAll(array("$model.product_id" => $ids));
					}
				}
				$this->deleteAll(array($this->alias . '.id' => $ids));
			}
		}
	}
	
	function selectList($options = array()) {
		$options['contain']['CatalogItem'] = array();
		$result = $this->find('all', $options);
		$select = array('' => ' --- Select a Product --- ', 'Active' => array(), 'Inactive' => array());
		foreach ($result as $row) {
			$key = empty($row['CatalogItem']) || empty($row['CatalogItem']['active']) ? 'Inactive' : 'Active';
			$title = !empty($row[$this->alias]['title']) ? $row[$this->alias]['title'] : $row['CatalogItem']['title'];
			if (empty($row['CatalogItem']['unlimited'])) {
				$title .= sprintf(' (%d)', $row[$this->alias]['stock']);
			}
			$select[$key][$row[$this->alias]['id']] = $title;
		}
		return $select;
	}
}