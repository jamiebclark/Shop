<?php
$add = !$this->Html->value('ProductImage.id');
echo $this->Form->create(null, array('type' => 'file'));
echo $this->Form->hidden('id');
?>
<div class="row">
	<div class="span2">
		<?php echo $this->CatalogItemImage->inputThumb(); ?>
	</div>
	<div class="span9">
		<?php echo $this->Form->input('catalog_item_id', array('helpBlock' => 'Select the which product this image represents')); ?>
	</div>
</div>
<?php echo $this->FormLayout->end($add ? 'Add Image' : 'Update Image');