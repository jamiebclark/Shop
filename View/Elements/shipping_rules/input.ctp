<?php
$numOptions = array('class' => 'input-small');
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
	<div class="span3"><?php
		echo $this->Form->input($prefix . 'min_quantity', array('label' => 'Minimum') + $numOptions);
	?></div>
	<div class="span3 right-border"><?php
		echo $this->Form->input($prefix . 'max_quantity', array('label' => 'Maximum') + $numOptions);
	?></div>
	<div class="span2"><?php
		echo $this->Form->input($prefix . 'amt', array('label' => 'Base Add') + $cashOptions);
	?></div>
	<div class="span2"><?php
		echo $this->Form->input($prefix . 'per_item', array('label' => 'Per-item') + $cashOptions);
	?></div>
	<div class="span2"><?php
		echo $this->Form->input($prefix . 'pct', array('label' => '% of Subtotal') + $pctOptions);
	?></div>
</div>
