<?php
class CatalogItemCategory extends ShopAppModel {
	var $name = 'CatalogItemCategory';
	var $actsAs = array('Tree', 'Shop.SelectList');
	var $hasAndBelongsToMany = array('Shop.CatalogItem');
	var $order = array('$ALIAS.lft' => 'DESC');
	
	function findActiveCategories($parentId = null) {
		//Finds any categories with active, non-hidden products associated
		$conditions = array(
			'CatalogItem.active' => 1,
			'CatalogItem.hidden' => 0,
		);
		if (!empty($parentId)) {
			$result = $this->read(array('lft', 'rght'), $parentId);
			$conditions[$this->alias . '.lft BETWEEN ? AND ?'] = array($result[$this->alias]['lft'], $result[$this->alias]['rght']);
		}
		$this->create();
		//Finds all active categories
		$result = $this->find('list', array(
			'link' => array('Shop.CatalogItem' => array('type' => 'INNER')),
			'conditions' => $conditions,
			'group' => 'CatalogItemCategory.id',
		));
		if (!empty($result)) {
			return $this->find('list', array(
				'joins' => array(
					array(
						'table' => 'catalog_item_categories',
						'alias' => 'CatalogItemCategoryChild',
						'conditions' => array(
							'CatalogItemCategoryChild.lft BETWEEN CatalogItemCategory.lft AND CatalogItemCategory.rght',
							'CatalogItemCategoryChild.id' => array_keys($result),
						)
					)
				),
				'conditions' => array(
					$this->alias . '.parent_id' => $parentId,
				)
			));
		} else {
			return null;
		}
	}
}
