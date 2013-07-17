<?php 
echo $this->Layout->defaultHeader(); 
echo $this->Form->create('CatalogItem', array('type' => 'file'));
$cashOptions = array('prepend' => '$', 'step' => 'any', 'class' => 'input-small');

?>
<div class="row">
	<div class="span6">
		<?php
		echo $this->FormLayout->inputs(array(
			'id',
			'title' => array('class' => 'input-block-level'),
			'short_description' => array('class' => 'input-block-level'),
			'description' => array('escape' => false, 'class' => 'input-block-level'),
		));
		?>
		<div class="form-horizontal"><?php
			echo $this->FormLayout->inputs(array(
				'price' => array('label' => 'Price', 'type' => 'cash'),
				'sale' => array('label' => 'Sale Price', 'type' => 'cash'),
				'min_quantity' => array('label' => 'Minimum Quantity', 'type' => 'int'),
				'quantity_per_pack' => array('type' => 'int'),
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
		?>
	</div>
	<div class="span6">
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
	</div>
</div>
<fieldset><legend>Shipping Rules</legend>
	<?php 
		echo $this->FormLayout->inputList('shipping_rules/input', array('model' => 'ShippingRule'));
	?>
</fieldset>
<?php 
echo $this->FormLayout->end('Update');