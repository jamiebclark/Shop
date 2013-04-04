<?php
if (!empty($catalogItemCategoryPath)) {
	echo $this->Html->div('catalog-item-category-path');
	$sep = ' \\ ';
	$totalCount = count($catalogItemCategoryPath) - 1;
	foreach ($catalogItemCategoryPath as $count => $catalogItemCategory) {
		$isLast = $count == $totalCount;
		$options = array();
		if ($isLast) {
			$options['class'] = 'last';
		}
		echo $this->Html->link(
			html_entity_decode($catalogItemCategory['CatalogItemCategory']['title']),
			array('action' => 'index', 'category' => $catalogItemCategory['CatalogItemCategory']['id']),
			$options
		);
		if (!$isLast) {
			echo $sep;
		}
	}
	echo "</div>\n";
}
