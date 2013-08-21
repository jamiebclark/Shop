<?php
App::uses('ShopAppModel', 'Shop.Model');
class ShopSetting extends ShopAppModel {
	var $name = 'ShopSetting';
	var $primaryKey = 'name';
	var $displayField = 'value';
}