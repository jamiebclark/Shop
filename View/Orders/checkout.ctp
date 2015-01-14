<?php
$infoTitle = 'Your Information';
$cartTitle = 'Cart Contents';
if (!$isArchived) {
	$infoTitle = $this->Html->link($infoTitle, array('action' => 'shipping', $order['Order']['id']));
	$cartTitle = $this->Html->link($cartTitle, array('action' => 'view', $order['Order']['id']));
}
$statusOptions = array('mode' => 'definitionList', 'tag' => 'div');
?>
<h2>Finish Checking Out</h2>
<div class="row">
	<div class="col-sm-8">
		<div class="row">
			<div class="col-md-6">
				<?php echo $this->element('orders/shipping_status', $statusOptions); ?>
			</div>
			<div class="col-md-6">
				<?php echo $this->element('orders/payment_status', $statusOptions); ?>
			</div>
		</div>
		<?php echo $this->Invoice->paymentForm($order['Invoice']); ?>
	</div>
	<div class="col-sm-4"><?php 
		echo $this->element('orders/cart', array(
			'condensed' => true,
			'links' => false,
			'form' => false,
			'images' => false,
			'title' => 'Cart Contents',
			'titleTag' => 'h3',
		));
	?></div>
</div>