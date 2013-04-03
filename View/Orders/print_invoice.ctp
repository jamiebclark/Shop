<?php
echo $this->Html->tag('table', null, array(
	'width' => '100%'
));

echo "<tr><td>&nbsp;</td>\n";
echo $this->Html->tag('td', null, array(
	'width' => 640
));

echo $this->Layout->topMenu(array(
	array('Back to Order', array('action' => 'view', $order['Order']['id'])),
	array('Go to tacklehunger.org', 'http://tacklehunger.org'),
));

echo $this->Html->tag('h1', null, array('class' => 'topTitle'));
echo $this->Html->link($this->Html->image(
		'logos/sboc/logo.gif',
		array(
			'height' => 80
		)
	),
	'/', 
	array('escape' => false)
);
echo 'Order #' . $order['Order']['id'];
echo "</h1/>\n";

echo "<table><tr><td width=50%>\n";
echo $this->element('orders/shipping_status');
echo "</td><td width=50%>\n";
echo $this->element('orders/payment_status');
echo "</td></tr>\n</table>\n";

echo $this->element('orders/cart', array(
	'links' => false,
	'form' => false,
	'images' => false,
));
echo "</td>";
echo "<td>&nbsp;</td></tr>\n";
echo "</table>";