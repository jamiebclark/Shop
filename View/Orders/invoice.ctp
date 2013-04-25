<h1>Order #<?php echo $order['Order']['id'];?></h1>
<div class="order-invoice">
	<?php
	$this->Table->reset();
	$this->Table->cells(array(
		array($this->element('orders/shipping_status')),
		array($this->element('orders/payment_status'))
	), true);
	echo $this->Table->output();
	
	echo $this->element('orders/cart', array(
		'links' => false,
		'form' => false,
		'images' => false,
	));
?>
</div>