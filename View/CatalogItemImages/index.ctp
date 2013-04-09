<?php
echo $this->element('products/crumbs');

echo $this->element('product_images/thumb_list', array(
	'class' => 'fullWidth'
));

if (!empty($loggedUserTypes['admin'])) {
	echo $this->Layout->adminMenu(array('view', 'add'), array(
		'url' => array(
			'action' => 'index', 
			'admin' => true
		)
	));
}
