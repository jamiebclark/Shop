<?php
class ShippingRule extends ShopAppModel {
	var $name = 'ShippingRule';
	var $actsAs = array(
		'Shop.BlankDelete' => array('and' => array('amt', 'pct', 'per_item')),
		'Layout.Removable',
	);
	var $belongsTo = array(
		'Shop.CatalogItem', 
		'Shop.ShippingClass',
		'Layout.Removable',
	);
	var $hasAndBelongsToMany = array('Shop.OrderProduct');
}
