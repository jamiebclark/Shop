<div class="catalogitem-category-list">
<?php
if (!empty($catalogItemCategory)) {
	$title = '';
	if (!empty($catalogItemCategory['CatalogItemCategory']['parent_id'])) {
		echo $this->Html->tag('h5', $this->Html->link(
			$this->Html->image('icn/16x16/folder_up.png') . ' Up',
			array('category' => $catalogItemCategory['CatalogItemCategory']['parent_id']),
			array('escape' => false)
		));
		$title = $catalogItemCategory['CatalogItemCategory']['title'];
	} else {
		$title = 'Categories';
	}
	echo $this->Html->tag('h4', $title);
}
if (!empty($catalogItemCategories)) {
	$list = array();
	foreach ($catalogItemCategories as $catalogItemCategory) {
		$catalogItemCategory = $catalogItemCategory['CatalogItemCategory'];
		$id = $catalogItemCategory['id'];
		$title = $catalogItemCategory['title'];
		$count = number_format($catalogItemCategory['active_catalog_item_count']);
		$title .= " ($count)";
		$list[] = array($title, array('action' => 'index', $id));
	}
	echo $this->Layout->nav($list, array('class' => 'nav-catalogitemcategories nav-pills nav-stacked'));
} else {
	echo $this->Html->tag('em', 'No Categories');
}
?>
</div>