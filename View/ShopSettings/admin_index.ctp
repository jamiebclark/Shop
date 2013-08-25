<?php echo $this->Form->create(null, array('class' => 'form-horizontal')); ?>
<div class="row">
	<div class="span8"><?php
		echo $this->ShopSetting->inputs(array(
			'COMPANY_NAME' => array('requred' => true),
			'COMPANY_ADDRESS' => array('requred' => true),
			'COMPANY_EMAIL' => array(
				'type' => 'email',
				'helpInline' => 'The contact email given to customers viewing the page',
				'required' => true,
			),
			'COMPANY_ADMIN_EMAILS' => array(
				'helpInline' => 'A comma-separated list of emails to receive store admin emails',
				'type' => 'textarea',
				'rows' => 1,
				'data-max-height' => 160,
				'class' => 'input-block-level',
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

		$codeOptions = array('type' => 'textarea', 'class' => 'code input-block-level');
		echo $this->ShopSetting->inputs(array(
			'EMAIL_BACKGROUND_COLOR' => array('class' => 'input-small'),
			'EMAIL_STYLE' => $codeOptions + array(
				'helpInline' => 'Use the #email-header, #email-content, #email-body, and #email-footer ids for styling',
			),
			'EMAIL_HEADER_HTML' => $codeOptions,
			'EMAIL_FOOTER_HTML' => $codeOptions,
			'EMAIL_HEADER_NON_HTML' => $codeOptions,
			'EMAIL_FOOTER_NON_HTML' => $codeOptions,
			
		), array('legend' => 'Email Style'));
		echo $this->ShopSetting->input('SHOP_VARS_LOADED', array('value' => 1, 'type' => 'hidden'));
	?></div>
	<div class="span4">
		<div class="scrollfix">
			<?php echo $this->FormLayout->submitPrimary('Update'); ?>
		</div>
	</div>
</div>
<?php echo $this->FormLayout->end('Update'); ?>
<script type="text/javascript">
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
</script>