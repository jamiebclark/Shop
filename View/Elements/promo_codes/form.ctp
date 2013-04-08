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
		'helpBlock' => 'An internal name (i.e. "Spring sale" or "Weekend Clearance")',
	),
	'code' => array(
		'label' => 'Promo Code',
		'helpBlock' => 'The code users will enter.',
	),
	'pct' => array(
		'label' => 'Discount Percent',
		'append' => '%',
		'helpBlock' => 'Percentage the total will be reduced',
		'step' => 'any',
	),
	'amt' => array(
		'label' => 'Discount Amount',
		'prepend' => '$',
		'helpBlock' => 'Flat rate the total will be reduced',
		'step' => 'any',
	),
	'started' => $dateOptions + array(
		'helpBlock' => 'When this promo will start going into effect (blank for right now)',
		'type' => 'text',
		'class' => 'datetimepicker',
	),
	'stopped' => $dateOptions + array(
		'helpBlock' => 'When this promo will stop (blank for always on)',
		'type' => 'text',
		'class' => 'datepicker',
	),
	'active' => array(
		'label' => 'Active',
		'helpBlock' => 'Is this promo code be usable now?',
	),
));
echo $this->Form->end('Submit');