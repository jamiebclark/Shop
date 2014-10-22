<?php echo $this->Form->create(null, array('class' => 'form-horizontal')); ?>
<div class="row">
	<div class="col-sm-8"><?php
		echo $this->ShopSetting->inputs(array(
			'COMPANY_NAME' => array('requred' => true),
			'COMPANY_ADDRESS' => array('requred' => true),
			'COMPANY_EMAIL' => array(
				'type' => 'email',
				'after' => '<span class="help-block">The contact email given to customers viewing the page</span>',
				'required' => true,
			),
			'COMPANY_ADMIN_EMAILS' => array(
				'after' => '<span class="help-block">A comma-separated list of emails to receive store admin emails</span>',
				'type' => 'textarea',
				'rows' => 1,
				'data-max-height' => 160,
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
				'required' => true,
				'after' => '<span class="help-block">The email address you set up with PayPal</span>',
			),
			'PAYPAL_RETURN_URL' => array(
				'type' => 'url',
				'after' => '<span class="help-block">Where the user should be taken after they complete their order</span>',
			),
			'PAYPAL_CANCEL_URL' => array(
				'type' => 'url',
				'after' => '<span class="help-block">Where the user should be taken if they cancel their order</span>',
			),
		), array('legend' => 'PayPal Info', 'note' => 'Information linking to your PayPal account'));

		$codeOptions = array('type' => 'textarea', 'class' => 'code form-control');
		echo $this->ShopSetting->inputs(array(
			'EMAIL_BACKGROUND_COLOR',
			'EMAIL_STYLE' => $codeOptions + array(
				'after' => '<span class="help-block">Use the #email-header, #email-content, #email-body, and #email-footer ids for styling</span>',
			),
			'EMAIL_HEADER_HTML' => $codeOptions,
			'EMAIL_FOOTER_HTML' => $codeOptions,
			'EMAIL_HEADER_NON_HTML' => $codeOptions,
			'EMAIL_FOOTER_NON_HTML' => $codeOptions,
			
		), array('legend' => 'Email Style'));
		echo $this->ShopSetting->input('SHOP_VARS_LOADED', array('value' => 1, 'type' => 'hidden'));
	?></div>
	<div class="col-sm-4">
		<div class="scrollfix">
			<?php echo $this->FormLayout->submitPrimary('Update'); ?>
		</div>
	</div>
</div>
<?php echo $this->FormLayout->end('Update'); 

$this->Asset->blockStart(); ?>
$(document).ready(function() {
	var textareaFocusHeight = '300px';
	$('textarea').each(function() {
		var oHeight = $(this).height();
		$(this)
			.focus(function() {
				var height = textareaFocusHeight;
				if ($(this).data('max-height')) {
					height = $(this).data('max-height');
				}
				$(this).animate({'height' : height});
			})
			.blur(function() {
				$(this).animate({'height' : oHeight});
			});
	});
});
<?php $this->Asset->blockEnd(); ?>