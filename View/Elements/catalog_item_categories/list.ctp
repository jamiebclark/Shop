<div class="catalogitemcategory-list scrollfix">
	<div class="panel panel-default"><?php
		if (!empty($catalogItemCategory)):
			$title = '';
			if (!empty($catalogItemCategory['CatalogItemCategory']['parent_id'])) {
				$title = $this->Iconic->icon('arrow_up');
				$title .= " {$catalogItemCategory['CatalogItemCategory']['title']}";
				$title = $this->Html->link(
					$title, 
					array('category' => $catalogItemCategory['CatalogItemCategory']['parent_id']),
					array('escape' => false)
				);
			} else {
				$title = 'Categories';
			}
			echo $this->Html->div('panel-heading', $title);
		endif;
		if (!empty($catalogItemCategories)):
			$list = array();
			foreach ($catalogItemCategories as $catalogItemCategory) {
				$catalogItemCategory = $catalogItemCategory['CatalogItemCategory'];
				$id = $catalogItemCategory['id'];
				$title = $catalogItemCategory['title'];
				$count = number_format($catalogItemCategory['active_catalog_item_count']);
				$title .= " <em>($count)</em>";
				$list[] = array($title, array('action' => 'index', $id));
			}
			echo $this->Layout->nav($list, array('class' => 'nav-catalogitemcategories nav-pills nav-stacked'));
		else: ?>
			<div class="panel-body"><em>No Categories</em></div>
		<?php endif; ?>
	</div>
</div>