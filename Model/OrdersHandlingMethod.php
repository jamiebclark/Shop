<?php
class OrdersHandlingMethod extends ShopAppModel {
	var $name = 'OrdersHandlingMethod';
	var $actsAs = array('Shop.BlankDelete' => array('and' => array('amt', 'pct')));
	var $belongsTo = array('Shop.HandlingMethod', 'Shop.Order');
}
