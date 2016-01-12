<?php
define('SHOP_ROOT', APP . 'Plugin' . DS . 'Shop' . DS);
define('SHOP_WWW_ROOT', SHOP_ROOT . 'webroot' . DS);
App::uses('ModelViewHelper', 'Layout.View/Helper');
class CatalogItemHelper extends ModelViewHelper {
	public $name = 'CatalogItem';
	public $modelPlugin = 'Shop';
	
	public $helpers = [
		'Html', 
		'Form', 
		'Photo', 
	];
	
	public $thumbDir = 'catalog_item_images/';
	
	public function beforeRender($viewFile, $options = []) {
		$this->Html->css('Shop.style', null, ['inline' => false]);
		return parent::beforeRender($viewFile, $options);
	}
	
	public function media($result, $options = []) {
		$result = $this->_getResult($result);
		$options = array_merge(array(
			'titleTag' => 'h3',
			'right' => $this->price($result),
			'dir' => 'thumb',
		), $options);
		if (empty($result['active'])) {
			$options = $this->addClass($options, 'inactive');
		}
		if (!empty($result['short_description'])) {
			$options['after'] = $this->DisplayText->text($result['short_description'], ['tag' => 'p']);
		}
		return parent::media($result, $options);
	}
	/*
	function media($catalogItem, $options = []) {
		$options = array_merge(array(
			'url' => $this->modelUrl($catalogItem),
			'dir' => 'thumb',
		), $options);
		$thumbOptions = $this->addClass($options, 'media-object');
		$thumb = $this->Html->div('pull-left', $this->thumb($catalogItem, $thumbOptions));
		$title = $catalogItem['title'];
		if (!empty($options['url'])) {
			$title = $this->Html->link($title, $options['url']);
		}
		$body = $this->Html->tag('h2', $title, ['class' => 'media-title']);
		return $this->Html->div('catalogitem media', $thumb . $this->Html->div('media-body', $body));
	}
	*/
	
	/*
	function thumb($catalogItem, $options = []) {
		$src = $this->thumbDir;
		if (!empty($options['dir'])) {
			$options = $this->addClass($options, $options['dir']);
			$src .= $options['dir'] . '/';
			unset($options['dir']);
		}
		if (!empty($options['url']) && $options['url'] === true) {
			$options['url'] = $this->modelUrl($catalogItem);
		}
		$src .= $catalogItem['filename'];
		$file = str_replace(['/','\\'], DS, SHOP_WWW_ROOT . 'img' . DS . $src);
		$filename = 'Shop.' . $src;
		if (is_file($file)) {
			return isset($options['filename']) ? $filename : $this->Html->image($filename, $options);
		} else {
			return null;
		}
	}
	*/
	
	public function thumbOptions($result, $options = []) {
		$options = array_merge([
			'externalServer' => false,
			'root' => SHOP_WWW_ROOT . 'img' . DS,
			'plugin' => 'Shop',
			'defaultFile' => false,
		], $options);
		return parent::thumbOptions($result, $options);
	}
	
	public function link ($CatalogItem, $options = [], $onClick = null) {
		$options = array_merge([
			'class' => '',
			'escape' => true,
		], $options);
		$options['class'] .= ' catalogitem';
		$url = Param::keyCheck($options, 'url', false, $this->modelUrl($CatalogItem));
		
		return $this->Html->link($CatalogItem['title'], $url, $onClick);
	}
	
	public function modelUrl($result, $options = []) {
		$result = $this->_getResult($result);
		return array(
			'controller' => 'catalog_items', 
			'action' => 'view', 
			$result['id'],
			Inflector::slug($result['title']),
			'plugin' => 'shop',
		);
	}
	
	public function notes($catalogItem) {
		$notes = [];
		if ($catalogItem['min_quantity'] > 1) {
			$notes[] = 'Minimum order of ' . number_format($catalogItem['min_quantity']);
		}
		if ($catalogItem['quantity_per_pack'] > 1) {
			$notes[] = 'This is a pack of ' . number_format($catalogItem['quantity_per_pack']);
		}
		if (empty($catalogItem['unlimited']) && $catalogItem['stock'] < 10) {
			$notes[] = 'Limited stock';
		}
		if (empty($notes)) {
			return '';
		} else {
			return $this->Html->div('catalogitem-notes', 
				'<ul><li>'.implode('</li><li>', $notes).'</li></ul>'
			);
		}
	}
	
/**
 * Gets the class associated with how much stock an item has
 * 
 * @param int|Array $qty Either the quantity of stock available, or the result array containing stock and unlimited fields
 * @param bool $unlimited Whether there is an unlimited amount of stock available
 * 
 * @return string CSS class name
 **/
	public function getInventoryClass($qty, $unlimited = false) {
		if (is_array($qty)) {
			return $this->getInventoryClass($qty['stock'], $qty['unlimited']);
		}
		$warning = 10;
		if ($qty <= 0 && !$unlimited) {
			$class = 'error';
		} else {
			$class = ($unlimited || $qty > $warning) ? 'success' : 'warning';
		}
		return $class;
	}
	
	public function inventory($qty = 0, $unlimited = false) {
		if (is_array($qty)) {
			$result = $qty;
			$qty = $result['stock'];
			$unlimited = $result['unlimited'];
		}
		$out = number_format($qty);
		if ($unlimited) {
			$out = 'Unlimited';
			$qty = 1;
		}
		$class = $this->getInventoryClass($qty, $unlimited);
		if ($class == 'error') {
			$class = 'danger';
		}
		$class = 'label label-' . $class;
		return $this->Html->tag('span', $out, compact('class'));
	}
	
	public function price($catalogItem) {
		$out = '';
		if ($catalogItem['sale'] > 0) {
			$out .= $this->cash($catalogItem['sale'], ['class' => 'cash-sale']);
			$out .= ' ';
			$out .= $this->cash($catalogItem['price'], ['class' => 'cash-old']);
		} else {
			$out .= $this->cash($catalogItem['price']);
		}
		return $this->Html->tag('span', $out, ['class' => 'catalogitem-price']);
	}
	
	public function cash($num, $options = []) {
		$options = array_merge(['tag' => 'font'], $options);
		extract($this->addClass($options, 'cash'));
		$out = '$' . number_format($num, $num == round($num) ? 0 : 2);
		if (!empty($tag)) {
			$out = $this->Html->tag($tag, $out, compact('class', 'style'));
		}
		return $out;
	}
	
	public function hasStock($catalogItem) {
		return !empty($catalogItem['stock']) || !empty($catalogItem['unlimited']);
	}
	
	public function categories($catalogItemCategories) {
		$out = '';
		foreach ($catalogItemCategories as $catalogItemCategory) {
			$list = '';
			foreach ($catalogItemCategory as $id => $title) {
				$url = ['controller' => 'catalog_items', 'action' => 'index', 'category' => $id];
				if (!empty($list)) {
					$list .= ' / ';
				}
				$list .= $this->Html->link($title, $url);
			}
			$out .= $this->Html->div('catalogitemcategory',
				$this->Html->tag('span', $list, ['class' => 'badge badge-catalogitem-category'])
			);
		}
		return $this->Html->div('catalogitemcategory-list', $out);
	}
}