<div id="bottom_nav">
	<?php
		foreach ($query->result() as $row) {
			$page_title = $row->page_title;
			$page_url = $row->page_url;

			echo anchor($page_url, $page_title);
		}
	?>
</div>