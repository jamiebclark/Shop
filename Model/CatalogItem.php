<?php
class CatalogItem extends ShopAppModel {
	var $name = 'CatalogItem';
	var $actsAs = array('Shop.SelectList');
	var $recursive = 0;
	
	var $order = array('$ALIAS.active DESC', '$ALIAS.hidden', '$ALIAS.title');
	
	var $hasMany = array(
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
		//Any package which the product is part of
		'CatalogItemPackageParent' => array(
			'className' => 'Shop.CatalogItemPackage',
			'foreignKey' => 'product_child_id',
			'dependent' => true,
		),
		//Any package which the product is parent of
		'CatalogItemPackageChild' => array(
			'className' => 'Shop.CatalogItemPackage',
			'foreignKey' => 'product_parent_id',
			'dependent' => true,
		),
	);
	var $hasAndBelongsToMany = array('Shop.CatalogItemCategory');
	
	function afterSave($created) {
		$products = $this->Product->find('list', array(
			'link' => array('Shop.' . $this->alias),
			'conditions' => array($this->alias . '.id' => $this->id)
		));
		foreach ($products as $productId => $productTitle) {
			$this->Product->updateTitle($productId);
		}
		return parent::afterSave($created);	
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
			'link' => array(				'Shop.CatalogItemPackageChild' => array('Shop.CatalogItemParent' => array('table' => 'products'))			),
			'conditions' => array('CatalogItemParent.id' => $id)
		));
	}
}
