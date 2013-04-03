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
echo $this->element('products/admin_heading');
echo $this->Layout->defaultHeader();$class = 'numeric centered';
$subClass = $class . ' sub';

foreach ($totals['year'] as $year => $yearTotals) {	$this->Table->reset();		foreach ($yearTotals as $productId => $yearTotal) {
		$url = array('action' => 'view', $products[$productId]['id']);		$this->Table->cell(			$this->Html->link(				$products[$productId]['title'],				$url,				array('class' => 'title ' . $products[$productId]['active'] ? 'active' : 'inactive')			),			'Product'		);		$this->Table->cell(number_format($yearTotal),			'Total', null, null, array('class' => $class . ' total')		);				for ($i = 1; $i <= 12; $i++) {			$m = $i + $monthShift;			if ($m > 12) {				$m -= 12;			} else if ($m < 1) {				$m += 12;			}			$monthTotal = isset($totals['month'][$year][$m][$productId]) ? $totals['month'][$year][$m][$productId] : 0;			$this->Table->cell(				number_format($monthTotal),				date('M.', strtotime("$year-$m-01")),				null,				null,				array('class' => $class . (empty($monthTotal) ? ' empty' : ''))			);		}				$this->Table->rowEnd();
		
		if (!empty($totalsOptions['year'][$year][$productId])) {
			foreach ($totalsOptions['year'][$year][$productId] as $productOptionId => $optionYearTotal) {
				$this->Table->cell($this->Html->link($productOptions[$productOptionId], $url), 
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
					if (isset($totalsOptions['month'][$year][$m][$productId][$productOptionId])) {
						$monthTotal =  $totalsOptions['month'][$year][$m][$productId][$productOptionId];
					}
					$this->Table->cell(number_format($monthTotal),
						null,null,null,array('class' => $subClass . (empty($monthTotal) ? ' empty' : ''))
					);
				}
				$this->Table->rowEnd();
			}
		}	}	echo $this->Html->tag('h2', $year);	echo $this->Table->table(array('class' => 'product-totals'));}