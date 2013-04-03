<?php
class CatalogItemPackagesCatalogItem extends ShopAppModel {
	var $name = 'CatalogItemPackagesCatalogItem';
	var $actsAs = array(
		'Shop.BlankDelete' => array('or' => array('catalog_item_parent_id', 'catalog_item_child_id', 'quantity'))
	);
	
	var $belongsTo = array(
		'ProductParent' => array(
			'className' => 'Shop.CatalogItem',
			'foreignKey' => 'catalog_item_parent_id',
		),
		'ProductChild' => array(
			'className' => 'Shop.CatalogItem',
			'foreignKey' => 'catalog_itemt_child_id',
		)
	);
}
