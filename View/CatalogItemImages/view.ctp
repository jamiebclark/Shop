<?php
$this->Crumbs->title('Image');
echo $this->element('products/crumbs', compact('crumbs'));

echo $this->Product->thumb($productImage['ProductImage'], array(
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