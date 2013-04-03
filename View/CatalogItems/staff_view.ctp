<?php
echo $this->element('products/staff_heading', array(
	'crumbs' => $product['Product']['title'],
));

echo $this->Html->tag('h1', $product['Product']['title'], array('class' => 'topTitle'));
echo $this->Layout->headerMenu(array(
	array('Edit Product', array('action' => 'edit', $product['Product']['id'])),
	array('Remove Product', array('action' => 'delete', $product['Product']['id']), null, 'Delete this product?')
));
echo $this->Html->div('span-16');
echo $this->Layout->infoResultTable($product['Product'], array(
		'title',
		'short_description',
		'description',
		'price' => array('format' => 'cash'),
		'sale' => array('format' => 'cash', 'notEmpty', 'class' => 'sale'),
		'stock' => array(
			'label' => 'Currently In Stock',
			'format' => 'number',
			'url' => array('controller' => 'product_inventories', 'action' => 'view', $product['Product']['id'])
		),
		'unlimited' => array(
			'format' => 'yesno',
			'label' => 'Unlimited Inventory',
		),			
		'min_quantity' => array('label' => 'Minimum Quantity per Order'),
		'quantity_per_pack',
		'created' => array('format' => 'date'),
		'modified' => array('format' => 'date', 'label' => 'Last Modified'),
		'active' => array('format' => 'yesno'),
	)
);

$url = array(
	'action' => 'shipping_rules',
	$product['Product']['id']
);
echo $this->Layout->headingActionMenu('Shipping Rules', array(array('edit', $url)));$this->Table->reset();
foreach ($product['ProductShippingRule'] as $shippingRule) {
	$range = !empty($shippingRule['min_quantity']) ? $shippingRule['min_quantity'] : '...';
	$range .= ' - ' . (!empty($shippingRule['max_quantity']) ? $shippingRule['max_quantity'] : '...');
	$this->Table->cells(array(
		array('If:', '&nbsp;'),
		array($range, 'Quantity Range'),
		array('Then Add:', '&nbsp;'),
		array($this->DisplayText->cash($shippingRule['amt']), 'Flat Rate'),
		array($this->DisplayText->cash($shippingRule['per_item']), 'Per-Item'),
		array(round($shippingRule['pct'] * 100) . '%', 'Percent of Sub-Total')
	), true);
}
echo $this->Table->table();
echo "<hr/>\n";

echo "</div>\n";
echo $this->Html->div('span-8 last');
$url = array(
	'action' => 'packages',
	$product['Product']['id']
);
echo $this->Layout->headingActionMenu('Product Packages', array(array('edit', $url)));
$this->Table->reset();
if (!empty($product['ProductPackageChild'])) {
	foreach ($product['ProductPackageChild'] as $ProductPackageChild) {
		$this->Table->cells(array(
			array($this->Product->thumb($ProductPackageChild['ProductChild'], array('dir' => 'thumb', 'url' => $url))),
			array($this->Html->link($ProductPackageChild['ProductChild']['title'], $url), 'Product'),
			array($this->Html->link(number_format($ProductPackageChild['quantity']), $url), 'Quantity')
		), true);
	}
	echo $this->Table->table();
}
echo "<hr/>\n";

echo $this->Layout->headingActionMenu('Product Images', array('index', 'add'), array('url' => array(
	'controller' => 'product_images',
	'action' => 'index',
	$product['Product']['id']
)));
$this->Table->reset();
foreach ($product['ProductImage'] as $productImage) {
	$url = array('controller' => 'product_images', 'action' => 'view', $productImage['id']);
	$this->Table->cells(array(
		array($this->Product->thumb($productImage, array('url' => $url))),
		array($this->Layout->actionMenu(array('view', 'edit', 'delete', 'move_up', 'move_down'), compact('url'))),
	), true);
}
echo $this->Table->table(array('class' => 'orderProductsForm'));
echo "</div>\n";
?>