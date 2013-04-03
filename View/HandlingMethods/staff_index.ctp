<?php
echo $this->element('product_handlings/staff_heading');
echo $this->Html->tag('h1', 'Handling Charges');
echo $this->Layout->headerMenu(array(
	array('Add New Charge', array('action' => 'add'))
));
$this->Table->reset();
foreach ($productHandlings as $productHandling) {
	$url = array(
		'controller' => 'product_handlings',
		'action' => 'view',
		$productHandling['ProductHandling']['id'],
	);
	$active = $productHandling['ProductHandling']['active'];
	$class = $active ? 'active' : 'inactive';
	
	$this->Table->cells(array(
		array($this->Html->link($productHandling['ProductHandling']['title'],$url, compact('class')), 'Title'),
		array(($productHandling['ProductHandling']['pct'] * 100) . '%', 'Percent'),
		array($this->DisplayText->cash($productHandling['ProductHandling']['amt']), 'Amount'),
		array(
			$this->Layout->actionMenu(array('view', 'edit', 'delete', 'active'), compact('url', 'active')), 
			'Actions',
			null,
			null,
			array('width' => 120)
		),
	), true);
}
echo $this->Table->table(array('paginate'));
?>