<h1>The Blog</h1>

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
	<div class="row" style="margin: 24px 0px;">
	  <div class="col-md-3">
	  	<img src="<?php echo $thumbnail_path; ?>" class="img-responsive img-thumbnail">
	  </div>
	  <div class="col-md-9">
	  	<h4><a href="<?php echo $article_url; ?>"><?php echo $row->blog_title; ?></a></h4>
	  	<p style="font-size: 0.8em;">
	  		<?php echo $row->author . "&nbsp;&nbsp;"; ?>
	  		<span style="color: #999;"><?php echo $date_published; ?></span>
	  	</p>
	  	<p><?php echo $article_preview; ?></p>
	  </div>
	</div>
<?php endforeach; ?>