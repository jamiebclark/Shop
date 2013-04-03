<?php
class OrderProductsShippingRule extends ShopAppModel {
	var $name = 'OrderProductsShippingRule';
	var $belongsTo = array('Shop.ShippingRule', 'Shop.OrderProduct');
}