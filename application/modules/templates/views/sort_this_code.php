<?php
$first_bit = $this->uri->segment(1);
$third_bit = $this->uri->segment(3);

if (!empty($third_bit)) {
	$start_of_target_url = "../../";
} else {
	$start_of_target_url = "../";
}
?>

<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.min.js" integrity="sha256-lnH4vnCtlKU2LmD0ZW1dU7ohTTKrcKP50WA9fa350cE=" crossorigin="anonymous"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#sortlist").sortable({
			stop: function(event, ui) {
				saveChanges();
			}
		});
		$("#sortlist").disableSelection();
	});

	function saveChanges() {
		var list_size = $("#sortlist > li").size();
		var data_string = "list_size=" + list_size;		

		for (var i = 1; i <= list_size; i++) {
			var category_id = $('#sortlist li:nth-child(' + i + ')').attr('id');
			data_string += "&list_item_" + i + "=" + category_id;
		}

		$.ajax({
			type: "POST",
			url: "<?php echo $start_of_target_url . $first_bit; ?>/sort", // view sources
			data: data_string
		});

		return false;
	}
</script>