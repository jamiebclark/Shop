<?php
if (empty($prefix)) {
	$prefix = !empty($class) ? $class . '.' : '';
}

$index = 1;
foreach ($catalogItemOptions as $row):
	$catalogItemOption = isset($row['CatalogItemOption']) ? $row['CatalogItemOption'] : $row;
	$title = $catalogItemOption['title'];
	$options = array('' => ' -- Select a ' . $title . ' --');
	foreach ($row['ProductOptionChoice'] as $productOptionsChoice) {
		$options[$productOptionsChoice['id']] = $productOptionsChoice['title'];
	}
	echo $this->Form->input($prefix . 'product_option_choice_id_' . $index, array(
		'type' => 'select',
		'options' => $options,
		'label' => $title,
		'class' => 'form-control element-input-list-key',
	));
	$index++;
endforeach;