<?php
$infoTitle = 'Your Information';
$cartTitle = 'Cart Contents';
if (!$isArchived) {
	$infoTitle = $this->Html->link($infoTitle, array('action' => 'shipping', $order['Order']['id']));
	$cartTitle = $this->Html->link($cartTitle, array('action' => 'view', $order['Order']['id']));
}
$statusOptions = array('mode' => 'definitionList', 'tag' => 'div');
?>
<h1>Finish Checking Out</h1>
<div class="row">
	<div class="span8">
		<div class="row">
			<div class="span4">
				<?php echo $this->element('orders/shipping_status', $statusOptions); ?>
			</div>
			<div class="span4">
				<?php echo $this->element('orders/payment_status', $statusOptions); ?>
			</div>
		</div>
		<?php
		echo $this->Invoice->paymentForm($order['Invoice']);
		?>
	</div>
	<div class="span4">
		<h3><?php echo $cartTitle; ?></h3>
		<?php 
		echo $this->element('orders/cart', array(
			'condensed' => true,
			'links' => false,
			'form' => false,
			'images' => false,
		));
		?>
	</div>
</div>