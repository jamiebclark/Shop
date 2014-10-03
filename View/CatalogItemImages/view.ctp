<?php echo $this->element('Shop.catalog_item_images/crumbs'); ?>
<div class="catalogitem-image-view">
	<div class="catalogitem-image-view-display">
		<?php echo $this->CatalogItem->thumb($catalogItemImage['CatalogItemImage'], array('class' => 'catalogitem-image-view')); ?>
	</div>
	<ul class="pager">
		<?php if (!empty($prevId)): ?>
			<li class="previous"><?php echo $this->Html->link('Previous', array('action' => 'view', $prevId)); ?></li>
		<?php endif; ?>
		<?php if (!empty($nextId)): ?>
			<li class="next"><?php echo $this->Html->link('Next', array('action' => 'view', $nextId)); ?></li>
		<?php endif; ?>	
	</ul>
	<div class="catalogitem-image-view-thumbnails">
	<?php if (count($catalogItemImages) > 1): ?>
		<?php echo $this->element('catalog_item_images/thumb_list', array('perRow' => 6, 'current' => $catalogItemImage['CatalogItemImage']['id'])); ?>
	<?php endif; ?>
</div>

<?php if (!empty($loggedUserTypes['admin'])): ?>
	<?php echo $this->Layout->adminMenu(array(
		'view', 
		'edit', 
		'add' => array(
			'url' => array('action' => 'add', 'admin' => true, $catalogItemImage['CatalogItem']['id'])
			), 
		'delete'
	), array(
		'url' => array(
			'action' => 'view', 
			$catalogItemImage['CatalogItemImage']['id'],
			'admin' => true
		)
	)); ?>
<?php endif; ?>