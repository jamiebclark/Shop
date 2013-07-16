<?php if (empty($shoppingCart) && isset($blankEmpty)) {
	return '';
}
?>
<?php if (!empty($shoppingCart)): ?>
	<div class="shop-cart-heading">
		<?php echo $this->Html->link(
			'<strong><i class="icon-shopping-cart"></i> Your Cart</strong> ' . $this->DisplayText->cash($shoppingCart['Order']['total']),
			array(
				'controller' => 'orders',
				'action' => 'view',
				$shoppingCart['Order']['id'],
				'plugin' => 'shop',
			), 
			array('escape' => false, 'title' => 'View your shopping cart')
		);
		?>
	</div>
<?php endif; ?>