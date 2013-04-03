<?php
echo $this->Html->tag('h1', 'Invoice #' . $invoice['Invoice']['id']);
echo $this->element('invoices/info');
?>