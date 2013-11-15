<?php
class ShopSettingsController extends ShopAppController {
	var $name = 'ShopSettings';
	var $helpers = array('Shop.ShopSetting');
	
	function admin_index() {
		if (!empty($this->request->data)) {
			$data = array_values($this->request->data['ShopSetting']);
			if ($this->ShopSetting->saveAll($data)) {
				$this->FormData->flashSuccess('Updates Store Settings');
				//$this->_setShopSettings(true);
			} else {
				$this->FormData->flashError('Failed to update store settings');
			}
		} else {
			$shopSettings = $this->ShopSetting->find('all');
			foreach ($shopSettings as $shopSetting) {
				$shopSetting = $shopSetting['ShopSetting'];
				$this->request->data['ShopSetting'][$shopSetting['name']] = $shopSetting;
			}
		}
	}
}