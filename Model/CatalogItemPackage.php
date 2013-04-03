<?php
class CatalogItemPackage extends ShopAppModel {
	var $name = 'CatalogItemPackage';
	var $actsAs = array(
		'Shop.BlankDelete' => array('or' => array(
			'catalog_item_parent_id', 'catalog_item_child_id', 'quantity'
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
}
