<?php
if (!$isAjax) {
	echo $this->element('product_categories/staff_heading');
	echo $this->Html->tag('h1', 'Categories');
	echo $this->Layout->headerMenu(array(
		array('Add New Category', array('action' => 'add'))
	));
}

echo $this->element('threaded/list', array(
	//'result' => $productCategories,
	//'modelName' => 'ProductCategory',
));
/*
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
		array($this->DisplayText->cash($productHandling['ProductHandling']['pct']), 'Amount'),
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
*/
?>