<?php
if (is_array($product)) {
	$defaultCrumbs = array(
		array($product['Product']['title'], $this->Product->url($product['Product'])),
		array('Images', array('action' => 'index', $product['Product']['id'])),
	);
	if (empty($crumbs)) {
		$crumbs = $defaultCrumbs;
	} else {
		$crumbs = array_merge($defaultCrumbs, (array) $crumbs);
	}
	echo $this->element('products/crumbs', compact('crumbs'));
} else {
	$defaultCrumbs = array(
		array('Images', array('action' => 'index')),
	);

	echo $this->element('layouts/add_crumbs', compact('defaultCrumbs', 'crumbs'));
}

?>