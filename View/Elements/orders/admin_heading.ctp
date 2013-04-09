<?php
if (empty($defaultCrumbs)) {
	$defaultCrumbs = array();
}
$defaultCrumbs = array_merge(array(
	array('Orders', array('controller' => 'orders', 'action' => 'index'))
), $defaultCrumbs);

echo $this->element('admin_heading', compact('defaultCrumbs', 'crumbs'));
