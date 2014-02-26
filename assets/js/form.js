// The application form JS

// Setup moment locale
moment.lang('id');

// Recheck plugin
var recheckActivated = false;
(function ($) {
	$.fn.getVal = function(fieldname) {
		var b;
		$(this.serializeArray()).each(function() {
			if (!b && this.name == fieldname)
				b = this.value;
		});
		
		return b;
	}
	$.fn.isPracticallyEmpty = function() {
		// v = $('#appform').getVal(this.attr('name'));
		if (this.val().match(/^[\s\-.]+$|^$/)) {
			return true;
		}
		else if (this.attr('type') == 'number' && parseInt(this.val()) <= 0) {
			return true;
		}
		else {
			return false;
		}
	}
	// Invoked from an input control
	$.fn.recheck = function(altFor) {
		// the corresponding label
		l = $('label[for=' + this.attr('id') + ']');
		s = this.parents('fieldset');
		n = $(".form-nav a[href='#" + s.attr('id') + "']");

		if (this.isPracticallyEmpty()) {
			// mark labels and nav
			l.addClass('recheck');
			n.addClass('recheck');

			this.addClass('invalid');
			if (this.css('border-width') == 0)
				this.parents('span').addClass('invalid');
		}
		this.change(function() {
			t = $(this);
			l = $('label[for=' + t.attr('id') + ']');
			s = t.parents('fieldset');
			n = $(".form-nav a[href='#" + s.attr('id') + "']");

			if (!t.isPracticallyEmpty()) {
				t.removeClass('invalid');
				if (t.css('border-width') == 0)
					t.parents('span').removeClass('invalid');
				l.removeClass('recheck');

				if ($('.invalid', s).length == 0) {
					n.removeClass('recheck');
				}
			}
			else {
				l.addClass('recheck');
				n.addClass('recheck');
				t.addClass('invalid');
				if (t.css('border-width') == 0)
					t.parents('span').addClass('invalid');
			}
		});

		return this;	
	}
	
	activateRecheck = function() {
		if (!recheckActivated) {
			// The required_fields global variable is fed by PHP
			$(required_fields).each(function() {
				id = '#' + this;
				$(id).recheck();
			});

			$('[data-continent] select').recheck();

			// Program check
			afs = $('#program_afs');
			yes = $('#program_yes');
			$('#program_afs, #program_yes').each(function() {
				if (!afs.prop('checked') && !yes.prop('checked')) {
					afs.parents('tr').children('th.label').addClass('recheck');
					$(".form-nav a[href='#program']").addClass('recheck');
				}
			}).change(function() {
				if (!afs.prop('checked') && !yes.prop('checked')) {
					$(this).parents('tr').children('th.label').addClass('recheck');
					$(".form-nav a[href='#program']").addClass('recheck');
				}
				else {
					afs.parents('tr').children('th.label').removeClass('recheck');
					$(".form-nav a[href='#program']").removeClass('recheck');
				}
			});

			// DOB check
			var dob_fields = $('.applicant-dob #date_of_birth-day-, .applicant-dob #date_of_birth-month-, .applicant-dob #date_of_birth-year-');
			dob_fields.check = function() {
				l = $('label[for=date_of_birth]');
				s = this.parents('fieldset');
				n = $(".form-nav a[href='#" + s.attr('id') + "']");

				// Remove recheck class
				l.removeClass('recheck');
				n.removeClass('recheck');

				// Check for empty date fields
				dob_fields.each(function() {
					var t = $(this);
					if (!t.val() || t.val() == '0') {
						t.addClass('invalid');
						l.addClass('recheck');
						n.addClass('recheck');
						console.log('invalid: ' + t.attr('id') + ' ' + t.val());
					}
					else {
						t.removeClass('invalid');
					}
				});

				// Check for dates outside DOB range
				var currentDOBValue = getDateValue('.applicant-dob #date_of_birth');
				var isValidDOB = (currentDOBValue >= dob_lower_limit) && (currentDOBValue <= dob_upper_limit);
				$('#invalid-dob-alert').hide();
				if (!isValidDOB) {
					dob_fields.addClass('invalid');
					l.addClass('recheck');
					n.addClass('recheck');
					$('#invalid-dob-alert').show();
				}
			}
			dob_fields.check();
			dob_fields.change(function() {
				dob_fields.check();
			});
		}

		recheckActivated = true;
	}
})(jQuery);

