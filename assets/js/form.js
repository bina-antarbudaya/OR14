// The application form JS

// Setup moment locale
moment.lang('id');

// Recheck plugin

$(function(){

	/***** VARIABLES *****/
	var isPinging = false;
	var isSaving = false;
	var saveAgain = false;
	var formValidationIsActivated = false;
	var lastSavedAt = moment();
	var previously_selected_yes = $('#program_yes').prop('checked');

	var sessionLifetimeArray = sessionLifetime.split(' ');
	var sessionLifetimeDuration = moment.duration(parseInt(sessionLifetimeArray[0]), sessionLifetimeArray[1]);
	var sessionLifetimeMilliseconds = sessionLifetimeDuration.asMilliseconds();
	var sessionLifetimeTimeout;

	var dob_upper_limit = moment.utc(dob_upper_limit_string);
	var dob_lower_limit = moment.utc(dob_lower_limit_string);
	var dob_lower_limit_yes = moment.utc(dob_lower_limit_yes_string);

	/***** JQUERY PLUGIN DEFINITIONS *****/
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
	$.fn.recheck = function() {
		// the corresponding label
		var l = $('label[for=' + this.attr('id') + ']');
		var s = this.parents('fieldset');
		var n = $(".form-nav a[href='#" + s.attr('id') + "']");

		if (this.isPracticallyEmpty()) {
			// mark labels and nav
			l.addClass('recheck');
			n.addClass('recheck');

			this.addClass('invalid');
			if (this.css('border-width') == 0)
				this.parents('span').addClass('invalid');
		}

		this.change(function() {
			var t = $(this);
			var l = $('label[for=' + t.attr('id') + ']');
			var s = t.parents('fieldset');
			var n = $(".form-nav a[href='#" + s.attr('id') + "']");

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
	$.fn.replaceKey = function(rand) {
		this.attr('name', this.attr('name').replace('[#]', '[' + rand + ']'));
	}
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

	/***** FUNCTION DEFINITIONS *****/

	// Get the value of a date field (which consists of three selects)
	// @param selector_base Part of jQuery selector shared by the three selects (i.e. the ID)
	function getDateValue(selector_base) {
		var year = parseInt($(selector_base + ' .date-y').val());
		var month = parseInt($(selector_base + ' .date-m').val()) - 1;
		var day = parseInt($(selector_base + ' .date-d').val());
		return moment.utc([year, month, day]);
	}

	function validateDate(dateToValidate, lowerLimit, upperLimit) {
		var diffLower = dateToValidate.diff(lowerLimit, 'days');
		var diffUpper = dateToValidate.diff(upperLimit, 'days');
		var isValid = diffLower >= 0 && diffUpper <= 0;

		return isValid;
	}

	// Activate form validation
	function activateFormValidation() {
		if (!formValidationIsActivated) {
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
			var dob_fields = $('.applicant-dob select');
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
				var currentDOBValue = getDateValue('.applicant-dob');
				var isValidDOB = validateDate(currentDOBValue, dob_lower_limit, dob_upper_limit);
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

		formValidationIsActivated = true;
	}

	// Switch the currently displayed tab to another one
	// @param newActiveTab the jQuery selector to the new active tab
	function switchToTab(newActiveTab, direct) {
		if ($('#lastpane').val() == newActiveTab)
			return;

		if (!newActiveTab)
			newActiveTab = '#pribadi';

		if ($(newActiveTab).hasClass('pane')) {
			$('fieldset.pane').hide();
			
			$(".form-nav li a.active").each(function() {
				t = $(this);
				t.removeClass('active');
				$(t.attr('href')).removeClass('active').hide().trigger('deactivate');
			})

			$(".form-nav li a[href='" + newActiveTab + "']").addClass("active"); //Add "active" class to selected tab

			$("#lastpane").val(newActiveTab);

			$(newActiveTab).trigger('activate');
			
			if (direct) {
				// Don't fade in
				$(window).scrollTop($('.page-header').offset().top);
				$(newActiveTab).addClass('active').show();
			}
			else {
				// Fade in
				$(newActiveTab).addClass('active').fadeIn('medium');
			}
		}
	}

	// Return the ID of the next tab, based on the order in the form navigation menu
	function getNextTab() {
		return $(".form-nav a.active").parent().closest('li').next().children().first().attr('href');
	}

	// Return the ID of the previous tab, based on the order in the form navigation menu
	function getPrevTab() {
		return	$(".form-nav a.active").parent().closest('li').prev().children().first().attr('href') ? 
				$(".form-nav a.active").parent().closest('li').prev().children().first().attr('href') :
				$(".form-nav a.active").parent().siblings().last().children().first().attr('href');
	}

	// Toggle the appearance of the Finalize button
	// @param e Event
	function toggleFinalizeButton(e) {
		if ($('#finalize').is(':checked')) {
			activateFormValidation();
			if ($('.form-nav li a.recheck').length) {
				// Invalid elements still exist
				$('#finalisasi .recheck').show();
				$('.finalize-checkbox').hide();
				e.preventDefault();
				$('#finalize').prop('checked', false);
			}
			else {
				$('#finalisasi .recheck').hide();
				$('.finalize-checkbox').show();
				$('#finalize-button:parent').fadeIn('fast').focus();
			}
		}
		else
			$('#finalize-button:parent').hide();
	}

	// Set the save status text
	// @param statusText The status text to display
	// @param isLoading true to display loading indicator
	function setSaveStatus(statusText, isLoading) {
		var saveStatus = $('#save-status');
		if (statusText)
			saveStatus.text(statusText);
		if (isLoading)
			saveStatus.addClass('loading');
		else
			saveStatus.removeClass('loading');

		return saveStatus;
	}

	// Ping the server
	function ping() {
		if (!isPinging) {
			window.clearTimeout(sessionLifetimeTimeout);
			$.get(ajax_ping_endpoint, '', function() {
				isPinging = false;
				sessionLifetimeTimeout = window.setTimeout(handleAjaxSave, sessionLifetimeMilliseconds);
			});
		}
	}

	// Commit saving the form through AJAX
	// @param callback The callback to call on success/fail of the request, in function(err, data, jqXHR) format
	function commitAjaxSave(callback) {
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
					else if (data.status == 'error' && data.error == 'unauthorized') {
						// Unauthorized -- probably out of inactivity
						callback('unauthorized', data, jqXHR);
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
				if (errorThrown == 'Unauthorized') {
					callback('unauthorized');
				}
				else {
					callback(errorThrown, jqXHR);
				}
			});
	};

	// Handle the saving of the form when form field values are changed
	// @param fallback (optional) callback to call on failure
	function handleAjaxSave(fallback) {
		if (isSaving) {
			// Another save is in progress, abort this one.
			saveAgain = true;
		}
		else {
			isSaving = true;
			saveAgain = false;
			window.clearTimeout(sessionLifetimeTimeout);

			setSaveStatus('Menyimpan isi formulir...', true);
			commitAjaxSave(function(err, data) {
				// console.log([err, data]);
				if (err) {
					console.log([err, data]);
					if (fallback) {
						isSaving = false;
					}
					else if (err == 'unauthorized') {
						$('#application-form').submit();
					}
					else {
						setSaveStatus('Penyimpanan gagal.');
						isSaving = false;

						if (saveAgain)
							handleAjaxSave();
						else
							sessionLifetimeTimeout = window.setTimeout(handleAjaxSave, sessionLifetimeMilliseconds);
					}
				}
				else {
					lastSavedAt = moment(data.timestamp);
					setSaveStatus('Terakhir disimpan pada pukul ' + lastSavedAt.format('HH.mm'));
					isSaving = false;

					if (saveAgain)
						handleAjaxSave();
					else
						sessionLifetimeTimeout = window.setTimeout(handleAjaxSave, sessionLifetimeMilliseconds);
				}
			});
		}
	};

	// Handle AJAX saving when the save button is clicked
	// @param e event
	function handleSaveButtonAjax(e) {
		e.preventDefault();
		this.blur();

		if (isSaving) {
			// If an AJAX save is currently being performed, submit the form immediately
			// This is because the user expects the button to be reliable
			$('#picture').val('');
			$('#application-form').submit();
		}
		else {
			handleAjaxSave(function() {
				$('#application-form').submit();
			})
		}
	}

	// Create or destroy rows for siblings
	function handleSiblingFields() {
		$('td.sibling-name input').each(function() {
			var t = $(this);
			if (!t.parent().parent().hasClass('prototype')) {
				if (t.val())
					t.parent().parent().removeClass('engineered').addClass('notempty');
				else
					t.parent().parent().addClass('engineered').removeClass('notempty');
			}
		})

		var v = parseInt($(this).val());
		var o = $('.siblings-table tbody tr').length - 1;
		if (v > o) {
			var d = v - o - 1;
			for (i=0; i<d; i++) {
				cl = $('.prototype').clone().removeClass('prototype');
				rand = Math.ceil(Math.random() * 1000).toString();
				$('input, select', cl).each(function() { $(this).replaceKey(rand); } );
				$('.siblings-table tbody').append(cl);
			}
		}
		if (v <= o) {
			var d = o - v + 1;
			for (i=0; i<d; i++) {
				$('tr.engineered').first().detach();
			}
		}
	}

	// Toggle whether the YES checkbox is disabled or not
	// @param disable true to disable
	function toggleYESEligibility(disable) {
		if (disable) {
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

	// Check DOB for YES eligibility
	function checkDOBForYES() {
		var currentDOBValue = getDateValue('.applicant-dob');
		var eligible = validateDate(currentDOBValue, dob_lower_limit_yes, dob_upper_limit);
		toggleYESEligibility(!eligible);
	}

	// Check whether the applicant is in acceleration class or not
	// If she/he is, disable the YES checkbox
	function checkAccelerationClassForYESEligibility() {
		toggleYESEligibility($('#in_acceleration_class').is(':checked'));
	}

	// Handle Bootstrap typeahead for school names
	function typeaheadSchool(query) {
		var schs = [query];
		$('datalist[data-for=high_school_name] option[value*="' + query + '"]').each(function() {
			schs.push($(this).attr('value'));
		})

		return schs;
	}

	/** DOM MANIPULATION **/

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

	// -- Finalization handling --
	$('#finalisasi .recheck').hide();
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
		activateFormValidation();
	}
	
	// Family
	$('#number_of_children_in_family').change(handleSiblingFields);

	// -- YES field handling --
	var previously_selected_yes = $('#program_yes').prop('checked');

	// @param disable: true to disable YES checkbox
	checkAccelerationClassForYESEligibility();
	$('#in_acceleration_class').change(checkAccelerationClassForYESEligibility);

	checkDOBForYES();
	$('.applicant-dob select').change(checkDOBForYES);

	// Submit on file upload
	$('input[type=file]').change(function() {
		setSaveStatus('Mengunggah foto...', true);
		$('#application-form').submit();
	});
	
	// Country Preference Ordering
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
	$(window).scroll(function() {
		el = $('.form-tools-container');
		t = $('.form-tools').offset().top;
		y = $(this).scrollTop();
		
		if (y >= t) {
			el.addClass('fixed');
		} else {
			// console.log('no more');
			el.removeClass('fixed');
		}
	});
	// Tooltip on hover on save button
	$('#save-button').tooltip({placement: 'bottom'});
	
	// Highlight the row containing the currently-focused form control
	$('#appform input, #appform select, #appform textarea').focus(function() {
		$(this).parents('tr').first().addClass('selected');
	});
	$('#appform input, #appform select, #appform textarea').blur(function() {
		$(this).parents('tr').first().removeClass('selected');
	});

	// Phone number combo field
	$('span.phone-number input, span.number input')
		.focus(function(){$(this.parentNode).addClass('focus')})
		.blur(function(){$(this.parentNode).removeClass('focus')});

	// AFS Program force checkbox
	$('#program_afs').prop('checked', true).change(function() {
		$(this).prop('checked', true);
	});

	// Typeahead for school names
	$('#high_school_name')
		.attr('autocomplete', 'off')
		.typeahead({
			source: typeaheadSchool,
			items: 20
		});

	// -- AJAX Saving --

	$('#save-status').text('Terakhir disimpan pada pukul ' + lastSavedAt.format('HH.mm'));

	if (autoSave) {
		var onChange = function() {
			handleAjaxSave();
		}
		$('#application-form input, #application-form textarea, #application-form select').change(onChange);
		$('#application-form input, #application-form textarea, #application-form select').focus(ping);
		$('#finalize, input[type=file]').off('change', onChange);
		$('#save-button').click(handleSaveButtonAjax);

		// First ping
		ping();

		$(window).on('load', function() {
			ping();
			$(window).focus(ping);
		});
	}
	
});