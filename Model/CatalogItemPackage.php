<?php
class CatalogItemPackage extends ShopAppModel {
	var $name = 'CatalogItemPackage';
	var $actsAs = array(
		'Shop.BlankDelete' => array('or' => array(
			'catalog_item_child_id', 'quantity'
		))
	);
	
	var $belongsTo = array(
		'CatalogItemParent' => array(
			'className' => 'Shop.CatalogItem',
			'foreignKey' => 'catalog_item_parent_id',
		),
		'CatalogItemChild' => array(
			'className' => 'Shop.CatalogItem',
			'foreignKey' => 'catalog_item_child_id',
		)
	);
	
	function afterSave($created, $options = array()) {
		$result = $this->read(null, $this->id);
		
		$this->CatalogItemParent->save(array(
			'id' => $result[$this->alias]['catalog_item_parent_id'],
			'is_package' => 1,
			'unlimited' => 1,
		));
		
		return parent::afterSave($created);
	}
}