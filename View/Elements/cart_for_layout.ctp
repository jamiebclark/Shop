<?php 

if (empty($shoppingCart) && isset($blankEmpty)) {
	return '';
}
?>
<?php if (!empty($shoppingCart)): ?>
	<div id="shop-cart-heading" class="btn-group"><?php 
		echo $this->Html->link(
			'<strong><i class="icon-shopping-cart"></i> Your Cart</strong> ' . $this->DisplayText->cash($shoppingCart['Order']['total']),
			array(
				'controller' => 'orders',
				'action' => 'view',
				$shoppingCart['Order']['id'],
				'plugin' => 'shop',
			) + Prefix::reset(), 
			array(
				'escape' => false, 
				'title' => 'View your shopping cart',
				'class' => 'btn btn-default',
			)
		);
		echo $this->Html->link(
			$this->Iconic->icon('x'),
			array('unset_cart' => 1),
			array(
				'escape' => false,
				'title' => 'Empty your shopping cart',
				'class' => 'btn btn-default',
			),
			'Empty your cart?'
		);
	?></div>
<?php endif; ?>