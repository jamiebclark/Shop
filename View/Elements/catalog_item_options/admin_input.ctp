<?php
$prefix = "CatalogItemOption.$count";
echo $this->Form->hidden("$prefix.id");
?>
<div class="row">
	<div class="col-sm-6">
		<?php echo $this->Form->input("$prefix.title", array(
			'label' => false, 
			'placeholder' => 'Title',
		)); ?>
	</div>
	<div class="col-sm-2">
		<?php echo $this->Form->input("$prefix.index", array(
			'label' => false,
			'placeholder' => 'Order',
			'div' => false,
		)); ?>
	</div>
</div>
<?php 
echo $this->FormLayout->inputList('product_option_choices/input', array(
	'model' => "$prefix.ProductOptionChoice",
	'pass' => compact('prefix')
) + compact('prefix'));