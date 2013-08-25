<?php
if (defined('EMAIL_STYLE')) {
	echo $this->Html->tag('style', EMAIL_STYLE, array('type' => 'text/css'));
}
?>
<table width=700 border=0 cellspacing=0 cellpadding=0 id="email-body">
	<tr>
		<td id="email-header">
			<?php echo $this->DisplayText->text(EMAIL_HEADER_HTML); ?>
		</td>
	</tr>
	<tr>
		<td id="email-content"><?php echo $this->fetch('content'); ?></td>
	</tr>
	<tr>
		<td id="email-footer">
		<?php echo $this->DisplayText->text(EMAIL_FOOTER_HTML); ?>
		<?php if (!empty($order)): ?>
			<div class="disclaimer">
			You are being sent this message regarding the status of an <?php echo $this->Html->link(
				'order you placed online',
				$this->Order->publicUrl($order['Order'])
			);?>. You have not been subscribed to any lists as a result of this.
			</div>
		<?php endif; ?>
		</td>
	</tr>
</table>