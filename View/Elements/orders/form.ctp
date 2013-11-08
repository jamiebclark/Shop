<?php
echo $this->Form->create('Order');
echo $this->Form->hidden('id');
$span = 6;

echo $this->Layout->defaultHeader($order['Order']['id']);
?>

	<?php	
	echo $this->Form->input('canceled', array(
		'label' => 'Canceled', 
		'helpBlock' => 'Order has been canceled'
	));
	?>
<div class="row">
	<div class="span5">
		<h3>Shipping</h3>
		<?php 
			echo $this->element('orders/input_shipping'); 
		?>
	</div>
	<div class="span7">
		<h3>Customer Info</h3><?php
		echo $this->FormLayout->addressInput(compact('span') + array(
			'placeholder' => true,
			'before' => $this->FormLayout->inputRow(array('first_name', 'last_name'), compact('span')),
			'after' => $this->FormLayout->inputRow(array('email', 'phone'), compact('span') + array('placeholder' => true)),
		));
		?>
	</div>
</div>

<div class="row">
	<div class="span5">
		<h3>Payment</h3>
		<?php echo $this->element('orders/input_payment', array('amt' => false));?>
	</div>
	<div class="span7">
		<h3>Billing</h3>
		<?php 
		echo $this->Layout->toggle(null, $this->FormLayout->addressInput(compact('span') + array(
			'model' => 'Invoice',
			'placeholder' => true,
			'before' => $this->FormLayout->inputRow(array(
				'Invoice.first_name', 
				'Invoice.last_name'
			), compact('span'))
		)), 'Billing address is same as Shipping', array(
			'name' => 'same_billing',
		));
		?>
	</div>
</div>

<h2>Cart</h2>
<?php
echo $this->Form->inputs(array(
	'fieldset' => false,
	'auto_shipping' => array(
		'type' => 'checkbox',
		'label' => 'Let the system calculate what shipping charges should be'
	),
	'auto_price' => array(
		'type' => 'checkbox',
		'label' => 'Let the system calculate product prices',
	)
));
$this->Table->reset();
$inputOptions = array('label' => false, 'class' => 'input-small');
$cashOptions = $inputOptions + array('prepend' => '$', 'step' => 'any');

foreach($this->request->data['OrderProduct'] as $k => $orderProduct) {
	$prefix = "OrderProduct.$k.";
	echo $this->Form->input($prefix . 'id');
	
	$this->Table->cells(array(
		array(
			$this->Form->input($prefix . 'product_id', array('class' => null) + $inputOptions),
			'Product'
		), array(
			$this->Form->input($prefix . 'price', $cashOptions), 
			'Price per Item', 
			array('class' => 'number')
		), array(
			$this->Form->input($prefix . 'shipping', $cashOptions),
			'Shipping', 
			array('class' => 'number')
		), array(
			$this->Form->input($prefix . 'quantity', $inputOptions),
			'Quantity', 
			array('class' => 'number')
		),
	), true);
}
echo $this->Table->output();
?>

<h2>Handling</h2>
<?php
echo $this->Form->input('auto_handling', array(
	'type' => 'checkbox',
	'label' => 'Let the system calculate what handling charges should be'
));
echo $this->Table->reset();
$total = 1;
if (!empty($this->request->data['OrdersHandlingMethod'])) {
	$total = count($this->request->data['OrdersHandlingMethod']);
}
for ($k = 0; $k <= $total; $k++) {
	$prefix = 'OrdersHandlingMethod.' . $k . '.';
	
	if (!empty($this->request->data['OrdersHandlingMethod'][$k])) {
		$total = ($this->request->data['Order']['sub_total'] + $this->request->data['Order']['shipping']);
		$total *= $this->request->data['OrdersHandlingMethod'][$k]['pct'];
		$total += $this->request->data['OrdersHandlingMethod'][$k]['amt'];
	} else {
		$total = 0;
	}
	echo $this->Form->hidden($prefix . 'id');
	echo $this->Form->hidden($prefix . 'handling_method_id');
	$this->Table->cells(array(
		array(
			$this->Form->input(
				$prefix . 'title', 
				array('prepend' => false, 'class' => null) + $inputOptions
			), 'Title'
		), array(
			$this->Form->input($prefix . 'amt', $cashOptions), 
			'Amount'
		), array(
			$this->Form->input($prefix . 'pct', $inputOptions + array('append' => '%')), 
			'Percent'
		), array(
			$this->DisplayText->cash($total), 'Charge',
			array('class' => 'price')
		),
	), true);
}
echo $this->Table->output(array('class' => 'handling-methods'));

echo $this->Form->submit('Update', array('class' => 'btn btn-primary')); 
echo $this->Form->end();
$this->Asset->blockStart();
?>
(function($) {
	$.fn.toggleActive = function(find) {
		return this.each(function() {
			var $toggle = $(this),
				$find = $(find);
			function toggleClick() {
				$find.each(function() {
					if ($toggle.is(':checked')) {
						$(this).data('old-readonly', $(this).prop('readonly'));
						$(this).prop('readonly', true);
					} else {
						$(this).prop('readonly', false);
					}
				});
			}
			
			$toggle.click(function(e) {
				toggleClick();
			});
			toggleClick();
			return $(this);
		});
	};
})(jQuery);
$(document).ready(function() {
	$('input[name*=auto_shipping]').toggleActive('input[name*="[shipping]"]');
	$('input[name*=auto_price]').toggleActive('input[name*="[price]"]');
	$('input[name*=auto_handling]').toggleActive('.handling-methods :input');
});
<?php
$this->Asset->blockEnd();