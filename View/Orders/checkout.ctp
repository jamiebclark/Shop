<?php
$archived = $order['Order']['archived'];

echo $this->element('products/crumbs', array(
	'crumbs' => array(
		array('Your Order', array('action' => 'view', $order['Order']['id'])),
		array('Shipping / Billing Info', array('action' => 'shipping', $order['Order']['id'])),
		array('Payment'),		
	)
));

echo $this->Grid->col('3/4');

echo $this->Html->tag('h1', 'Payment Options', array('class' => 'topTitle'));
echo $this->Invoice->paymentForm($order['Invoice']);

echo $this->Grid->colContinue('1/4');
$title = 'Your Information';
if (!$archived) {
	$title = $this->Html->link($title, array('action' => 'shipping', $order['Order']['id']));
}
echo $this->Html->tag('h2', $title);
$statusOptions = array('mode' => 'definitionList', 'tag' => 'div');
echo $this->element('orders/shipping_status', $statusOptions);
echo "<hr/>\n";
echo $this->element('orders/payment_status', $statusOptions);
echo "<hr/>\n";

$title = 'Cart Contents';
if (!$archived) {
	$title = $this->Html->link($title, array('action' => 'view', $order['Order']['id']));
}
echo $this->Html->tag('h2', $title);
echo $this->element('orders/cart', array(
	'links' => false,
	'form' => false,
	'images' => false,
));

echo $this->Grid->colClose(true);

/*			
echo $this->PaypalForm->create();
$cols = array(
	'first_name',
	'last_name',
	'address1' => 'addline1',
	'address2' => 'addline2',
	'city',
	'state',
	'zip',
	'email',
	'country',
	'day_phone' => 'phone',
	'night_phone' => 'phone',
);
$settings = array(
//	'cmd' => '_cart',
	'amount' => $order['Order']['total'],
	'item_name' => 'Online Store',
	'item_number' => $order['Order']['id'],
	'invoice' => $order['Invoice']['id'],
	
	'handling_cart' => $order['Order']['handling'],
	'discount_amount_cart' => $order['Order']['promo_discount'],

	'return' => $this->Html->url(array('controller' => 'orders', 'action' => 'view', $order['Order']['id']), true),
	'cancel_return' => $this->Html->url(array('controller' => 'orders', 'action' => 'checkout', $order['Order']['id']), true),
	
);
foreach ($cols as $paypalCol => $dbCol) {
	if (is_numeric($paypalCol)) {
		$paypalCol = $dbCol;
	}
	$settings[$paypalCol] = $order['Order'][$dbCol];
}
echo $this->PaypalForm->inputSettings($settings);
echo $this->FormLayout->submit('Pay with Paypal');
echo $this->PaypalForm->end();
*/
?>