<?php 
echo $this->Layout->defaultHeader(); 
echo $this->Form->create('CatalogItem', array('type' => 'file'));
$cashOptions = array('prepend' => '$', 'step' => 'any', 'class' => 'input-small');

?>
<div class="row">
	<div class="col-sm-6">
		<?php
		echo $this->FormLayout->inputs(array(
			'id',
			'title' => array('class' => 'input-block-level'),
			'short_description' => array('class' => 'input-block-level'),
			'description' => array('escape' => false, 'class' => 'input-block-level', 'rows' => 10),
		));
		?>
		<div class="form-horizontal"><?php
			echo $this->FormLayout->inputs(array(
				'price' => array('label' => 'Price', 'type' => 'cash'),
				'sale' => array('label' => 'Sale Price', 'type' => 'cash'),
				'min_quantity' => array('label' => 'Minimum Quantity', 'type' => 'number'),
				'quantity_per_pack' => array('type' => 'number'),
			));
			?>
		</div>
		<?php
		echo $this->FormLayout->inputs(array(
			'active' => array(
				'label' => 'Active',
				'helpBlock' => 'Whether item is available for sale',
			),
			'hidden' => array(
				'label' => 'Hidden',
				'helpBlock' => 'Hide this from catalog page while still being active',
			),
			'unlimited' => array(
				'label' => 'Unlimited Inventory',
				'helpBlock' => 'Item\'s stock never runs out'
			),
		));
		echo $this->FormLayout->inputList('shipping_rules/input', array('model' => 'ShippingRule', 'legend' => 'Shipping Rules'));
		?>
		</fieldset>
	</div>
	<div class="col-sm-6">
		<fieldset>
			<legend>Categories</legend>
			<?php echo $this->FormLayout->inputList('catalog_item_categories/input', array('model' => 'CatalogItemCategory')); ?>
		</fieldset>
		<fieldset>
			<legend>Images</legend>
			<?php echo $this->FormLayout->inputList('catalog_item_images/input', array('model' => 'CatalogItemImage')); ?>
		</fieldset>
		<fieldset>
			<legend>Options / Sizes</legend>
			<p class="note">If there are options that must be selected before the item can be purchased (ie sizes, colors, langauges, etc), then set them here</p>
			<?php echo $this->FormLayout->inputList('catalog_item_options/admin_input', array('model' => 'CatalogItemOption')); ?>
		</fieldset>
		<fieldset>
			<legend>Packages</legend>
			<p class="note">If this is a combination of other products, you can group them all here</p>
			<?php echo $this->FormLayout->inputList('catalog_item_packages/input', array('model' => 'CatalogItemPackage')); ?>
		</fieldset>
	</div>
</div>
<?php 
echo $this->FormLayout->end('Update');