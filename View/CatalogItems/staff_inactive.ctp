<?php
echo $this->element('products/admin_heading', array(
	'crumbs' => 'Inactive Products',
));
echo $this->Html->tag('h1', 'Inactive Store Products');
echo $this->element('products/admin_list');
?>