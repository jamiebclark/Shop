<?php
$infoTitle = 'Your Information';
$cartTitle = 'Cart Contents';
if (!$isArchived) {
	$infoTitle = $this->Html->link($infoTitle, array('action' => 'shipping', $order['Order']['id']));
	$cartTitle = $this->Html->link($cartTitle, array('action' => 'view', $order['Order']['id']));
}
$statusOptions = array('mode' => 'definitionList', 'tag' => 'div');
?>
<div class="row">
	<div class="span8">
		<h1>Payment Options</h1>
		<?php
		echo $this->Invoice->paymentForm($order['Invoice']);
		?>
	</div>
	<div class="span4">
		<h2><?php echo $infoTitle; ?></h2>
		<?php echo $this->element('orders/shipping_status', $statusOptions); ?>
		<hr/>
		<?php echo $this->element('orders/payment_status', $statusOptions); ?>
		<hr/>
		<h2><?php echo $cartTitle; ?></h2>
		<?php 
		$this->element('orders/cart', array(
			'links' => false,
			'form' => false,
			'images' => false,
		));
		?>
	</div>
</div>