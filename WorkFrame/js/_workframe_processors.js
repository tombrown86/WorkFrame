_wf_custom_field_error_class = 'has-error';
_wf_custom_field_success_class = 'has-success';
_wf_custom_field_warning_class = 'has-warning';

function _wf_field_id(form_id, field_name, name_container_array) {
    return (name_container_array?name_container_array+'__':'')+ form_id + '__' + field_name;
}

function _wf_get_results_by_field(results) {
    var field_results = [];
    for (i in results) {
	if (typeof field_results[results[i]['field_name']] == 'undefined') {
	    field_results[results[i]['field_name']] = []
	}
	field_results[results[i]['field_name']].push(results[i]);
    }
    return field_results;
}

function _wf_set_fields_group_state(field_id, state) {
    var $field_group = $("#" + field_id).closest('.wf_field_group');

    $field_group.addClass(window['_wf_custom_field_' + state + '_class'] + ' wf_' + state + '_field');
}

function _wf_reset_field_processing_output(field_id) {
    $("#" + field_id).closest('.wf_field_group').removeClass('wf_error_field wf_success_field wf_warning_field ' + _wf_custom_field_error_class + ' ' + _wf_custom_field_success_class + ' ' + _wf_custom_field_warning_class);
    $("#" + field_id + "__wf_field_error_container").html('');
}

//TODO: Populate the main error list UL at top of form ?
//TODO: Merge the following 2 funcs as they are v similar
function _wf_show_errors(form_id, errors, name_container_array) {
    var errors = _wf_get_results_by_field(errors); // Actually - now this is only ever called for individual fields! so this is pointless. TODO: remove it

    for (field_name in errors) {
	var html = '<ul class="wf_field_errors_inline">';

	for (i in errors[field_name]) {
	    error = errors[field_name][i];
	    html += '<li>' + wf_h(error['error_message']);
	    if (error["error_details"]) {
		html += " <span class'wf_error_details'>" + (wf_h(error['error_details'])) + "</span>";
	    }
	    html += '</li>';
	}
	html += '</ul>';
	var field_id = _wf_field_id(form_id, field_name, name_container_array);
	$("#" + field_id + "__wf_field_error_container").html(html).show();
	_wf_set_fields_group_state(field_id, 'error')
    }
}

// TODO This is a clone of the above pretty much, perhaps refactor + merge
function _wf_show_warnings(form_id, warnings, name_container_array) {
    var warnings = _wf_get_results_by_field(warnings); // Actually - now this is only ever called for individual fields! so this is pointless. TODO: remove it

    $("#" + form_id + " .wf_field_warning_container").html('').hide();

    for (field_name in warnings) {
	var html = '<ul class="wf_field_warnings_inline">';
	for (i in warnings[field_name]) {
	    warning = warnings[field_name][i];

	    html += '<li>' + wf_h(warning['warning_message']);
	    if (warning["warning_details"]) {
		html += " <span class'wf_warning_details'>" + (wf_h(warning['warning_details'])) + "</span>";
	    }
	    html += '</li>';
	}
	html += '</ul>';
	var field_id = _wf_field_id(form_id, field_name, name_container_array);
	_wf_set_fields_group_state(field_id, 'warning');
	$("#" + field_id + "__wf_field_warnings_container").html(html).show();
    }
}

function _wf_show_successes(form_id, success_field_ids, name_container_array) {
    for (i in success_field_ids) {
		_wf_set_fields_group_state(success_field_ids[i], 'success');
    }
}

function _wf_validate_email(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function _wf_validate_password(pw) {
    var re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#\_\.@£$!%*?&=\(\)\-\]\[])[A-Za-z\d#\_\.@£$!%*?&=\(\)\-\]\[]{8,}$/;
    return re.test(pw);
}
