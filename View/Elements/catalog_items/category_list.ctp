<?php
echo $this->Html->div('productCategoryList');
if (!empty($productCategory)) {
	$title = '';
	if (!empty($productCategory['ProductCategory']['parent_id'])) {
		$title .= $this->Html->link(
			$this->Html->image('icn/16x16/folder_up.png'),
			array('category' => $productCategory['ProductCategory']['parent_id']),
			array('escape' => false)
		) . ' ';
		$title .= $productCategory['ProductCategory']['title'];
	} else {
		$title = 'Categories';
	}
	echo $this->Html->tag('h2', $title);
}
if (!empty($productCategories)) {
	$list = array();
	foreach ($productCategories as $id => $title) {
		$list[] = array(
			$title,
			array('action' => 'index', $id)
		);
	}
	echo $this->Layout->menu($list, array('class' => 'blankList secondary'));
} else {
	echo $this->Html->tag('em', 'No Categories');
}
echo "</div>\n";
?>