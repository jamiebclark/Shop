<?php
echo $this->element('product_promos/staff_heading');
echo $this->Html->tag('h1', 'Promotional Codes');
echo $this->Layout->headerMenu(array(
	array('Add New Promo', array('action' => 'add'))
));

$this->Table->reset();
foreach ($productPromos as $productPromo) {
	$url = array(
		'controller' => 'product_promos',
		'action' => 'view',
		$productPromo['ProductPromo']['id'],
	);
	$active = $productPromo['ProductPromo']['active'];
	
	$past = !empty($productPromo['ProductPromo']['stopped']) && ($productPromo['ProductPromo']['stopped'] < date('Y-m-d H:i:s'));
	$class = $active && !$past ? 'active' : 'inactive';
	
	$this->Table->cells(array(
		array($this->Html->link($productPromo['ProductPromo']['title'],$url, compact('class')), 'Title'),
		array($productPromo['ProductPromo']['code'], 'Code'),
		array(($productPromo['ProductPromo']['pct'] * 100) . '%', 'Percent'),
		array($this->DisplayText->cash($productPromo['ProductPromo']['amt']), 'Amount'),
		array($this->Calendar->niceShort($productPromo['ProductPromo']['started']), 'Starts'),
		array($this->Calendar->niceShort($productPromo['ProductPromo']['stopped']), 'Ends'),
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