<?php
echo $this->Form->create();
echo $this->Form->hidden('id');
echo $this->Form->input('catalog_item_parent_id', [
	'type' => 'hidden',
	'label' => 'Parent Item',
]);
?>
<p class="help-block">Choose the product you want to be grouped into this package</p>
<div class="row">
	<div class="col-sm-8">
	<?php 
		echo $this->Form->input('catalog_item_child_id', [
			'label' => 'Child Item',
			'options' => $packageChildren,
		]);
	?>
	</div>
	<div class="col-sm-4">
	<?php
		echo $this->Form->input('quantity', [
			'type' => 'number',
			'afterInput' => '<span class="help-block">How many?</span>',
		]); 
	?>
	</div>
</div>
<?php
echo $this->Form->submit('Save');
echo $this->Form->end();