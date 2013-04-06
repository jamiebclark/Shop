<h1>Your Cart</h1>
<?php
echo $this->element('orders/cart', array(
	'form' => true,
	'links' => true,
	'photos' => true,
));
?>
<div class="row">
	<div class="span6"><?php 
		echo $this->element('orders/shipping_status', array('blank' => false));
	?></div>
	<div class="span6"><?php
		echo $this->element('orders/payment_status', array('blank' => false));
	?></div>
</div>