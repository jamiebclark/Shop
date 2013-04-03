<?php
$output = '';
if (!empty($shoppingCart)) {
	$alt = 'View the contents of your cart';
	$url = array(
		'controller' => 'cart',
		'action' => 'view',
		$shoppingCart['Order']['id'],
	);
	
	$output .= $this->Html->div('shoppingCartHeading');
	$output .=  $this->Html->link(
		$this->Html->image('icn/16x16/cart.png', compact('alt')),
		$url, array(
			'escape' => false,
			'title' => $alt,
		)
	);

	$output .= ' ';

	if ($shoppingCart['Order']['order_product_count'] == 0) {
		$display = 'Cart Empty';
	} else {
		$display = $this->DisplayText->cash($shoppingCart['Order']['total']);
	}	
	$output .= $this->Html->link($display, $url, array('title' => $alt));
	
	$output .=  "</div>\n";
	
	$this->viewVars['pre_crumb'] = $output;
}
?>