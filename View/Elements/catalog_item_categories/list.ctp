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
<<<<<<< HEAD
			echo $this->Html->div('panel-heading', $title);
		endif;
=======
			?>
			<div class="panel-heading"><?php echo $title; ?></div>
		<?php endif;
		
>>>>>>> 8d2b9ace26644135d86e336e52568999b1972ed1
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
<<<<<<< HEAD
			<div class="panel-body"><em>No Categories</em></div>
=======
			<div class="panel-body"><em>No categories</em></div>
>>>>>>> 8d2b9ace26644135d86e336e52568999b1972ed1
		<?php endif; ?>
	</div>
</div>