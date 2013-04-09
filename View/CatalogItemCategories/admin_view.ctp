<?php
echo $this->element('product_categories/admin_heading', array(
	'crumbs' => array(
		$productCategory['ProductCategory']['title'],
	)
));

echo $this->Html->tag('h1', $productCategory['ProductCategory']['title']);
echo $this->Layout->headerMenu(array(
	array('Edit Category', array('action' => 'edit', $productCategory['ProductCategory']['id'])),
	array('Delete Category', array('action' => 'delete', $productCategory['ProductCategory']['id']), null, 'Delete this charge?'),
));
/*
echo $this->Layout->infoResultTable($productCategory['ProductCategory'], array(
	'title',
	'pct' => array('format' => 'percent'),
	'amt' => array('format' => 'cash'),
	'active' => array('format' => 'yesno'),
));
*/
?>