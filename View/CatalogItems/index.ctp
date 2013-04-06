<div class="row">
	<div class="span2">
		<?php echo $this->element('catalog_items/category_list');?>
	</div>
	<div class="span10">
		<div class="catalog-item-list"><?php
		echo $this->element('catalog_items/category_path');
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
		$dir = 'thumb';
		echo $this->Layout->paginateNav();
		foreach ($catalogItems as $catalogItem):
			$catalogItem = $catalogItem['CatalogItem'];
			$url = $this->CatalogItem->url($catalogItem);
			?>
			<div class="catalog-item-list-item media">
				<div class="img"><?php
					echo $this->CatalogItem->thumb($catalogItem, compact('url', 'dir'));
				?></div>
				<div class="rght"><?php 
					echo $this->CatalogItem->price($catalogItem);
				?></div>
				<div class="bd"><?php
					echo $this->Html->tag('h3', $this->CatalogItem->link($catalogItem));
					if (!empty($catalogItem['short_description'])) {
						echo $this->DisplayText->text($catalogItem['short_description']);
					}
				?></div>	
			</div>
		<?php endforeach;
		echo $this->Layout->paginateNav();
		?>
		</div>
	</div>
</div>