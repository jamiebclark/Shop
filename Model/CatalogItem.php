<?php
class CatalogItem extends ShopAppModel {
	var $name = 'CatalogItem';
	var $actsAs = array(
		'Shop.SelectList',
	//	'Shop.BlankDelete' => array('title'),
	);
	var $recursive = 0;
	
	var $order = array('$ALIAS.active DESC', '$ALIAS.hidden', '$ALIAS.title');
	var $hasMany = array(
		'Product' => array(
			'className' => 'Shop.Product',
			'dependent' => true,
		),
		'CatalogItemImage' => array(
			'className' => 'Shop.CatalogItemImage',
			'dependent' => true
		),
		'CatalogItemOption' => array(
			'className' => 'Shop.CatalogItemOption',
			'dependent' => true
		),
		'ShippingRule' => array(
			'className' => 'Shop.ShippingRule',
			'dependent' => true
		),
		//Any package which the catalog item is part of
		'CatalogItemPackageParent' => array(
			'className' => 'Shop.CatalogItemPackage',
			'foreignKey' => 'catalog_item_child_id',
			'dependent' => true,
		),
		//Any package which the catalog item is parent of
		'CatalogItemPackageChild' => array(
			'className' => 'Shop.CatalogItemPackage',
			'foreignKey' => 'catalog_item_parent_id',
		),
	);
	var $hasAndBelongsToMany = array('Shop.CatalogItemCategory');
	
	var $validate = array(
		'title' => array(
			'rule' => 'notEmpty',
			'message' => 'Please give your item a title',
		),
		'price' => array(
			'rule' => 'notEmpty',
			'message' => 'Please enter a price for the item',
		)
	);
	
	function beforeSave($options = array()) {
		debug($this->data);
		debug($options);
		return parent::beforeSave($options);
	}
	
	function afterSave($created) {
		$id = $this->id;
		debug($this->find('first', array('contain' => array('CatalogItemCategory'), 'conditions' => array($this->alias . '.id' => $id))));
		
		$this->createProducts($id);
		$this->updateProductTitles($id);
		$this->read(null, $id);
		return parent::afterSave($created);	
	}
	
/**
 * Updates the titles of the catalog item's associated products
 *
 * @param int $id Catalog Item Id
 * @return bool Success
 **/
	public function updateProductTitles($id) {
		$products = $this->Product->find('list', array(
			'link' => array('Shop.' . $this->alias),
			'conditions' => array($this->alias . '.id' => $id)
		));
		foreach ($products as $productId => $productTitle) {
			$this->Product->updateTitle($productId);
		}
		return true;
	}
	
/**
 * Finds all possible combinations of options of the catalog item
 * Saves each one in the product table
 *
 * @param int $id Catalog Item Id
 * @return bool Success
 **/
	public function createProducts($id) {
		$result = $this->find('first', array(
			'contain' => array('Product'),
			'conditions' => array($this->alias . '.id' => $id)
		));
		$count = $this->Product->optionChoiceCount;
		$indexes = $this->CatalogItemOption->findCatalogItemIndexes($id);
		$data = $indexData = array();
		foreach ($indexes as $index => $indexVals) {
			$oData = $data;
			$indexData = array();
			foreach ($indexVals as $key => $val) {
				$add = array($index => $key);
				if (empty($oData)) {
					$indexData[] = $add;
				} else {
					foreach ($oData as $dataKey => $oldDataRow) {
						$indexData[] = $add + $oldDataRow;
					}
				}
			}
		}
		if (empty($indexData)) {
			$indexData[] = array();
		}
		foreach ($indexData as $k => $indexVals) {
			$data[$k] = array('catalog_item_id' => $id);
			for ($i = 1; $i <= $count; $i++) {
				$data[$k]['product_option_choice_id_' . $i] = null;
				if (isset($indexVals[$i])) {
					$data[$k]['product_option_choice_id_' . $i] = $indexVals[$i];
				}
			}
		}
		foreach ($result['Product'] as $product) {
			foreach ($data as &$dataRow) {
				for ($i = 1; $i <= $count; $i++) {
					$key = 'product_option_choice_id_' . $i;
					if ($dataRow[$key] != $product[$key]) {
						continue 2;
					}					
				}
				$dataRow['id'] = $product['id'];
				continue 2;
			}
		}
		return $this->Product->saveAll($data, array('callbacks' => false));
	}
	
	function findCategories($id) {
		$categories = $this->CatalogItemCategory->find('all', array(
			'link' => array('Shop.CatalogItem'),
			'conditions' => array('CatalogItem.id' => $id)
		));
		$options = array('order' => 'CatalogItemCategory.lft DESC', 'conditions' => array());
		$conditions = array();
		$ids = array();
		foreach ($categories as $category) {
			$options['conditions']['OR'][]['AND'] = array(
				'CatalogItemCategory.lft <=' => $category['CatalogItemCategory']['lft'],
				'CatalogItemCategory.rght >=' => $category['CatalogItemCategory']['rght'],
			);
			$ids[$category['CatalogItemCategory']['id']] = $category['CatalogItemCategory']['id'];
		}
		$categories = $this->CatalogItemCategory->find('all', $options);
		$return = $returnSorted = array();
		foreach ($categories as $category) {
			$id = $category['CatalogItemCategory']['id'];
			$parentId = $category['CatalogItemCategory']['parent_id'];
			if (isset($ids[$id])) {
				$return[][$parentId] = $category;
			}
			foreach ($return as $key => $list) {
				if (isset($list[$id])) {
					$return[$key][$parentId] = $category;
				}
			}
		}
		foreach ($return as $key => $categories) {
			$categories = array_reverse($categories);
			foreach ($categories as $category) {
				$returnSorted[$key][$category['CatalogItemCategory']['id']] = $category['CatalogItemCategory']['title'];
			}
		}
		return $returnSorted;
	}
	
	/*
	function updateStock($id) {
		$result = $this->Product->ProductInventory->find('first', array(
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
	}
	*/
	
	function findPackageChildren($id) {
		return $this->CatalogItemPackageChild->CatalogItemChild->find('all', array(
			'fields' => array('*'),
			'link' => array(				'Shop.CatalogItemPackageChild' => array(
					'Shop.CatalogItemParent' => array('table' => 'catalog_items')
				)			),
			'conditions' => array('CatalogItemParent.id' => $id)
		));
	}
}
