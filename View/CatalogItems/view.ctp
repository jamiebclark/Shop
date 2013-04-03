<?php
echo $this->element('products/crumbs', array(
	'crumbs' => $product['Product']['title']
));
echo $this->Html->div('product');
echo $this->Form->create('OrderProduct', array('action' => 'add'));

echo $this->Grid->col('1/4', null, array('class' => 'productViewImages'));
echo $this->Product->thumb(
	$product['Product'], array(
		'div' => false, 
		'dir' => 'thumb',
		'url' => array(
			'controller' => 'product_images',
			'action' => 'index',
			$product['Product']['id'],
		)
	)
);
if (count($product['ProductImage']) > 1) {
	echo $this->element('product_images/thumb_list', array(
		'limit' => 3,
		'productImages' => $product['ProductImage'],
	));
}

echo $this->Grid->colContinue('1/2');
echo $this->Html->tag('h1', $product['Product']['title']);

echo $this->DisplayText->text($product['Product']['description']);

if (!empty($product['ProductPackageChild'])) {
	echo $this->Html->tag('h2', 'Packaged Item');
	echo 'This product contains the following items:';
	$this->Table->reset();
	foreach ($product['ProductPackageChild'] as $k => $productPackage) {
		if ($productPackage['ProductChild']['hidden']) {
			$url = null;
			$title = $productPackage['ProductChild']['title'];
		} else {
			$url = $this->Product->url($productPackage['ProductChild']);
			$title = $this->Html->link($productPackage['ProductChild']['title'], $url);
		}
		
		if (!empty($loggedUserTypes['staff']) && !empty($productChildOptions[$productPackage['ProductChild']['id']])) {
			$childId = $productPackage['ProductChild']['id'];
			$title .= $this->Html->div('fullFormWidth', $this->element('products/product_option_input', array(
				'productOptions' => $productChildOptions[$childId],
				'model' => 'OrderProduct.PackageChild.' . $childId,
			)));
		}
		$this->Table->cells(array(
			array(
				$this->Product->thumb($productPackage['ProductChild'], array('dir' => 'thumb', 'url' => $url)),
				null,
				null,
				null,
				array('width' => 40)
			),
			array($title, 'Product'),
			array(
				number_format($productPackage['quantity']),
				'Quantity',
				null,
				null,
				array('width' => 40)
			)
		), true);
	}
	echo $this->Table->table();
}
echo $this->Grid->colContinue('1/4', null, array('class' => 'orderCart'));

echo $this->Product->price($product['Product']);

$notes = array();
if ($product['Product']['min_quantity'] > 1) {
	$notes[] = 'Minimum order of ' . number_format($product['Product']['min_quantity']);
}
if ($product['Product']['quantity_per_pack'] > 1) {
	$notes[] = 'This is a pack of ' . number_format($product['Product']['quantity_per_pack']);
}
if ($product['Product']['stock'] < 10) {
	$notes[] = 'Limited stock';
}

if (!empty($notes)) {
	echo $this->Html->tag('h3', 'Notes');
	echo $this->Layout->menu($notes);
}

if ($product['Product']['stock'] <= 0) {
	echo $this->Html->tag('h3', 'Out of Stock');
	echo $this->Html->tag('p', 'Sorry, this item is temporarily out of stock. Please check back soon for inventory updates!');
} else {
	echo $this->Layout->fieldset('Add to Cart');
	echo $this->Form->inputs(array(
		'fieldset' => false,
		'Order.id' => array(
			'type' => 'hidden'
		),
		'OrderProduct.product_id' => array(
			'type' => 'hidden', 
			'value' => $product['Product']['id']
		),
		'Order.user_id' => array(
			'type' => 'hidden', 
			'default' => $loggedUserId
		),
	));

	echo $this->element('products/product_option_input', array(
		'blank' => true,
		'label' => false,
	));
	echo $this->Form->input('OrderProduct.quantity', array(
		'default' => !empty($product['Product']['min_quantity']) ? $product['Product']['min_quantity'] : 1,
		'class' => 'quantity',
	));
	echo $this->FormLayout->submit('Add to Cart');
	echo "</fieldset>\n";
}

echo $this->Grid->colClose(true);

echo $this->Form->end();
echo "</div>\n";	//product

if (!empty($loggedUserTypes['staff'])) {
	echo "<hr/>\n";
	echo $this->Layout->adminMenu(array('view', 'edit'), array('url' => array(
		'action' => 'view',
		$product['Product']['id'],
		'staff' => true
	)));
}
?>