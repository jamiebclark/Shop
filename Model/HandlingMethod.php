<?php
class HandlingMethod extends ShopAppModel {
	var $name = 'HandlingMethod';
	var $hasMany = array('Shop.OrdersHandlingMethod');
	var $hasAndBelongsToMany = array('Shop.Order');
}
