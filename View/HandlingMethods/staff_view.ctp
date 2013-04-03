<?php
echo $this->element('product_handlings/staff_heading', array(
	'crumbs' => array(
		$productHandling['ProductHandling']['title'],
	)
));

echo $this->Html->tag('h1', $productHandling['ProductHandling']['title']);
echo $this->Layout->headerMenu(array(
	array('Edit Handling Charge', array('action' => 'edit', $productHandling['ProductHandling']['id'])),
	array('Delete Handling Charge', array('action' => 'delete', $productHandling['ProductHandling']['id']), null, 'Delete this charge?'),
));
echo $this->Layout->infoResultTable($productHandling['ProductHandling'], array(
	'title',
	'pct' => array('format' => 'percent'),
	'amt' => array('format' => 'cash'),
	'active' => array('format' => 'yesno'),
));
?>