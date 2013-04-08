<?php
$add = !$this->Html->value('HandlingMethod.id');
if ($add) {
	$crumbs = array(
		'Add Handling',
	);
} else {
	$crumbs = array(
		array($this->Html->value('HandlingMethod.title'), array('action' => 'view', $this->Html->value('HandlingMethod.id'))),
		'Edit Handling',
	);
}

echo $this->Form->create('HandlingMethod');
echo $this->Form->inputs(array(
	'legend' => 'Handling Charge',
	'id',
	'title' => array('helpBlock' => 'A name for this handling we can refer to in the system'),
	'pct' => array(
		'append' => '%', 
		'helpBlock' => 'A percentage of the total to be charged',
		'step' => 'any',
	),
	'amt' => array(
		'prepend' => '$', 
		'helpBlock' => 'A flat rate to be charged',
		'step' => 'any',
	),
	'active' => array('label' => 'Active', 'helpBlock' => 'Will this handling method be used?'),
));
echo $this->Form->submit($add ? 'Add New Handling Charge' : 'Update Handling Charge');
echo $this->Form->end();