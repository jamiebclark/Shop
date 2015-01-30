<?php 
$this->Asset->css(array('Shop.style'));
$this->Asset->js(array('Shop.script'));

echo $this->extend('/../../../View/Layouts/' . $this->layout);
?>
<div class="container">
	<?php echo $this->element('Shop.orders/shipping_cutoff_msg'); ?>
	<?php echo $this->fetch('content'); ?>
</div>
