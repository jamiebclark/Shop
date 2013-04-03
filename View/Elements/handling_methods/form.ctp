<?php
$add = !$this->Html->value('ProductHandling.id');
if ($add) {
	$crumbs = array(
		'Add Handling',
	);
} else {
	$crumbs = array(
		array($this->Html->value('ProductHandling.title'), array('action' => 'view', $this->Html->value('ProductHandling.id'))),
		'Edit Handling',
	);
}

echo $this->Form->create('ProductHandling');
echo $this->Form->inputs(array(
	'legend' => 'Handling Charge',
	'id',
	'title',
	'pct',
	'amt',
	'active' => array('default' => 1),
));
echo $this->FormLayout->submit($add ? 'Add New Handling Charge' : 'Update Handling Charge');
echo $this->Form->end();
?>