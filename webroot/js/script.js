$.fn.sameBilling = function() {
	var $target = $('.billingInfo');
	if ($(this).is(':checked')) {
		$target.slideUp();
	} else {
		$target.slideDown();
	}
	return $(this);
};

$(document).ready(function() {
	$('input[name*=same_billing]').sameBilling().click(function() { $(this).sameBilling();});
	$('input[name*="[quantity]"]').click(function() {
		$(this).select();
	}).keyup(function() {
		var $val = $(this).val();
		$(this).val($val.replace(/[^\d]+/, ""));
	});
});