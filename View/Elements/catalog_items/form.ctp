<?php
$add = !$this->Html->value('Product.id');

echo $this->Html->tag('h1', $add ? 'Add a Product' : 'Edit Product Info');
echo $this->Form->create('Product');
echo $this->Html->div('span-18');
echo $this->Form->inputs(array(
	'id',
	'title',
	'short_description',
	'description' => array('escape' => false),
	'price' => array('label' => 'Price $'),
	'sale' => array('label' => 'Sale Price $'),
	'min_quantity' => array('label' => 'Minimum Quantity'),
	'quantity_per_pack',
	'active',
	'hidden',
	'unlimited' => array('label' => 'Unlimited Inventory (never runs out)'),
));
echo $this->FormLayout->submit('Update');

echo "</div>\n";
echo $this->Html->div('span-6 last fullFormWidth');
echo $this->element('form/habtm_select', array(
	'name' => 'ProductCategory',
	'label' => false,
	'options' => $productCategories,
));
echo "</div>\n";
echo $this->Form->end();

?>