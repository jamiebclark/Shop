<?php 
$this->Asset->css(array(
	'Shop.style',
	'Shop.bootstrap.min'
));
$this->Asset->js(array(
	'//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js',
	'Shop.script', 
	'Shop.bootstrap.min'
));

echo $this->Asset->output(true);

echo $this->extend('/../../../View/Layouts/default');

echo $this->Html->div('container', $this->fetch('content')); 