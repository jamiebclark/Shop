<?php
$year = date('Y') + 1;
$dateOptions = array(
	'minYear' => $year - 30,
	'maxYear' => $year,
	'empty' => true,
);

echo $this->Form->create('PromoCode');
echo $this->Form->inputs(array(
	'legend' => 'Promo Code',
	'id',
	'title' => array(
		'after' => '<span class="help-block">An internal name (i.e. "Spring sale" or "Weekend Clearance")</span>',
	),
	'code' => array(
		'label' => 'Promo Code',
		'after' => '<span class="help-block">The code users will enter.</span>',
	),
	'pct' => array(
		'label' => 'Discount Percent',
		'beforeInput' => '<div class="input-group">',
		'afterInput' => '<span class="input-group-addon">%</span></div>',
		'after' => '<span class="help-block">Percentage the total will be reduced</span>',
		'step' => 'any',
	),
	'amt' => array(
		'label' => 'Discount Amount',
		'beforeInput' => '<div class="input-group"><span class="input-group-addon">$</span>',
		'afterInput' => '</div>',
		'after' => '<span class="help-block">Flat rate the total will be reduced</span>',
		'step' => 'any',
	),
	'started' => $dateOptions + array(
		'after' => '<span class="help-block">When this promo will start going into effect (blank for right now)</span>',
		'type' => 'text',
		'class' => 'form-control datepicker',
	),
	'stopped' => $dateOptions + array(
		'after' => '<span class="help-block">When this promo will stop (blank for always on)</span>',
		'type' => 'text',
		'class' => 'form-control datepicker',
	),
	'active' => array(
		'label' => 'Active',
		'class' => 'checkbox',
		'after' => '<span class="help-block">Is this promo code be usable now?</span>',
	),
));
echo $this->Form->end(array(
	'label' => 'Update',
	'class' => 'btn btn-primary btn-lg',
));