<?php
$add = !$this->Html->value('ProductImage.id');
echo $this->Form->create(null, array('type' => 'file'));
echo $this->Form->hidden('id');
?>
<div class="media">
	<div class="media-object pull-left">
		<?php echo $this->CatalogItemImage->inputThumb(); ?>
	</div>
	<div class="media-body">
		<?php echo $this->Form->input('catalog_item_id', array('helpBlock' => 'Select the which product this image represents')); ?>
	</div>
</div>
<?php echo $this->FormLayout->end($add ? 'Add Image' : 'Update Image');