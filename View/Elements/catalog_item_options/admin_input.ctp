<?php
$prefix = "CatalogItemOption.$count";
echo $this->Form->hidden("$prefix.id");
echo $this->Form->input("$prefix.title", array(
	'label' => false, 
	'placeholder' => 'Title',
	'after' => $this->Form->input("$prefix.index", array(
		'label' => false,
		'placeholder' => 'Order',
		'div' => false,
	))
));
echo $this->FormLayout->inputList('product_option_choices/input', array(
	'model' => "$prefix.ProductOptionChoice",
	'pass' => compact('prefix')
) + compact('prefix'));