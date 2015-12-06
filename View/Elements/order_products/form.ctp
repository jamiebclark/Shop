<?php
$add = !$this->Html->value('OrderProduct.id');
$cashOptions = ['beforeInput' => '<div class="input-group"><span class="input-group-addon">$</span>','afterInput' => '</div>', 'step' => 'any', 'placeholder' => '0.00'];
$inputs = array(
	'fieldset' => false,
	'id' => ['type' => 'hidden'],
	'order_id' => ['type' => 'hidden'],
	'product_id',
	'parent_catalog_item_id' => ['type' => 'hidden'],
	'quantity' => ['default' => 1],
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
echo $this->Form->create('OrderProduct');
echo $this->Form->inputs($inputs);
echo $this->Form->submit($add ? 'Add To Order' : 'Update');
echo $this->Form->end();
