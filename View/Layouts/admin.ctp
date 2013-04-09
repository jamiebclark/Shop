<?php
$this->extend('default');
?>
<div class="navbar">
	<div class="navbar-inner">
	<span class="brand">Online Store</span>
	<?php
	echo $this->Layout->menu(array(
		array('Orders', array('controller' => 'orders', 'action' => 'index')),
		array('Catalog Items', array('controller' => 'catalog_items', 'action' => 'index')),
		array('Inventory', array('controller' => 'products', 'action' => 'index')),
		array('Handling', array('controller' => 'handling_methods', 'action' => 'index')),
		array('Promo Codes', array('controller' => 'promo_codes', 'action' => 'index')),
	), array('class' => 'nav', 'tag' => false, 'currentSelect' => array('controller')));
	?>
	</div>
</div>
<?php
echo $this->fetch('content');
