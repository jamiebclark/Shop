<div class="row">
	<div class="span2">
		<?php echo $this->element('catalog_item_categories/list');?>
	</div>
	<div class="span10"><?php
		echo $this->Layout->defaultHeader(null, null, array(
			'title' => 'Online Store Products',
		));
		echo $this->element('catalog_items/admin_nav');
		echo $this->element('catalog_items/admin_list');
	?></div>
</div>