<?php 
$this->Asset->css(array('Shop.style'));
$this->Asset->js(array('Shop.script'));
echo $this->extend('/../../../View/Layouts/default');
echo $this->Html->div('container', $this->fetch('content')); 