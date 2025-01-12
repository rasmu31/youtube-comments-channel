$(document).ready(function() {
	$('#form_getIdChannel').on("submit", function (e) {
		e.preventDefault();
		$.post("",
		{'getIdChannel': '', 'username': $("#username").val()},
			function(data) {
				data = JSON.parse(data);
				if (data.idchannel == "")
					text = "Not found";
				else
					text = "Channel id : " + data.idchannel + " / Channel title : " + data.name;
				$("#result_idchannel").html(text);
			});
	});
	
	$('#form_getUsername').on("submit", function (e) {
		e.preventDefault();
		$.post("",
		{'getUsername': '', 'idchannel': $("#idchannel").val()},
			function(data) {
				data = JSON.parse(data);
				if (data.username == "")
					text = "Not found";
				else
					text = "Channel name : " + data.username + " / Channel title : " + data.name;
				$("#result_username").html(text);
			});
	});
	
	$("#all_files").change(function() {
		$('.files_checkbox').prop('checked', this.checked);
		$('#channels option').prop('selected', this.checked);
	});
	
	$('#channels').click(function() {
		$('input[name="files[]"]').prop('checked', false);
		$('#channels option:selected').each(function() {
			$('.files_checkbox[value*=' + $(this).val() + ']').prop('checked', true);
			
			if ($(".files_checkbox").length == $(".files_checkbox:checked").length)
				$('#all_files').prop('checked', true);
			else
				$('#all_files').prop('checked', false);
		});
	});
	
	$(".files_checkbox").change(function() {
		if (!this.checked) {
			$('#all_files').prop('checked', false);	
		}
		else {
			if ($(".files_checkbox").length == $(".files_checkbox:checked").length)
				$('#all_files').prop('checked', true);
		}
		
	});
	
	$('#searchForm').on("submit", function (e) {
		if ($(".files_checkbox:checked").length == 0) {
			e.preventDefault();
			$(".errorForm").remove();
			$(".td_button_submit").append('<div class="errorForm">Vous need to select at least one file.</div>');
		}
	});
	
});