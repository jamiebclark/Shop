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
	'title' => array('after' => '<span class="help-block">A name for this handling we can refer to in the system</span>'),
	'pct' => array(
		'beforeInput' => '<div class="input-group">',
		'afterInput' => '<span class="input-group-addon">%</span></div>', 
		'after' => '<span class="help-block">A percentage of the total to be charged</span>',
		'step' => 'any',
	),
	'amt' => array(
		'beforeInput' => '<div class="input-group"><span class="input-group-addon">$</span>',
		'afterInput' => '</div>', 
		'after' => '<span class="help-block">A flat rate to be charged</span>',
		'step' => 'any',
	),
	'active' => array('label' => 'Active', 'class' => 'checkbox', 'after' => '<span class="help-block">Will this handling method be used?</span>'),
));
echo $this->Form->submit($add ? 'Add New Handling Charge' : 'Update Handling Charge');
echo $this->Form->end();