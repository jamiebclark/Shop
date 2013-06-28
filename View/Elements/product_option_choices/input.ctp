<?php
$prefix = !empty($prefix) ? "$prefix." : '';
$prefix .= "ProductOptionChoice.$count";
echo $this->Form->inputs(array(
	'fieldset' => false,
	"$prefix.id" => array('type' => 'hidden'),
	"$prefix.title" => array('label' => false, 'placeholder' => 'Option'),
));
