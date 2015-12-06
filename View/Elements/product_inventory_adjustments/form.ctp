<?php
$productIdType = !empty($product) ? 'hidden' : 'select';
?>
<h2>Adjust Inventory</h2>
<?php
echo $this->Form->create('ProductInventoryAdjustment');
echo $this->Form->hidden('id');
echo $this->Form->input('product_id', ['type' => 'select']);
?>

<?php if ($this->Form->value('ProductInventoryAdjustment.quantity')): ?>
	<?php echo $this->Form->input('quantity', [
		'type' => 'number',
	]); ?>
<?php else: ?>
	<div class="row">
		<div class="col-sm-4">
			<?php echo $this->Form->input('add_quantity', [
				'type' => 'number',
				'label' => 'Add Inventory',
				'div' => 'form-group has-success',
				'placeholder' => 0,
				'beforeInput' => '<div class="input-group"><span class="input-group-addon">+</span>',
				'afterInput' => '</div><span class="help-block">Add stock to the inventory</span>',
			]); ?>
		</div>
		<div class="col-sm-4">
			<?php echo $this->Form->input('remove_quantity', [
				'type' => 'number',
				'label' => 'Remove Inventory',
				'div' => 'form-group has-warning',
				'placeholder' => 0,
				'beforeInput' => '<div class="input-group"><span class="input-group-addon">-</span>',
				'afterInput' => '</div><span class="help-block">Remove stock to the inventory</span>',
			]); ?>
		</div>
		<div class="col-sm-4">
			<?php echo $this->Form->input('change_quantity',[
				'type' => 'number',
				'label' => 'Change Inventory',
				'placeholder' => 0,
				'afterInput' => '<span class="help-block">Change the stock of the product</span>',
			]); ?>
		</div>
	</div>
<?php endif; ?>

<?php 
echo $this->Form->input('title', [
	'label' => 'Description',
	'after' => '<span class="help-block">Optional description behind why this stock was added</span>',
]);

echo $this->FormLayout->inputDate('available', [
	'label' => 'Date Available',
	'control' => ['today', 'clear']
]);
echo $this->FormLayout->end('Update');