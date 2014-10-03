<?php echo $this->element('Shop.catalog_item_images/crumbs'); ?>

<?php echo $this->element('catalog_item_images/thumb_list', array('perRow' => 6)); ?>

<?php if (!empty($loggedUserTypes['admin'])) :
	echo $this->Layout->adminMenu(array('view', 'add'), array(
		'url' => array(
			'action' => 'index', 
			'admin' => true
		)
	));
endif;
?>