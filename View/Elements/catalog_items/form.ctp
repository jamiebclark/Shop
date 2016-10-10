<?php 
$cashOptions = ['beforeInput' => '<div class="input-group"><span class="input-group-addon">$</span>','afterInput' => '</div>', 'step' => 'any'];

echo $this->Layout->defaultHeader(); 
echo $this->Form->create('CatalogItem', ['type' => 'file']);
echo $this->FormLayout->inputs([
	'id',
	'title',
	'short_description',
	'description' => ['escape' => false, 'rows' => 10],
]);
?>
<?php
echo $this->FormLayout->inputs([
	'price' => ['label' => 'Price', 'type' => 'cash'],
	'sale' => [
		'label' => 'Sale Price', 
		'type' => 'cash',
		'after' => '<span class="help-block">A temporary sale price</span>',
	],
	'min_quantity' => [
		'label' => 'Minimum Quantity', 
		'type' => 'number',
		'after' => '<span class="help-block">The minimum amount required to count as an order</span>',
	],
	'quantity_per_pack' => [
		'type' => 'number',
		'after' => '<span class="help-block">How to subtract from inventory with every order placed</span>',
	],
]);

echo $this->FormLayout->inputs([
	'active' => [
		'label' => 'Active',
		'class' => 'checkbox',
		'after' => '<span class="help-block">Whether item is available in the store</span>',
	],
	'hidden' => [
		'label' => 'Hidden',
		'class' => 'checkbox',
		'after' => '<span class="help-block">This item is available for purchase, but it won\'t show up in the store</span>',
	],
	'unlimited' => [
		'label' => 'Unlimited Inventory',
		'class' => 'checkbox',
		'after' => '<span class="help-block">Item\'s stock never runs out</span>'
	],
]);
echo $this->FormLayout->inputList('shipping_rules/input', ['model' => 'ShippingRule', 'legend' => 'Shipping Rules']);
?>
</fieldset>

<fieldset>
	<legend>Categories</legend>
	<?php echo $this->element('Layout.form/element_input_list', [
		'element' => 'catalog_item_categories/input',
		'count' => 0,
		'model' => 'CatalogItemCategory',
		'modelHuman' => 'Category',
	]); ?>
</fieldset>
<fieldset>
	<legend>Images</legend>
	<?php echo $this->element('Layout.form/element_input_list', [
		'element' => 'catalog_item_images/input',
		'model' => 'CatalogItemImage',
		'modelHuman' => 'Image',
		'count' => 0,
	]); ?>
</fieldset>
<?php 
echo $this->FormLayout->end('Update');