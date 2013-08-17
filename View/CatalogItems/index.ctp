<?php
list($tableNavTop, $tableNavBottom) = $this->Table->tableNav(array(
	'paginate' => true,
	'sort' => array(
		array('Title: A to Z', 'CatalogItem.title', 'asc'),
		array('Title: Z to A', 'CatalogItem.title', 'desc'),
		array('Price: Low to High', 'CatalogItem.price', 'asc'),
	)
), true);
?>
<div class="row">
	<div class="span2">
		<?php echo $this->element('catalog_item_categories/list');?>
	</div>
	<div class="span10"><?php 
		echo $this->element('Shop.catalog_items/layout_form');
		echo $tableNavTop;
		if ($catalogLayout['layout'] == 'thumb'): ?>
			<div class="row-fluid">
				<?php echo $this->CatalogItem->thumbnails($catalogItems, array('span' => 3, 'caption' => true, 'paginate' => false)); ?>
			</div>
		<?php else: ?>
			<div class="catalogitem-list">
				<?php echo $this->CatalogItem->mediaList($catalogItems); ?>
			</div>
		<?php endif; 
		/*
		if (!isset($sort)) {
			$sort = true;
		}
		if ($sort) {
			echo $this->Layout->tableSortMenu(array(
				array('Title', 'CatalogItem.title'),
				array('Lowest Price', 'CatalogItem.price', 'ASC'),
			));
		}
		*/
		echo $tableNavBottom;
		echo $this->element('Shop.catalog_items/layout_form');
		
		?>
		</div>
	</div>
</div>