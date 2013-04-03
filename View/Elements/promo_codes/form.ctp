<?php
$add = !$this->Html->value('ProductPromo.id');
if ($add) {
	$crumbs = array(
		'Add Promo',
	);
} else {
	$crumbs = array(
		array($this->Html->value('ProductPromo.title'), array('action' => 'view', $this->Html->value('ProductPromo.id'))),
		'Edit Promo',
	);
}

$dateOptions = array(
	'minYear' => REGISTER_YEAR - 30,
	'maxYear' => REGISTER_YEAR,
	'empty' => true,
);

echo $this->Form->create('ProductPromo');
echo $this->Form->inputs(array(
	'legend' => 'Handling Charge',
	'id',
	'title',
	'code' => array('label' => 'Promo Code'),
	'pct' => array('label' => 'Discount Percent'),
	'amt' => array('label' => 'Discount Amount $'),
	'started' => $dateOptions,
	'stopped' => $dateOptions,
	'active' => array('default' => 1),
));
echo $this->FormLayout->submit($add ? 'Add New Promotion' : 'Update Promotion');
echo $this->Form->end();
?>