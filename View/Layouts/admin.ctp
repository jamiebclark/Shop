<?php
$this->extend('default');
echo $this->Layout->navBar(array(
	array('Orders', array('controller' => 'orders', 'action' => 'index')),
	array('Catalog Items', array('controller' => 'catalog_items', 'action' => 'index')),
	array('Categories', array('controller' => 'catalog_item_categories', 'action' => 'index')),
	array('Inventory', array('controller' => 'products', 'action' => 'index')),
	array('Handling', array('controller' => 'handling_methods', 'action' => 'index')),
	array('Promo Codes', array('controller' => 'promo_codes', 'action' => 'index')),
	array('Shipping Methods', array('controller' => 'shipping_methods', 'action' => 'index')),
), 'Online Store', array('currentSelect' => array('controller')));
echo $this->fetch('content');
