<div class="content-width-full">
	<h2>Your Cart</h2>
	<?php
	echo $this->element('orders/cart', array(
		'form' => true,
		'links' => true,
		'photos' => true,
	));
	?>
	<div class="row">
		<div class="col-sm-6"><?php 
			echo $this->element('orders/shipping_status', array('blank' => false));
		?></div>
		<div class="col-sm-6"><?php
			echo $this->element('orders/payment_status', array('blank' => false));
		?></div>
	</div>
</div>