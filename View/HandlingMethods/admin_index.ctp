<?php
echo $this->Layout->defaultHeader();
$this->Table->reset();
foreach ($handlingMethods as $handlingMethod) {
	$url = array(
		'controller' => 'handling_methods',
		'action' => 'view',
		$handlingMethod['HandlingMethod']['id'],
	);
	$active = $handlingMethod['HandlingMethod']['active'];
	$class = $active ? null : 'inactive';
	
	$this->Table->checkbox($handlingMethod['HandlingMethod']['id']);
	$this->Table->cells(array(
		array($this->Html->link($handlingMethod['HandlingMethod']['title'],$url), 'Title'),
		array(($handlingMethod['HandlingMethod']['pct'] * 100) . '%', 'Percent'),
		array($this->DisplayText->cash($handlingMethod['HandlingMethod']['amt']), 'Amount'),
		array(
			$this->ModelView->actionMenu(array('view', 'edit', 'delete', 'active'), compact('url', 'active')), 
			'Actions',
			null,
			null,
			array('width' => 120)
		),
	), compact('class'));
}
echo $this->Table->output(array(
	'paginate' => true,
	'withChecked' => array('active', 'inactive', 'delete')
));