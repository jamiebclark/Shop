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
		$options['link'][$this->alias];
		$options['conditions'][$this->alias . '.id'] = $id;
		return $this->CatalogItem->find('first', $options);
	}

	function adjustStock($id, $amt) {
		return $this->updateAll(
			array($this->alias . '.stock', $this->alias . '.stock + ' . $amt),
			array($this->alias . '.id' => $id)
		);
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
		return $this->save($data);
	}
}