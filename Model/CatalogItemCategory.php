<?php
class CatalogItemCategory extends ShopAppModel {
	var $name = 'CatalogItemCategory';
	var $actsAs = array(
		'Tree', 
		'Shop.SelectList',
		'Shop.Sluggable',
	);
	var $hasAndBelongsToMany = array('Shop.CatalogItem');
	var $order = array('$ALIAS.lft' => 'DESC');
	
	function findCatalogItems($id, $deep = true, $scope = false, $admin = false) {
		return $this->CatalogItem->find('all', $this->findCatalogItemsOptions($id, $deep, $admin));
	}
	
	function findChildren($id, $deep = true, $admin = false) {
		$options = array();
		if ($deep) {
			if ($leftRight = $this->findLeftRight($id)) {
				$options['conditions'][$this->alias . '.lft BETWEEN ? AND ?'] = $leftRight;
			}
		} else {
			$options['conditions'][$this->alias . '.parent_id'] = $id;
		}
		return $this->find('all', $options);
	}
	
	function getPath($id, $rootId = null, $fields = null, $recursive = null) {
		$path = parent::getPath($id, $fields, $recursive);
		if (empty($path)) {
			return null;
		}
		if (!empty($rootId)) {
			foreach ($path as $k => $row) {
				if ($row[$this->alias][$this->primaryKey] != $rootId) {
					unset($row[$k]);
				} else {
					break;
				}
			}
		}
		return array_values($path);
	}
	
	function checkScope($id, $rootId) {
		if (empty($id)) {
			return $rootId;
		}
		$id = $this->idSlugCheck($id);
		if ($id == $rootId) {
			return $id;
		}
		list($l, $r) = $this->findLeftRight($id);
		list($rootL, $rootR) = $this->findLeftRight($rootId);
		return $l > $rootL && $l < $rootR ? $id : false;
	}
	
	function findCatalogItemsOptions($id, $deep = true, $scope = false, $admin = false) {
		$id = $this->idSlugCheck($id);
		$options = array(
			'fields' => '*',
			'link' => array('Shop.' . $this->alias), 
			'conditions' => array(),
			'group' => 'CatalogItem.id',
		);
		if (!$admin) {
			$options['conditions'] += array('CatalogItem.hidden' => 0, 'CatalogItem.active' => 1);
		}
		if ($deep) {
			if ($leftRight = $this->findLeftRight($id)) {
				$options['conditions'][$this->alias . '.lft BETWEEN ? AND ?'] = $leftRight;
			}
		} else {
			$options['conditions'][$this->alias . '.id'] = $id;
		}
		return $options;			
	}
	
	function idSlugCheck($id) {
		if (!is_numeric($id)) {
			$result = $this->find('list', array(
				'fields' => array($this->alias . '.id', $this->alias . '.id'),
				'conditions' => array($this->alias . '.slug LIKE' => $id),
			));
			if (empty($result)) {
				return null;
			}
			$id = array_pop($result);
		}
		return $id;
	}
	
	function findLeftRight($id) {
		if (!($result = $this->read(array('lft', 'rght'), $id))) {
			return array(null, null);
		}
		return array($result[$this->alias]['lft'], $result[$this->alias]['rght']);
	}
	
	function findActiveCategories($parentId = null) {
		//Finds any categories with active, non-hidden products associated
		return $this->find('all', array(
			'conditions' => array(
				$this->escapeField('parent_id') => $parentId,
				$this->escapeField('active_catalog_item_count') . ' > 0',
			)
		));
	}
	
	function updateTotals() {
		$alias = $this->alias;
		$result = $this->find('all', array(
			'recursive' => -1,
			'fields' => array(
				$this->escapeField('id'),
				'COUNT(CatalogItem.id) AS `catalog_item_count`',
				'SUM(IF(CatalogItem.hidden = 0 AND CatalogItem.active = 1,1,0)) AS `active_catalog_item_count`',
			), 
			'joins' => array(
				array(
					'table' => 'catalog_item_categories',
					'alias' => 'ChildCatalogItemCategory',
					'conditions' => array("ChildCatalogItemCategory.lft BETWEEN $alias.lft AND $alias.rght"),
				), array(
					'table' => 'catalog_item_categories_catalog_items',
					'alias' => 'CatalogItemCategoriesCatalogItem',
					'conditions' => array("CatalogItemCategoriesCatalogItem.catalog_item_category_id = ChildCatalogItemCategory.id"),
				), array(
					'table' => 'catalog_items',
					'alias' => 'CatalogItem',
					'conditions' => array(
						'CatalogItem.id = CatalogItemCategoriesCatalogItem.catalog_item_id',
					)
				)
			), 
			'group' => $this->escapeField('id')));
		$data = array();
		foreach ($result as $row) {
			$data[] = array('id' => $row[$this->alias]['id']) + $row[0];
		}
		return $this->saveAll($data, array('validate' => false, 'callbacks' => false));
	}
}
