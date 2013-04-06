<?php
class Product extends ShopAppModel {
	var $name = 'Product';
	var $hasMany = array (
		'Shop.OrderProduct',
		'ProductInventoryAdjustment' => array(
			'className' => 'Shop.ProductInventoryAdjustment',
			'dependent' => true,
		)		
	);
	var $belongsTo = array('Shop.CatalogItem');
	
	var $optionChoiceCount = 4;
	
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
			$this->alias . '.catalog_item_id' => $data['catalog_item_id'],
			$this->alias . '.active' => 1,
		);
		for ($i = 1; $i <= $this->optionChoiceCount; $i++) {
			$key = 'product_option_choice_id_' . $i;
			$conditions["{$this->alias}.$key"] = isset($data[$key]) ? $data[$key] : null;
		}
		$result = $this->find('first', 
			array('fields' => $this->alias.'.id') + compact('conditions'));
		return !empty($result) ? $result[$this->alias]['id'] : false;
	}
	
	function adjustStock($id, $amt) {
		return $this->updateAll(
			array($this->alias . '.stock', $this->alias . '.stock + ' . $amt),
			array($this->alias . '.id' => $id)
		);
	}
	
	function checkStock($id, $quantity = 1) {
		$this->updateStock($id);	//Temporary
		
		$result = $this->read(null, $id);
		$catalogItemId = $result[$this->alias]['catalog_item_id'];
		
		//If the product is a package, check all the child elements in the package
		$catalogItemChildren = $this->CatalogItem->findPackageChildren($catalogItemId);
		if (!empty($catalogItemChildren)) {
//			debug($catalogItemChildren);
			foreach ($catalogItemChildren as $key => $catalogItemChild) {
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
		return $this->save(compact('id', 'stock'));
		
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
							'Order.cancelled' => 0
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
}