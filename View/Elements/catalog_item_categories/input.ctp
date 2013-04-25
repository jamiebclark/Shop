<?php
$prefix = 'CatalogItemCategory.';
$label = 'Category';
if (isset($count)) {
	$prefix .= $count . '.';
	$label .= ' #' . ($count + 1);
}
if (!isset($options) && isset($catalogItemCategories)) {
	$options = $catalogItemCategories;
}
echo $this->Form->input($prefix . 'id', array('type' => 'select') + compact('label', 'options'));