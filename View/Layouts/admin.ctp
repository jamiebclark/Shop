<?php $this->extend('Layout.default'); ?>
<div class="ajax-modal-hide">
	<?php
	echo $this->Layout->navBar([
		['Orders', ['controller' => 'orders', 'action' => 'index']],
		['Catalog Items', ['controller' => 'catalog_items', 'action' => 'index']],
		['Categories', ['controller' => 'catalog_item_categories', 'action' => 'index']],
		['Inventory', ['controller' => 'products', 'action' => 'index']],
		['Handling', ['controller' => 'handling_methods', 'action' => 'index']],
		['Promos', ['controller' => 'promo_codes', 'action' => 'index']],
		['Shipping', ['controller' => 'shipping_methods', 'action' => 'index']],
		['Invoices', ['controller' => 'invoices', 'action' => 'index']],
		['PayPal', ['controller' => 'paypal_payments', 'action' => 'logs']],
		['Settings', ['controller' => 'shop_settings', 'action' => 'index']],
	], 'Online Store', ['currentSelect' => ['controller']]);
	?>
</div>
<?php echo $this->fetch('content'); ?>
