<?php
$this->Asset->css('products');

if (!isset($sort)) {
	$sort = true;
}

if ($sort) {
	echo $this->Layout->tableSortMenu(array(
		array('Title', 'Product.title'),
		array('Lowest Price', 'Product.price', 'ASC'),
	));
}

$this->Table->reset();
foreach ($products as $product) {
	$url = $this->Product->url($product['Product']);
	$productCell = $this->Html->tag('h2', $this->Product->link($product['Product']));
	if (!empty($product['Product']['short_description'])) {
		$productCell .= $this->DisplayText->text($product['Product']['short_description']);
	}
	$dir = 'thumb';
	$this->Table->cells(array(
		array(
			$this->Product->thumb($product['Product'], compact('url', 'dir')),
			$sort ? 'Product' : null,
			$sort ? 'Product.title' : null,
			null,
			array('width' => 80)
		),
		array($productCell),
		array(
			$this->Product->price($product['Product']), 
			$sort ? 'Price' : null, 
			$sort ? 'Product.price' : null,
		),
	), true);
}
echo $this->Html->div('productsList',
	$this->Table->table(array('paginate'))
);

?>