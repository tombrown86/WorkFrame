function wf_h(s) {
	return $('<div/>').text(s).html().replace(/"/g, '&quot;');
}
function wf_nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
function wf_prevent_default(event) {
	event.preventDefault ? event.preventDefault() : (event.returnValue = false);
}
