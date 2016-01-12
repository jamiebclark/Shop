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
<div class="form-horizontal"><?php
	$inputDefaults = $this->Form->inputDefaults();
	$this->Form->inputDefaults([
		'label' => ['class' => 'control-label col col-sm-3'],
		'wrapInput' => 'col col-sm-9',
	], true);
	echo $this->FormLayout->inputs([
		'price' => ['label' => 'Price', 'type' => 'cash'],
		'sale' => ['label' => 'Sale Price', 'type' => 'cash'],
		'min_quantity' => ['label' => 'Minimum Quantity', 'type' => 'number'],
		'quantity_per_pack' => ['type' => 'number'],
	]);
	$this->Form->inputDefaults($inputDefaults);
	?>
</div>
<?php
echo $this->FormLayout->inputs([
	'active' => [
		'label' => 'Active',
		'class' => 'checkbox',
		'after' => '<span class="help-block">Whether item is available for sale</span>',
	],
	'hidden' => [
		'label' => 'Hidden',
		'class' => 'checkbox',
		'after' => '<span class="help-block">Hide this from catalog page while still being active</span>',
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