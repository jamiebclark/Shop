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
	
	var $validate = array(
		'title' => array(
			'rule' => 'notEmpty',
			'message' => 'Please name your shipping method',
		)
	);
}