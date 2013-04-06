<?php
if (empty($prefix)) {
	$prefix = !empty($class) ? $class . '.' : '';
}

$index = 1;
foreach ($catalogItemOptions as $title => $catalogOptionChoices):
	echo $this->Form->input($prefix . 'product_option_choice_id_' . $index, array(
		'type' => 'select',
		'options' => array('' => ' -- Select a ' . $title . ' --') + $catalogOptionChoices,
		'label' => $title,
	));
	$index++;
endforeach;