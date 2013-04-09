<?php
$add = !$this->Html->value('ProductInventory.id');

if ($add) {
	$crumbs = 'Add Inventory';
} else {
	$crumbs = array(
		array($this->Html->value('Product.title'), array('action' => 'view', $this->Html->value('ProductInventory.id'))),
		array('Edit')
	);
}
echo $this->element('product_inventories/admin_heading', compact('crumbs'));

echo $this->Form->create('ProductInventory');
echo $this->Html->div('text input');
echo $this->Html->tag('label', 'Product');
echo $this->Html->div('fakeInput', $product['Product']['title']);
echo "</div>\n";

echo $this->Form->inputs(array(
	'id',
	'Product.id' => array('type' => 'hidden'),
	'fieldset' => false,
));
echo $this->element('products/product_option_input', array(
	'blank' => true,
	'admin' => true,
	'model' => 'ProductInventory',
));
$dateOptions = array(
	'empty' => true,
	'minYear' => REPORT_YEAR - 20,
	'maxYear' => REPORT_YEAR + 2,
);
echo $this->Form->inputs(array(
	'fieldset' => false,
	'ProductInventoryAdjustment.0.id',
	'ProductInventoryAdjustment.0.quantity',
	'ProductInventoryAdjustment.0.available' => $dateOptions,
));
echo $this->FormLayout->submit($add ? 'Add Inventory' : 'Update Inventory Entry');
echo $this->Form->end();
?>