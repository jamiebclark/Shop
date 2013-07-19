<?php
$numOptions = array('class' => 'input-mini');
$pctOptions = array('step' => 'any', 'append' => '%', 'class' => 'input-mini') + $numOptions;
$cashOptions = array(
	'prepend' => '$', 'step' => 'any','placeholder' => '0.00', 'class' => 'input-mini'
) + $numOptions;

$prefix = 'ShippingRule.';
if (isset($count)) {
	$prefix .= "$count.";
}
echo $this->Form->inputs(array(
	'fieldset' => false,
	$prefix . 'id' => array('type' => 'hidden'),
	$prefix . 'catalog_item_id' => array('type' => 'hidden', 'value' => $this->request->data['CatalogItem']['id']),
));
?>
<div class="row-fluid shipping-rule-input">
	<div class="span6"><?php
		echo $this->Form->input($prefix . 'min_quantity', array(
			'label' => 'Product Amount',
			'placeholder' => 'Min',
			'after' => ' - ' . $this->Form->input($prefix . 'max_quantity', array(
				'label' => false,
				'div' => false,
				'placeholder' => 'Max'
			) + $numOptions)
		) + $numOptions);
	?></div>
	<div class="span6"><?php
		echo $this->Form->input($prefix . 'amt', array(
			'label' => 'Adjust Order',
			'append' => '+',
		) + $cashOptions);
		echo $this->Form->input($prefix . 'per_item', array(
			'label' => false,
			'append' => 'Per-item',
		) + $cashOptions);
		echo $this->Form->input($prefix . 'pct', array(
			'label' => false,
			'prepend' => '+',
			'placeholder' => '0%',
		) + $pctOptions);
	?></div>
</div>