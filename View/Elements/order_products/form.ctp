<?php
$add = !$this->Html->value('OrderProduct.id');

echo $this->Form->create('OrderProduct');
echo $this->Html->tag('h2', $product['Product']['title']);

echo $this->element('products/product_option_input', array('legend' => 'Options'));

echo $this->Form->inputs(array(
	'id' => array('type' => 'hidden'),
	'order_id' => array('type' => 'hidden'),
	'product_id' => array('type' => 'hidden'),
	'parent_product_id' => array('type' => 'hidden'),
	'quantity' => array(
		'default' => !empty($product['Product']['min_quantity']) ? $product['Product']['min_quantity'] : 1,
	),
	'price' => array(
		'default' => $product['Product']['price']
	),
	'sale' => array(
		'default' => $product['Product']['sale'],
		'label' => 'Sale Price (if any)'
	),
	'shipping',
));

echo $this->FormLayout->submit($add ? 'Add To Order' : 'Update');
echo $this->Form->end();
