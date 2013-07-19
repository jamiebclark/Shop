<?php
$prefix = 'CatalogItemImage.';
if (isset($count)) {
	$prefix .= $count . '.';
}
?>
<div class="catalog-item-image-input media">
	<?php if ($this->Html->value($prefix . 'filename')): ?>
		<div class="pull-left">
		<?php 
			echo $this->CatalogItem->thumb($this->Html->value(substr($prefix,0,-1)), array(
				'class' => 'media-object',
				'dir' => 'thumb',
			)); 
		?>
		</div>
	<?php endif; ?>
	<div class="media-body">
	<?php
		echo $this->Form->inputs(array(
			'fieldset' => false,
			$prefix . 'id' => array('type' => 'hidden'),
			$prefix . 'add_file' => array('type' => 'file'),
			$prefix . 'set_thumbnail' => array('type' => 'checkbox', 'label' => 'Make Thumbnail'),
		));
	?>
	</div>
</div>