<?php
echo $this->element('product_promos/staff_heading', array(
	'crumbs' => array(
		$productPromo['ProductPromo']['title'],
	)
));

echo $this->Html->tag('h1', $productPromo['ProductPromo']['title']);
echo $this->Layout->headerMenu(array(
	array('Edit Promo', array('action' => 'edit', $productPromo['ProductPromo']['id'])),
	array('Delete Promo', array('action' => 'delete', $productPromo['ProductPromo']['id']), null, 'Delete this charge?'),
));
echo $this->Layout->infoResultTable($productPromo['ProductPromo'], array(
	'title',
	'code',
	'pct' => array('format' => 'percent'),
	'amt' => array('format' => 'cash'),
	'started' => array('format' => 'date', 'label' => 'Starts'),
	'stopped' => array('format' => 'date', 'label' => 'Ends',),
	'active' => array('format' => 'yesno'),
));
?>