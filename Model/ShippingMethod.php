<?php
class ShippingMethod extends ShopAppModel {
	var $name = 'ShippingMethod';
	var $actsAs = array('Shop.SelectList' => array(
		'blank' => true,
		'label' => 'Shipping Method',
	));
	var $hasMany = array('Order' => array(
		'className' => 'Shop.Order',
	));
}