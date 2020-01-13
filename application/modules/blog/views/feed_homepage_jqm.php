<h3 class="ui-bar ui-bar-a">The Blog</h3>
<div data-role="collapsibleset" data-theme="a" data-content-theme="a" data-mini="true">
	<?php
	$this->load->module("timedate");

	foreach ($query->result() as $row) :
		$date_published = $this->timedate->get_nice_date($row->date_published, "mini");

		$picture = $row->picture;
		$thumbnail = str_replace(".", "_thumb.", $picture);
		$thumbnail_path = base_url() . "uploads/blog_pics/" . $thumbnail;

		$article_preview = word_limiter($row->blog_content, 25);
		$article_url = base_url() . "blog/article/" . $row->blog_url;
	?>
	  <div data-role="collapsible">
	  	<h3><?php echo $row->blog_title; ?></h3>
	    <img src="<?php echo $thumbnail_path; ?>" class="img-responsive img-thumbnail">
			<p style="font-size: 0.8em;">
	  		<?php echo $row->author . "&nbsp;&nbsp;"; ?>
	  		<span style="color: #999;"><?php echo $date_published; ?></span>
	  	</p>
	  	<p><?php echo $article_preview; ?></p>
	  	<p style="text-align: right;">
	  		<a href="<?= $article_url ?>">Read More</a>
	  	</p>
	  </div>
	<?php endforeach; ?>
</div>
