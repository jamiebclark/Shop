<?php if (empty($shoppingCart) && isset($blankEmpty)) {
	return '';
}
?>
<div class="shop-cart-heading">
<?php if (!empty($shoppingCart)): ?>
	
		<?php echo $this->Html->link(
			'<strong>Your Cart</strong> ' . $this->DisplayText->cash($shoppingCart['Order']['total']),
			array(
				'controller' => 'orders',
				'action' => 'view',
				$shoppingCart['Order']['id']
			), 
			array(
				'escape' => false,
				'title' => 'View your shopping cart',
			)
		);
		?>
	</div>
<?php endif; ?>