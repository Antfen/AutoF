jQuery(document).ready(function($) {
	$(".quantity input[type='number']").each(function(){

		$(this).asSpinner({
			namespace: 'theme-spinner',
			mousewheel: true,
			looping: false,
			min: $(this).attr("min"),
            max: $(this).attr("max"),
            step: $(this).attr("step")
		});
	});
});
