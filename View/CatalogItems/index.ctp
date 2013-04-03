<?php
echo $this->element('products/crumbs');
//echo $this->Html->tag('h1', 'Online Store');
echo $this->Html->div('span-4');
echo $this->element('products/category_list');
echo "</div>\n";

echo $this->Html->div('span-12');
echo $this->element('products/category_path');
echo $this->element('products/list');
echo "</div>\n";

echo $this->Html->div('span-8 last');
echo $this->element('orders/shipping_cutoff_msg');
echo "</div>\n";