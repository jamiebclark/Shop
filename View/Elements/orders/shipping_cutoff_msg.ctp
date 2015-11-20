<?php
if (defined('STORE_SHIPPING_CUTOFF')): 
	$todayStamp = time(); //strtotime('-3 days');
	$cutoffStamp = strtotime(STORE_SHIPPING_CUTOFF);
	$superBowlStamp = strtotime(SOUPER_BOWL_SUNDAY);

	$today = date('Y-m-d', $todayStamp);
	$superBowlDaysAway = floor(($superBowlStamp - $todayStamp) / DAY);
	$cutoffDaysAway = floor(($cutoffStamp - $todayStamp) / DAY);
	$cutoffToday = $cutoffDaysAway == 0;

	$msg = '';
	$title = 'Shipping Deadline: ';
	$class = 'info';

	if ($cutoffDaysAway < 0) {
		$title .= 'PASSED';
		$class = 'danger';
		if ($superBowlDaysAway > 0) {
			$msg .= 'With the Big Game so close (Less than '. $superBowlDaysAway . ' days away!)';
		} else {
			$msg .= 'Since the Big Game is today';
		}
		$msg .= ', we cannot ensure that items ordered will be delivered in time for your group\'s collection.';
	} else if ($cutoffDaysAway <= 7) {
		$class = 'warning';
		if ($cutoffToday) {
			$title .= 'TODAY';
		} else {
			$title .= date('F j', $cutoffStamp) . ' - Less than ' . $cutoffDaysAway . ' day';
			if ($cutoffDaysAway != 1) {
				$title .= 's';
			}
		}
		$msg  = 'In order to have your order arrive in time for the Big Game (<em>'.date('F j, Y',$superBowlStamp).'</em>), ';
		$msg .= 'please be sure to place your orders <b>NO LATER</b> than ';
		$msg .= '<strong>'.date('g:iA T', $cutoffStamp).'</strong> ' . ($cutoffToday ? 'today' : date('F j, Y', $cutoffStamp)).'!';
	}
	?>
	<?php if (!empty($msg)): ?>
		<div class="alert alert-<?php echo $class;?>">
			<h5><?php echo $title; ?></h5>
			<?php echo $msg; ?>
		</div>
	<?php endif ?>
<?php endif ?>
