<?php
$this->extend('Layout.default');
echo $this->Layout->navBar(array(
	array('Orders', array('controller' => 'orders', 'action' => 'index')),
	array('Catalog Items', array('controller' => 'catalog_items', 'action' => 'index')),
	array('Categories', array('controller' => 'catalog_item_categories', 'action' => 'index')),
	array('Inventory', array('controller' => 'products', 'action' => 'index')),
	array('Handling', array('controller' => 'handling_methods', 'action' => 'index')),
	array('Promos', array('controller' => 'promo_codes', 'action' => 'index')),
	array('Shipping', array('controller' => 'shipping_methods', 'action' => 'index')),
	array('Invoices', array('controller' => 'invoices', 'action' => 'index')),
	array('PayPal', array('controller' => 'paypal_payments', 'action' => 'logs')),
	array('Settings', array('controller' => 'shop_settings', 'action' => 'index')),
), 'Online Store', array('currentSelect' => array('controller')));
echo $this->fetch('content');
