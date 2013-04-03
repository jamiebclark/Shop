<?php
$defaultCrumbs = array(
	array('Promotion', array('controller' => 'product_promos', 'action' => 'index')),
);
echo $this->element('staff_heading', compact('defaultCrumbs', 'crumbs'));