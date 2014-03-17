<?php
App::uses('ShopAppModel', 'Shop.Model');
class CatalogItemCategory extends ShopAppModel {
	public $name = 'CatalogItemCategory';
	public $actsAs = array(
		'Tree', 
		'Shop.SelectList',
		'Shop.Sluggable',
	);
	public $hasAndBelongsToMany = array('Shop.CatalogItem');
	public $order = array('$ALIAS.lft' => 'DESC');
	
	public function findCatalogItems($id, $deep = true, $scope = false, $admin = false) {
		return $this->CatalogItem->find('all', $this->findCatalogItemsOptions($id, $deep, $admin));
	}
	
	public function findChildren($id, $deep = true, $admin = false) {
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
	
	public function getPath($id, $rootId = null, $fields = null, $recursive = null) {
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
	
	public function checkScope($id, $rootId) {
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
	
	public function findCatalogItemsOptions($id, $deep = true, $scope = false, $admin = false) {
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
	
	public function idSlugCheck($id) {
		if (!is_numeric($id)) {
			$result = $this->find('list', array(
				'fields' => array($this->escapeField('id'), $this->escapeField('id')),
				'conditions' => array($this->escapeField('slug') . ' LIKE' => $id),
			));
			if (empty($result)) {
				return null;
			}
			$id = array_pop($result);
		}
		return $id;
	}
	
	public function findLeftRight($id) {
		if (!($result = $this->read(array('lft', 'rght'), $id))) {
			return array(null, null);
		}
		return array($result[$this->alias]['lft'], $result[$this->alias]['rght']);
	}
	
	public function findActiveCategories($parentId = null) {
		//Finds any categories with active, non-hidden products associated
		return $this->find('all', array(
			'conditions' => array(
				$this->escapeField('parent_id') => $parentId,
				$this->escapeField('active_catalog_item_count') . ' > 0',
			)
		));
	}
	
	public function updateTotals() {
		$alias = $this->alias;
		$active = array(
			'CatalogItem.hidden = 0',
			'CatalogItem.active = 1',
			'CatalogItem.stock > 0',
		);
		
		$options = array(
			'recursive' => -1,
			'fields' => array(
				$this->escapeField('id'),
				'COUNT(DISTINCT(CatalogItem.id)) AS `catalog_item_count`',
				'COUNT(DISTINCT(ActiveCatalogItem.id)) AS `active_catalog_item_count`',
			), 
			'joins' => array(
				array(
					'table' => 'catalog_item_categories',
					'alias' => 'ChildCatalogItemCategory',
					'conditions' => array("ChildCatalogItemCategory.lft BETWEEN $alias.lft AND $alias.rght"),
				), 
				
				array(
					'table' => 'catalog_item_categories_catalog_items',
					'alias' => 'CatalogItemCategoriesCatalogItem',
					'type' => 'LEFT',
					'conditions' => array("CatalogItemCategoriesCatalogItem.catalog_item_category_id = ChildCatalogItemCategory.id"),
				), 
				array(
					'table' => 'catalog_items',
					'alias' => 'CatalogItem',
					'type' => 'LEFT',
					'conditions' => array('CatalogItem.id = CatalogItemCategoriesCatalogItem.catalog_item_id')
				), 
				
				array(
					'table' => 'catalog_item_categories_catalog_items',
					'alias' => 'ActiveCatalogItemCategoriesCatalogItem',
					'type' => 'LEFT',
					'conditions' => array("ActiveCatalogItemCategoriesCatalogItem.catalog_item_category_id = ChildCatalogItemCategory.id"),
				), 
				array(
					'table' => 'catalog_items',
					'alias' => 'ActiveCatalogItem',
					'type' => 'LEFT',
					'conditions' => $this->CatalogItem->publicConditions(
						array('ActiveCatalogItem.id = ActiveCatalogItemCategoriesCatalogItem.catalog_item_id'), 
						'ActiveCatalogItem'
					)
				)
			), 
			'group' => $this->escapeField('id'));
		$result = $this->find('all', $options);
		$data = array();
		foreach ($result as $row) {
			$data[] = array('id' => $row[$this->alias]['id']) + $row[0];
		}
		return $this->saveAll($data, array('validate' => false, 'callbacks' => false));
	}
}