<div id="header">
	<h1><?php echo $this->Html->link(COMPANY_NAME . ' Online Store', array(
		'controller' => 'catalog_items', 'action' => 'index', 'prefix' => 'Shop'
	)); ?></h1>
</div>

<div id="content">
	<?php echo $this->fetch('content'); ?>
</div>

<div id="footer">
	<?php if (!empty($order)): ?>
		<div class="disclaimer">
		You are being sent this message regarding the status of an <?php echo $this->Html->link(
			'order you placed online',
			$this->Order->publicUrl($order['Order'])
		);?>. You have not been subscribed to any lists as a result of this.
		</div>
	<?php endif; ?>
</div>