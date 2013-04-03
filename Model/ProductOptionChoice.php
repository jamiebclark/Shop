<?php
class ProductOptionChoice extends ShopAppModel {
	var $name = 'ProductOptionChoice';
	var $actsAs = array('Shop.BlankDelete' => array('and' => array('title')));
	var $order = '$ALIAS.id';
	
	var $belongsTo = array('Shop.CatalogItemOption');
	var $hasMany = array('Shop.Product');
	/*
		'OrderProduct1' => array(
			'className' => 'Shop.OrderProduct',
			'foreignKey' => 'order_choice_id_1',
		),
		'OrderProduct2' => array(
			'className' => 'Shop.OrderProduct',
			'foreignKey' => 'order_choice_id_2',
		),
		'OrderProduct3' => array(
			'className' => 'Shop.OrderProduct',
			'foreignKey' => 'order_choice_id_3',
		),
		'OrderProduct4' => array(
			'className' => 'Shop.OrderProduct',
			'foreignKey' => 'order_choice_id_4',
		),
	);
	*/
}
