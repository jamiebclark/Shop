<?php
$layout = 'thumb';
$paginateNav = $this->Layout->paginateNav();
?>
<div class="row">
	<div class="span2">
		<?php echo $this->element('catalog_item_categories/list');?>
	</div>
	<div class="span10"><?php 
		echo $paginateNav;
		echo $this->element('catalog_items/category_path');
		if ($layout == 'thumb'): ?>
			<div class="row-fluid">
				<?php echo $this->CatalogItem->thumbnails($catalogItems, array('span' => 3, 'caption' => true)); ?>
			</div>
		<?php else: ?>
			<div class="catalog-item-list">
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
		echo $paginateNav;
		?>
		</div>
	</div>
</div>