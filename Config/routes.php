<?php
Router::connect('/shop/products/:id/:slug', 
	array(
		'controller' => 'catalog_items', 
		'action' => 'view',
		'plugin' => 'shop',
	),
	array('pass' => array('id', 'slug'), 'id' => '[0-9]+')
);

Router::connect('/shop/catalog_items/:id/:slug', 
	array(
		'controller' => 'catalog_items', 
		'action' => 'view',
		'plugin' => 'shop',
	),
	array('pass' => array('id', 'slug'), 'id' => '[0-9]+')
);

Router::connect('/products/:id/:slug', 
	array(
		'controller' => 'catalog_items', 
		'action' => 'view',
		'plugin' => 'shop',
	),
	array('pass' => array('id', 'slug'), 'id' => '[0-9]+')
);
