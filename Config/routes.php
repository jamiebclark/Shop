<?php
Router::connect('/shop/product/:id/:slug', 
	array(
		'controller' => 'catalog_items', 
		'action' => 'view',
		'plugin' => 'shop',
	),
	array('pass' => array('id', 'slug'), 'id' => '[0-9]+')
);
