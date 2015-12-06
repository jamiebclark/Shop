<?php 
echo $this->Layout->defaultHeader(); 
echo $this->Form->create('CatalogItem', ['type' => 'file']);
$cashOptions = ['beforeInput' => '<div class="input-group"><span class="input-group-addon">$</span>','afterInput' => '</div>', 'step' => 'any'];

?>
<div class="row">
	<div class="col-sm-6">
		<?php
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
	</div>
	<div class="col-sm-6">
		<fieldset>
			<legend>Categories</legend>
			<?php echo $this->FormLayout->inputList('catalog_item_categories/input', ['model' => 'CatalogItemCategory']); ?>
		</fieldset>
		<fieldset>
			<legend>Images</legend>
			<?php echo $this->FormLayout->inputList('catalog_item_images/input', ['model' => 'CatalogItemImage']); ?>
		</fieldset>
		<fieldset>
			<legend>Options / Sizes</legend>
			<p class="help-block">If there are options that must be selected before the item can be purchased (ie sizes, colors, langauges, etc), then set them here</p>
			<?php echo $this->FormLayout->inputList('catalog_item_options/admin_input', ['model' => 'CatalogItemOption']); ?>
		</fieldset>
		<fieldset>
			<legend>Packages</legend>
			<p class="help-block">If this is a combination of other products, you can group them all here</p>
			<?php echo $this->FormLayout->inputList('catalog_item_packages/input', ['model' => 'CatalogItemPackage']); ?>
		</fieldset>
	</div>
</div>
<?php 
echo $this->FormLayout->end('Update');