$(window).load(function () {
	var rx = parseInt($('#pic').attr('data-original-width'));
	var ry = parseInt($('#pic').attr('data-original-height'));

	w = $('#pic').innerWidth();
	h = $('#pic').innerHeight();
	ratio = w / h;
	ideal = 0.75;
	padding = 10;
	var x1 = padding;
	var y1 = padding;
	if (ratio > ideal) {
		half = w / 2;
		ph = h - (2 * padding);
		nw = Math.round(ph * ideal);
		x1 = Math.round(half - (nw / 2));
		x2 = Math.round(half + (nw / 2));
		y1 = padding;
		y2 = ph + padding;
	}
	else {
		half = h / 2;
		pw = w - (2 * padding);
		nh = Math.round(pw / ideal);
		y1 = Math.round(half - (nh / 2));
		y2 = Math.round(half + (nh / 2));
		x1 = padding;
		x2 = pw + padding;
	}

	x1 = Math.round(x1);
	x2 = Math.round(x2);
	y1 = Math.round(y1);
	y2 = Math.round(y2);
	
	console.log([w, h, x1, x2, y1, y2]);

	$('#pic').imgAreaSelect({
		x1: x1,
		y1: y1,
		x2: x2,
		y2: y2,
		aspectRatio: '3:4',
		handles: true,
		onSelectEnd: function (img, selection) {
			$('#x').val(rx * selection.x1);
			$('#y').val(ry * selection.y1);
			$('#width').val(rx * selection.width);
			$('#height').val(ry * selection.height);
		}
	});

	$('#x').val(Math.round(rx * x1));
	$('#y').val(Math.round(ry * y1));
	$('#width').val(Math.round(rx * (x2 - x1)));
	$('#height').val(Math.round(ry * (y2 - y1)));
});
$(function() {
	var ph = $('.page-header');
	$(window).scrollTop(ph.offset().top + ph.outerHeight());
})