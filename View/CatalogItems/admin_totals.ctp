<style type="text/css">
.product-totals td {
	border-top: 1px solid #CCC;
}
.product-totals td.sub {
	border-top: 1px dotted #EEE;
}
td.total {
	font-weight: bold;
}
td.sub {
	background-color: #F2FDFF !important;
	color: #999;
}
td.sub.title {
	text-align: right;
}
td.sub.title a {
	font-weight: normal;
	text-decoration: underline;
}
</style>
<?php
echo $this->Layout->defaultHeader();

$class = 'numeric centered';
$subClass = $class . ' sub';

foreach ($totals['year'] as $year => $yearTotals) {
	foreach ($yearTotals as $catalogItemId => $yearTotal) {
		$url = array('action' => 'view', $catalogItems[$catalogItemId]['id']);
		$this->Table->cell(
		if (
			!empty($totalsOptions['year'][$year][$catalogItemId]) && 
			count($totalsOptions['year'][$year][$catalogItemId]) > 1
		) {
			foreach ($totalsOptions['year'][$year][$catalogItemId] as $productId => $optionYearTotal) {
				$this->Table->cell($this->Html->link($products[$catalogItemId][$productId], $url), 
					null, null, null, array('class' => 'sub title')
				);
				$this->Table->cell(number_format($optionYearTotal),
					null, null, null, array('class' => $subClass . ' total')
				);
				for ($i = 1; $i <= 12; $i++) {
					$m = $i + $monthShift;
					if ($m > 12) {
						$m -= 12;
					} else if ($m < 1) {
						$m += 12;
					}
					$monthTotal = 0;
					if (isset($totalsOptions['month'][$year][$m][$catalogItemId][$productId])) {
						$monthTotal =  $totalsOptions['month'][$year][$m][$catalogItemId][$productId];
					}
					$this->Table->cell(number_format($monthTotal),array(
						'class' => $subClass . (empty($monthTotal) ? ' empty' : '')
					));
				}
				$this->Table->rowEnd();
			}
		}