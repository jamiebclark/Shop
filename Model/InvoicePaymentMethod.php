<?php
class InvoicePaymentMethod extends ShopAppModel {
	var $name = 'InvoicePaymentMethod';
	var $actsAs = array('Shop.SelectList' => array('blank'));
}