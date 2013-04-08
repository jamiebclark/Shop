<?php
$this->extend('default');
echo $this->Layout->menu(array(
	array('Orders', array('controller' => 'orders', 'action' => 'index')),
	array('Catalog Items', array('controller' => 'catalog_items', 'action' => 'index')),
	array('Inventory', array('controller' => 'products', 'action' => 'index')),
	array('Handling', array('controller' => 'handling_methods', 'action' => 'index')),
	array('Promo Codes', array('controller' => 'promo_codes', 'action' => 'index')),
), array('class' => 'nav nav-tabs', 'tag' => false, 'currentSelect' => array('controller')));

echo $this->fetch('content');
