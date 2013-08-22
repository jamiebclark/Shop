<?php 
echo $this->Form->create(null, array('class' => 'form-horizontal'));
echo $this->ShopSetting->inputs(array(
	'COMPANY_NAME',
	'COMPANY_ADDRESS',
	'COMPANY_EMAIL' => array(
		'type' => 'email',
		'helpInline' => 'The contact email given to customers viewing the page',
	),
	'COMPANY_ADMIN_EMAILS' => array(
		'helpInline' => 'A comma-separated list of emails to receive store admin emails'
	),
), array(
	'legend' => 'Basic Company Info',
	'note' => 'PayPal stuff',
));

echo $this->ShopSetting->inputs(array(
	'COMPANY_EMAIL_USER' => array('type' => 'email'),
	'COMPANY_EMAIL_PASSWORD' => array(
		'type' => 'password',
	),
	'COMPANY_EMAIL_HOST' => array(
		'default' => 'ssl://smtp.gmail.com',
	),
	'COMPANY_EMAIL_TRANSPORT' => array(
		'default' => 'Smtp',
	),
	'COMPANY_EMAIL_PORT' => array(
		'type' => 'number',
		'default' => 465,
	),
), array(
	'legend' => 'Email Info',
	'note' => 'The email login info used for communicating with users',
));

echo $this->ShopSetting->inputs(array(
	'PAYPAL_USER_NAME' => array(
		'type' => 'email',
		'helpInline' => 'The email address you set up with PayPal',
	),
	'PAYPAL_RETURN_URL' => array(
		'type' => 'url',
		'helpInline' => 'Where the user should be taken after they complete their order',
	),
	'PAYPAL_CANCEL_URL' => array(
		'type' => 'url',
		'helpInline' => 'Where the user should be taken if they cancel their order',
	),
), array('legend' => 'PayPal Info', 'note' => 'Information linking to your PayPal account'));

echo $this->ShopSetting->inputs(array(
	'EMAIL_BACKGROUND_COLOR',
	'EMAIL_HEADING_STYLE',
), array('legend' => 'Email Style'));

echo $this->ShopSetting->input('SHOP_VARS_LOADED', array('value' => 1, 'type' => 'hidden'));
echo $this->FormLayout->end('Update');