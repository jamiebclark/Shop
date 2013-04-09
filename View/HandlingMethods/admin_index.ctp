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
	$class = $active ? 'active' : 'inactive';
	
	$this->Table->cells(array(
		array($this->Html->link($handlingMethod['HandlingMethod']['title'],$url), 'Title'),
		array(($handlingMethod['HandlingMethod']['pct'] * 100) . '%', 'Percent'),
		array($this->DisplayText->cash($handlingMethod['HandlingMethod']['amt']), 'Amount'),
		array(
			$this->Layout->actionMenu(array('view', 'edit', 'delete', 'active'), compact('url', 'active')), 
			'Actions',
			null,
			null,
			array('width' => 120)
		),
	), compact('class'));
}
echo $this->Table->output(array('paginate' => true));
?>