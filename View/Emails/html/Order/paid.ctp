<h2>Your order has been paid.</h2>
<h3><?php echo $this->Order->publicLink($order['Order']);?></h3>
<p>Just a quick note to let you know your order has been paid</p>
<?php echo $this->element('Shop.orders/email_info', compact('order')); ?>