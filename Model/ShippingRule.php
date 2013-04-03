<?php
class ShippingRule extends ShopAppModel {
	var $name = 'ShippingRule';
	var $actsAs = array(
		'Shop.BlankDelete' => array('and' => array('amt', 'pct', 'per_item'))
	);
	var $belongsTo = array(
		'Shop.CatalogItem', 
		'Shop.ShippingClass'
	);
	var $hasAndBelongsToMany = array('Shop.OrderProduct');
}
