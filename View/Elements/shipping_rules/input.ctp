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
$catalogItemId = !empty($this->request->data['CatalogItem']['id']) ? $this->request->data['CatalogItem']['id'] : null;
echo $this->Form->inputs(array(
	'fieldset' => false,
	$prefix . 'id' => array('type' => 'hidden'),
	$prefix . 'catalog_item_id' => array('type' => 'hidden', 'value' => $catalogItemId),
));
?>
<div class="row shipping-rule-input">
	<div class="col-sm-6"><?php
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
	<div class="col-sm-6"><?php
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
