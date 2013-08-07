<div class="catalog-item-category-list">
<?php
if (!empty($catalogItemCategory)) {
	$title = '';
	if (!empty($catalogItemCategory['CatalogItemCategory']['parent_id'])) {
		$title .= $this->Html->link(
			$this->Html->image('icn/16x16/folder_up.png'),
			array('category' => $catalogItemCategory['CatalogItemCategory']['parent_id']),
			array('escape' => false)
		) . ' ';
		$title .= $catalogItemCategory['CatalogItemCategory']['title'];
	} else {
		$title = 'Categories';
	}
	echo $this->Html->tag('h4', $title);
}
if (!empty($catalogItemCategories)) {
	$list = array();
	foreach ($catalogItemCategories as $id => $title) {
		$list[] = array(
			$title,
			array('action' => 'index', $id)
		);
	}
	echo $this->Layout->menu($list, array(
		'tag' => false,
		'class' => 'nav-catalogitemcategories nav nav-pills nav-stacked'
	));
} else {
	echo $this->Html->tag('em', 'No Categories');
}
?>
</div>