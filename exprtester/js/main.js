var tmr_pos = 0;
(function($) {
	$('document').ready(function() {
		$('#code-parse').removeAttr('disabled');
		$('#frmcode').on('submit', function(evt) {
			evt.preventDefault();
			doParse();
		});
		$('#log-clear').on('click', function(evt) {
			$('#testlog').html('');	
		});
	});
	document.getElementById('code').onkeydown = function(e) {
		if(e.keyCode==9 || e.which==9) {
			e.preventDefault();
			var s = this.selectionStart;
            this.value = this.value.substring(0,this.selectionStart) + "\t" + this.value.substring(this.selectionEnd);
            this.selectionEnd = s+1;
		}
	}
	tmr_pos = setInterval(function() {
		$('#code-pos').html(document.getElementById('code').selectionStart+1);
	}, 100);
})(jQuery);

function o_newTest() {
	var today = new Date();
	var id = today.getTime();
	var out = ''
	+ '<div class="panel panel-default" id="test-' + id + '">'
	+ '  <div class="panel-heading">Test # ' + id + '</div>'
	+ '  <div class="panel-body test-output">'
	+ '  </div>'
	+ '</div>';
	$('#testlog').prepend(out);
	return id;
}

function o_printIntoTest(id, str, type) {
	var ty = type || 'info';
	ty = (ty==='info'||ty==='success'||ty==='warning'||ty==='danger') ? ty : 'info';
	var out = ''
	+ '    <div class="alert alert-' + ty + '">'
	+ '      <p>' + str + '</p>'
	+ '    </div>';
	$('#test-' + id + ' .test-output').prepend(out);
}

function o_printResultIntoTest(id, result) {
	var out = ''
	+ '    <div class="alert alert-info">'
	+ '      <p>Parse result: </p>'
	+ '      <pre>' + result + '</pre>'
	+ '    </div>';
	$('#test-' + id + ' .test-output').prepend(out);
}

function doParse() {
	var id = o_newTest();
	
	var data = $('#frmcode').serialize();
	var url = 'parser/client.php';
	
	o_printIntoTest(id, 'Sending request to <em>' + url + '</em>');
	
	var st = new Date();
	
	$.ajax({
		cache: false,
		timeout: 8000,
		data: data,
		method: 'POST',
		dataType: 'text',
		url: url,
		success: function( data , status, xhr ) {
			o_printResultIntoTest(id, data);
		},
		error: function( xhr , status , error ) {
			o_printResultIntoTest(id, xhr.responseText);
			o_printIntoTest(id, 'Error on AJAX request! <strong>' + error + '<strong>.', 'danger');
		},
		complete: function( xhr , status ) {
			var ed = new Date();
			var delta = ed.getTime() - st.getTime();
			var ty = (status==='success') ? 'success' : 'warning';
			o_printIntoTest(id, 'Request ended with status <strong>' + status + '</strong> taking <em>' + delta + ' ms</em>.', ty);
		}
	});
	
}