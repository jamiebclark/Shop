<?php
if (!empty($productCategoryPath)) {
	echo $this->Html->div('productCategoryPath');
	$sep = ' \\ ';
	$totalCount = count($productCategoryPath) - 1;
	foreach ($productCategoryPath as $count => $productCategory) {
		$isLast = $count == $totalCount;
		$options = array();
		if ($isLast) {
			$options['class'] = 'last';
		}
		echo $this->Html->link(
			html_entity_decode($productCategory['ProductCategory']['title']),
			array('action' => 'index', 'category' => $productCategory['ProductCategory']['id']),
			$options
		);
		if (!$isLast) {
			echo $sep;
		}
	}
	echo "</div>\n";
}
?>