$(function(){
	$('span.phone-number input, span.number input')
		.focus(function(){$(this.parentNode).addClass('focus')})
		.blur(function(){$(this.parentNode).removeClass('focus')});

	function switchToTab(activeTab, direct) {
		if ($('#lastpane').val() == activeTab)
			return;

		if (!activeTab)
			activeTab = '#pribadi';

		if ($(activeTab).hasClass('pane')) {
			$('fieldset.pane').hide();
			
			$(".form-nav li a.active").each(function() {
				t = $(this);
				t.removeClass('active');
				$(t.attr('href')).removeClass('active').hide().trigger('deactivate');
			})

			$(".form-nav li a[href='" + activeTab + "']").addClass("active"); //Add "active" class to selected tab
	
			$("#lastpane").val(activeTab);

			$(activeTab).trigger('activate');
			
			if (direct) {
				// Don't fade in
				$(window).scrollTop($('.page-header').offset().top);
				$(activeTab).addClass('active').show();
			}
			else {
				// Fade in
				$(activeTab).addClass('active').fadeIn('medium');
			}
		}
	}

	// History handling
	if (history.pushState) {
		window.onpopstate = function(e) {
			e.preventDefault();
			if (e.state)
				switchToTab(e.state);
			else if (window.location.hash)
				switchToTab(window.location.hash, true);
		}
	}

	// Handle current window.location.hash
	if (last_pane) {
		last_pane = '#' + last_pane;
		switchToTab(last_pane, true);
		window.location.replace(last_pane);
	}
	else if (window.location.hash) {
		switchToTab(window.location.hash, true);
	}
	else {
		switchToTab('#pribadi', true);
		window.location.replace('#pribadi');
	}
	$(window).load(function() {
		$(window).scrollTop(0);
	});

	// Click Events
	$(".form-nav li a").click(function(e) {
		e.preventDefault();

		activeTab = $(this).attr("href"); //Find the href attribute value to identify the active tab + content
		
		switchToTab(activeTab);
		if (history.pushState)
			history.pushState(activeTab, $(this).text(), activeTab);

		this.blur();
	});

	// Pagination
	function getNextTab() {
		return $(".form-nav a.active").parent().closest('li').next().children().first().attr('href');
	}
	function getPrevTab() {
		return	$(".form-nav a.active").parent().closest('li').prev().children().first().attr('href') ? 
				$(".form-nav a.active").parent().closest('li').prev().children().first().attr('href') :
				$(".form-nav a.active").parent().siblings().last().children().first().attr('href');
	}
	$("a[href='#_next']").click(function(e) {
		e.preventDefault();
		$(window).scrollTop(0);
		switchToTab(getNextTab());
		this.blur();
	})
	$("a[href='#_prev']").click(function(e) {
		e.preventDefault();
		$(window).scrollTop(0);
		switchToTab(getPrevTab());
		this.blur();
	})

	toggleFinalizeButton = function(e) {
		if ($('#finalize').is(':checked')) {
			activateRecheck();
			if ($('.form-nav li a.recheck').length) {
				// Invalid elements still exist
				$('.recheck', '#finalisasi').show();
				$('.finalize-checkbox').hide();
				e.preventDefault();
				$('#finalize').prop('checked', false);
			}
			else {
				$('.recheck', '#finalisasi').hide();
				$('.finalize-checkbox').show();
				$('#finalize-button:parent').fadeIn('fast').focus();
			}
		}
		else
			$('#finalize-button:parent').hide();
	}
	$('#finalize').change(toggleFinalizeButton);

	$('fieldset#finalisasi')
		.on('activate', function() {
			if (!$('.form-nav li a.recheck').length) {
				$('.recheck', '#finalisasi').hide();
				$('.finalize-checkbox').show();
				$('#finalize-button:parent').fadeIn('fast').focus();
			}
			toggleFinalizeButton();
		})
		.on('deactivate', function() {
			$('p.save button').css('visibility', 'visible');
			$('.form-page-nav.below').show();
			$('#finalize').prop('checked', false);
			toggleFinalizeButton();
		});

	if (incomplete) {
		activateRecheck();
	}
	
	// Family
	$.fn.replaceKey = function(rand) {
		this.attr('name', this.attr('name').replace('[#]', '[' + rand + ']'));
	}
	fac = function() {
		$('td.sibling-name input').each(function() {
			t = $(this);
			if (!t.parent().parent().hasClass('prototype')) {
				if (t.val())
					t.parent().parent().removeClass('engineered').addClass('notempty');
				else
					t.parent().parent().addClass('engineered').removeClass('notempty');
			}
		})

		v = parseInt($(this).val());
		o = $('.siblings-table tbody tr').length - 1;
		if (v > o) {
			d = v - o - 1;
			for (i=0; i<d; i++) {
				cl = $('.prototype').clone().removeClass('prototype');
				rand = Math.ceil(Math.random() * 1000).toString();
				$('input, select', cl).each(function() { $(this).replaceKey(rand); } );
				$('.siblings-table tbody').append(cl);
			}
		}
		if (v <= o) {
			d = o - v + 1;
			for (i=0; i<d; i++) {
				$('tr.engineered').first().detach();
			}
		}
	}
	$('#number_of_children_in_family').click(fac);
	$('#number_of_children_in_family').change(fac);
	$('#number_of_children_in_family').keyup(fac);

	// YES filter: acceleration class cannot choose YES
	previously_selected_yes = $('#program_yes').prop('checked');
	toggleYES = function(toggle) {
		if (toggle) {
			previously_selected_yes = $('#program_yes').prop('checked');
			$('#program_yes').prop('checked', false)
			$('#program_yes').prop('disabled', true);
			$('.program-name .yes').addClass('disabled');
			$('.program-age-limit .yes').addClass('recheck');
		}
		else {
			if (previously_selected_yes)
				$('#program_yes').prop('checked', true);
			else
				$('#program_yes').prop('checked', false);
			$('#program_yes').prop('disabled', false);
			$('.program-name .yes').removeClass('disabled');
			$('.program-age-limit .yes').removeClass('recheck');
		}
	}
	checkAcc = function() {
		toggleYES($('#in_acceleration_class').is(':checked'));
	}
	checkAcc();
	$('#in_acceleration_class').change(checkAcc);

	getDateValue = function(selector_base) {
		var year = parseInt($(selector_base + '-year-').val());
		var month = parseInt($(selector_base + '-month-').val()) - 1;
		var day = parseInt($(selector_base + '-day-').val());
		return new Date(year, month, day, 12, 0, 0, 0);
	}
	checkYESDOB = function() {
		var currentDOBValue = getDateValue('.applicant-dob #date_of_birth');
		toggleYES((currentDOBValue < dob_lower_limit_yes) || (currentDOBValue > dob_upper_limit));
	}
	checkYESDOB();
	$('.applicant-dob #date_of_birth-day-, .applicant-dob #date_of_birth-month-, .applicant-dob #date_of_birth-year-').change(checkYESDOB);

	$('input[type=file]').change(function() { $(this).parents('form').submit() });
	
	// Country Preference Ordering
	$.fn.reserveCountry = function()  {
		this.each(function() {
			t = $(this);
			v = t.val();
			p = t.data('prev-value');
			t.data('prev-value', v);
			siblings = $('select', t.parents('li').siblings());
			// enable all options
			siblings.each(function() {
				$('option[value=' + p + ']', this).removeAttr('disabled');
			});

			// disable options with the same value as this one, except if it's empty
			if (v) siblings.each(function() {
				$('option[value=' + v + ']', this).attr('disabled', 'disabled');
			});
		});
		
		return this;
	};
	$('[data-continent] select')
		.reserveCountry()
		.change(function() {
			$(this).reserveCountry();
		});

	$('*[data-toggle]').each(function() {
		t = $(this);
		trigger = t.attr('data-toggle');

		if ($('#' + trigger).prop('checked'))
			t.show();
		else
			t.hide();

		$('#' + trigger).change(function() {
			if ($(this).prop('checked'))
				$('*[data-toggle=' + this.id + ']').fadeIn();
			else
				$('*[data-toggle=' + this.id + ']').hide();
		});
	});
	
	// Form tools (Simpan Sementara button) - keep afloat
	$(window).scroll(function(e) {
		el = $('.form-tools-container');
		t = $('.form-tools').offset().top;
		y = $(this).scrollTop();
		
		if (y >= t) {
			el.addClass('fixed');
		} else {
			// console.log('no more');
			el.removeClass('fixed');
		}
	})
	
	// Highlight the row containing the currently-focused form control
	$('#appform input, #appform select, #appform textarea').focus(function() {
		$(this).parents('tr').first().addClass('selected');
	});
	$('#appform input, #appform select, #appform textarea').blur(function() {
		$(this).parents('tr').first().removeClass('selected');
	});
	
	// AFS Program force checkbox
	$('#program_afs').prop('checked', true).change(function() {
		$(this).prop('checked', true);
	});

	// Typeahead for school names
	typeaheadSchool = function(query, process) {
		var schs = [query];
		$('datalist[data-for=high_school_name] option[value*="' + query + '"]').each(function() {
			schs.push($(this).attr('value'));
		})

		return schs;
	}
	$('#high_school_name')
		.attr('autocomplete', 'off')
		.typeahead({
			source: typeaheadSchool,
			items: 20
		});

	// callback is in function(err, data) format.
	handleAjaxSave = function(callback) {
		var form = $('#application-form');
		var postdata = form.serialize();

		var endpoint = ajax_save_endpoint;

		$.post(endpoint, postdata, null, 'json')
			.done(function(data, textStatus, jqXHR) {
				if (textStatus != 'parsererror') {
					// Request seems to have gone fine
					if (data.status == 'success') {
						// Saving succeeded
						callback(null, data, jqXHR);
					}
					else {
						// Saving did not succeed
						callback('saving_error', data, jqXHR);
					}
				}
				else {
					callback('parser_error', data, jqXHR);
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				callback(errorThrown, jqXHR);
			});
	};

	var lastSavedAt = moment();
	var isSaving;
	var saveAgain;

	$('#save-status').text('Terakhir disimpan pada pukul ' + lastSavedAt.format('HH.mm'));

	ajaxSave = function(fallback) {
		if (isSaving) {
			// Another save is in progress, abort this one.
			saveAgain = true;
		}
		else {
			isSaving = true;
			saveAgain = false;

			$('#save-status').text('Menyimpan isi formulir...');
			$('#save-status').addClass('loading').removeClass('text-success').removeClass('text-error');
			handleAjaxSave(function(err, data) {
				// console.log([err, data]);
				if (err) {
					if (fallback) {
						fallback();
						$('#save-status').removeClass('loading');
						isSaving = false;
					}
					else {
						$('#save-status').removeClass('loading');
						$('#save-status').text('Penyimpanan gagal.');
						$('#save-status').addClass('text-error');
						isSaving = false;

						if (saveAgain) ajaxSave();
					}
				}
				else {
					$('#save-status').removeClass('loading');
					lastSavedAt = moment(data.timestamp);
					$('#save-status').text('Terakhir disimpan pada pukul ' + lastSavedAt.format('HH.mm'));
					$('#save-status').addClass('text-success');
					isSaving = false;

					if (saveAgain) ajaxSave();
				}
			});
		}
	};
	ajaxSaveWithFallback = ajaxSave.bind(undefined, function() {
		$('#application-form').submit();
	});

	if (autoSave) {
		$('#application-form input, #application-form textarea, #application-form select').change(function() {
			ajaxSave();
		});
		$('#save-button')
			.click(function(e) {
				e.preventDefault();
				ajaxSaveWithFallback();
				this.blur();
			})
			.tooltip({placement: 'bottom'});
	}
	
});