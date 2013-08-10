<?php
$layout = 'thumb';
//$paginateNav = $this->Layout->paginateNav();
list($tableNavTop, $tableNavBottom) = $this->Table->tableNav(array(
	'paginate' => true,
	'sort' => array(
		array('Title', 'CatalogItem.title', 'asc'),
		array('Price', 'CatalogItem.price', 'asc'),
	)
), true);


?>
<div class="row">
	<div class="span2">
		<?php echo $this->element('catalog_item_categories/list');?>
	</div>
	<div class="span10"><?php 
		echo $tableNavTop;
		if ($this->Html->value('CatalogItem.layout') == 'thumb'): ?>
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
		
		echo $this->Form->create('CatalogItem', array('class' => 'text-right form-inline'));
		echo $this->Form->input('layout', array(
			'label' => ' Layout: ',
			'div' => false, 
			'type' => 'select', 
			'default' => $layoutDefault['layout'],
			'style' => 'width: auto;',
		));
		echo $this->Form->input('per_page', array(
			'div' => false, 
			'type' => 'select', 
			'default' => $layoutDefault['per_page'],
			'label' => ' Per-Page: ',
			'style' => 'width: auto;',
		));
		echo $this->Form->end('Submit');
		
		?>
		</div>
	</div>
</div>