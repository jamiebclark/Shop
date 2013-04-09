<?php
echo $this->Layout->defaultHeader();
foreach ($promoCodes as $promoCode) {
	$url = array(
		'controller' => 'promo_codes',
		'action' => 'view',
		$promoCode['PromoCode']['id'],
	);
	$active = $promoCode['PromoCode']['active'];
	
	$past = !empty($promoCode['PromoCode']['stopped']) && ($promoCode['PromoCode']['stopped'] < date('Y-m-d H:i:s'));
	$class = $active && !$past ? 'active' : 'inactive';
	
	$this->Table->cells(array(
		array($this->Html->link($promoCode['PromoCode']['title'],$url), 'Title'),
		array($promoCode['PromoCode']['code'], 'Code'),
		array(($promoCode['PromoCode']['pct'] * 100) . '%', 'Percent'),
		array($this->DisplayText->cash($promoCode['PromoCode']['amt']), 'Amount'),
		array($this->Calendar->niceShort($promoCode['PromoCode']['started']), 'Starts'),
		array($this->Calendar->niceShort($promoCode['PromoCode']['stopped']), 'Ends'),
		array(
			$this->Layout->actionMenu(array('view', 'edit', 'delete', 'active'), compact('url', 'active')), 
			'Actions',
			null,
			null,
			array('width' => 120)
		),
	), compact('class'));
}
echo $this->Table->output(array('paginate' => true));