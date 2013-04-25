<?php
$add = !$this->Html->value('OrderProduct.id');
$cashOptions = array('prepend' => '$', 'step' => 'any', 'placeholder' => '0.00');
$inputs = array(
	'fieldset' => false,
	'id' => array('type' => 'hidden'),
	'order_id' => array('type' => 'hidden'),
	'product_id',
	'parent_catalog_item_id' => array('type' => 'hidden'),
	'quantity' => array('default' => 1),
	'price' => $cashOptions,
	'sale' => $cashOptions + array('label' => 'Sale Price (if any)'),
	'shipping' => $cashOptions,
);

if (!empty($catalogItem)) {
	if (!empty($catalogItem['CatalogItem']['min_quantity'])) {
		$inputs['quantity']['default'] = $catalogItem['CatalogItem']['min_quantity'];
	}
	if ($catalogItem['CatalogItem']['sale'] > 0) {
		$inputs['price']['default'] = $catalogItem['CatalogItem']['sale'];
	} else {
		$inputs['price']['default'] = $catalogItem['CatalogItem']['price'];
	}
}
echo $this->Layout->defaultHeader();
echo $this->Form->create('OrderProduct', array('class' => 'form-horizontal'));
echo $this->Form->inputs($inputs);
echo $this->Form->submit($add ? 'Add To Order' : 'Update');
echo $this->Form->end();
