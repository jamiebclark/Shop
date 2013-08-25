<?php
$tracking = $this->Order->tracking($order);
$info = array(
	'Shipping' => array(
		'Address' => $this->Order->shipping($order['Order']),
		'Email' => $this->AddressBook->email($order['Order']['email']),
		'Phone' => $this->AddressBook->phone($order['Order']['phone']),
		'Shipped' => $this->Order->shipped($order),
		'Track' => $this->Order->tracking($order),
	),
	'Payment' => array(
		'Billing' => $this->Invoice->address($order['Invoice']),
		'Paid' => $this->Order->paid($order),
	)
);
?>
<style type="text/css">
.order-cart table {
	width: 100%;
}
.cart-total.top-total {
	border-top: 1px solid black;
}
.cart-total .total-label {
	text-align: right:
}
.row-total {
	width: 80px;
	text-align: right;
}
dd, dl {
	overflow: hidden;
}
dt {
	font-weight: bold;
	float: left;
	margin-right: 10px;
	width: 120px;
	text-align: right;
}
dd, dt {
	margin-top: 6px;
}
</style>
<?php foreach ($info as $title => $definitions): ?>
	<div>
		<h3><?php echo $title; ?></h3>
		<?php echo $this->Layout->definitionList($definitions); ?>
	</div>
<?php endforeach; ?>
<div>
	<h3>Your Cart</h3>
	<?php echo $this->element('orders/cart', array('images' => false, 'links' => false)); ?>
</div>
<p>You can continue to check the status at <?php echo $this->Html->link(
	'our online store', $this->Order->publicUrl($order['Order'])
);?></p>