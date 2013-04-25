function shopInit() {
	$(':input[type=checkbox][name*="[canceled]"]').each(function() {
		var $checkbox = $(this),
			$form = $checkbox.closest('form'),
			className = 'canceled';
		if (!$checkbox.data('canceled-init')) {
			function toggle() {
				if ($checkbox.is(':checked')) {
					$form.addClass(className);
				} else {
					$form.removeClass(className);
				}
			}
			$checkbox.change(function() { toggle();});
			toggle();
			$checkbox.data('canceled-init', true);
		}
	});
}

$(document).ready(function() {
	shopInit();
});