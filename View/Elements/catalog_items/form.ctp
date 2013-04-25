<?php 
echo $this->Layout->defaultHeader(); 
echo $this->Form->create('CatalogItem', array('type' => 'file'));
$cashOptions = array('prepend' => '$', 'step' => 'any', 'class' => 'input-small');

?>
<div class="row">
	<div class="span6">
		<fieldset><legend>Item Info</legend>
			<?php
			echo $this->Form->inputs(array(
				'fieldset' => false,
				'id',
				'title',
				'short_description',
				'description' => array('escape' => false),
				'price' => array('label' => 'Price') + $cashOptions,
				'sale' => array('label' => 'Sale Price') + $cashOptions,
				'min_quantity' => array('label' => 'Minimum Quantity'),
				'quantity_per_pack',
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
		</fieldset>
	</div>
	<div class="span6">
		<fieldset><legend>Categories</legend>
		<?php
			echo $this->FormLayout->inputList('catalog_item_categories/input', array(
				'model' => 'CatalogItemCategory'
			));
		?>
		</fieldset>
		<fieldset><legend>Images</legend>
		<?php
			echo $this->FormLayout->inputList('catalog_item_images/input', array(
				'model' => 'CatalogItemImage'
			));
		?>
		</fieldset>
	</div>
</div>
<fieldset><legend>Shipping Rules</legend>
	<?php 
		echo $this->FormLayout->inputList('shipping_rules/input', array('model' => 'ShippingRule'));
	?>
</fieldset>
<?php 
echo $this->Form->submit('Update');
echo $this->Form->end(); 