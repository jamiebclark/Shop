<?php
class ShippingClass extends ShopAppModel {
	var $name = 'ShippingClass';
	var $hasMany = array('Shop.Order', 'Shop.ShippingRule');
}
