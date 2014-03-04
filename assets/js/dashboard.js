$(function() {
	$('table.counts td span, .nav-tabs li[data-toggle=tooltip]').tooltip({
		placement: 'bottom'
	});
	$('#searchPane:not(.active)').hide();
	$('#searchLink').click(function(e) {
		e.preventDefault();
		$(this).parent().toggleClass('active');
		$('#searchPane').toggle();
		this.blur();
	});

	$('[data-stage]').css('cursor', 'pointer').click(function() {
		window.location.href = '/chapter/applicants?stage=' + this.getAttribute('data-stage');
	})
})