<?php
$defaultCrumbs = array(
	array('Categories', array('controller' => 'product_categories', 'action' => 'index')),
);
echo $this->element('staff_heading', compact('defaultCrumbs', 'crumbs'));
?>