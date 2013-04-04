<?php
$this->Crumbs->title('Image');
echo $this->element('products/crumbs', compact('crumbs'));

<<<<<<< HEAD
echo $this->CatalogItem->thumb($productImage['ProductImage'], array(
=======
echo $this->Product->thumb($productImage['ProductImage'], array(
>>>>>>> 7f1010ba1dfec77e6fe69120dbda39b9bea5eb76
	'class' => 'productImageView'
));

if (count($productImages) > 1) {
	echo $this->element('product_images/thumb_list');
}

if (!empty($loggedUserTypes['staff'])) {
	echo $this->Layout->adminMenu(array('view', 'edit', 'add', 'delete'), array(
		'url' => array(
			'action' => 'view', 
			$productImage['Product']['id'],
			'staff' => true
		)
	));
}