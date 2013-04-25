<?php 
$this->Asset->css(array('Shop.style'));
$this->Asset->js(array('Shop.script'));
echo $this->extend('/../../../View/Layouts/default');

echo $this->element('Shop.cart_heading');
echo $this->Html->div('container', $this->fetch('content')); 