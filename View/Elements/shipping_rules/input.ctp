<?php
$numOptions = array('class' => 'form-control input-mini');
$pctOptions = array(
	'step' => 'any', 
	'beforeInput' => '<div class="input-group">',
	'afterInput' => '<span class="input-group-addon">%</span></div>', 
) + $numOptions;
$cashOptions = array(
	'beforeInput' => '<div class="input-group"><span class="input-group-addon">$</span>',
	'afterInput' => '</div>', 
	'step' => 'any',
	'placeholder' => '0.00', 
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
			'div' => 'form-group shipping-rule-amt',
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
			'beforeInput' => '<div class="input-group">',
			'afterInput' => '<span class="input-group-addon">+</span></div>',
		) + $cashOptions);
		echo $this->Form->input($prefix . 'per_item', array(
			'label' => false,
			'beforeInput' => '<div class="input-group">',
			'afterInput' => '<span class="input-group-addon">Per-item</span></div>',
		) + $cashOptions);
		echo $this->Form->input($prefix . 'pct', array(
			'label' => false,
			'beforeInput' => '<div class="input-group"><span class="input-group-addon">+</span>',
			'afterInput' => '</div>',
			'placeholder' => '0%',
		) + $pctOptions);
	?></div>
</div>
