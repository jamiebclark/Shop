<?php 
$this->Asset->css(array('Shop.style'));
$this->Asset->js(array('Shop.script'));
echo $this->extend('/../../../View/Layouts/default');
?>
<h1>Online Store</h1>
<?php echo $this->Html->div('container', $this->fetch('content'));  ?>
