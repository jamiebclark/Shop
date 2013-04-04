<?php
echo $this->element('product_images/staff_heading', array(
	'crumbs' => array(
		'Image',
	)
));

echo $this->Html->tag('h1', 'Product Image');
echo $this->Layout->headerMenu(array(
	array('Edit Image', array('action' => 'edit', $productImage['ProductImage']['id'])),
	array('Delete Image', array('action' => 'delete', $productImage['ProductImage']['id']), null, 'Delete this image?'),
));

<<<<<<< HEAD
echo $this->CatalogItem->thumb($productImage['ProductImage'], array('class' => 'productImageView'));
=======
echo $this->Product->thumb($productImage['ProductImage'], array('class' => 'productImageView'));
>>>>>>> 7f1010ba1dfec77e6fe69120dbda39b9bea5eb76
?>