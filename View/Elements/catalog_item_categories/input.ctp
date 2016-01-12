<?php
$default = null;
if (isset($count)) {
	$field = "CatalogItemCategory.$count.id";
	$label = false;
	if (isset($catalogItem['CatalogItemCategory']['id'])) {
		$default = $catalogItem['CatalogItemCategory']['id'];
	}
} else {
	$label = 'Category';
	$field = 'CatalogItemCategory.id';
}
	
if (!isset($options) && isset($catalogItemCategories)) {
	$options = $catalogItemCategories;
}

echo $this->Form->input($field, array(
	'type' => 'select',
	'class' => 'form-control select-collapse element-input-list-key',
) + compact('label', 'options', 'default'));