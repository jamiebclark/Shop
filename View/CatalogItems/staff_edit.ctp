<?php
echo $this->element('products/staff_heading', array(
	'crumbs' => array(
		array($this->Html->value('Product.title'), array('action' => 'view', $this->Html->value('Product.id'))),
		'Edit',
	)
));
echo $this->element('products/form');
?>