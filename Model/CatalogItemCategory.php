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
			$options['conditions'][$this->alias . '.lft BETWEEN ? AND ?'] = $this->findLeftRight($id);
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
			$options['conditions'][$this->alias . '.lft BETWEEN ? AND ?'] = $this->findLeftRight($id);
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
			return null;
		}
		return array($result[$this->alias]['lft'], $result[$this->alias]['rght']);
	}
	
	function findActiveCategories($parentId = null) {
		//Finds any categories with active, non-hidden products associated
		$conditions = array(
			'CatalogItem.active' => 1,
			'CatalogItem.hidden' => 0,
		);
		if (!empty($parentId) && ($result = $this->read(array('lft', 'rght'), $parentId))) {
			$conditions[$this->alias . '.lft BETWEEN ? AND ?'] = array(
				$result[$this->alias]['lft'], $result[$this->alias]['rght']
			);
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
