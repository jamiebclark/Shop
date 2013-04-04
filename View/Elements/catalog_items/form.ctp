<?php
$add = !$this->Html->value('CatalogItem.id');

echo $this->Html->tag('h1', $add ? 'Add a CatalogItem' : 'Edit CatalogItem Info');
echo $this->Form->create('CatalogItem');
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
	'name' => 'CatalogItemCategory',
	'label' => false,
	'options' => $catalogItemCategories,
));
echo "</div>\n";
echo $this->Form->end();
