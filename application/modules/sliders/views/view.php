<h2><?php echo $slider_title; ?></h2>

<p><?php echo $showing_statement; ?></p>
<?php echo $pagination; ?>

<div class="row">
	<?php foreach ($query->result() as $row) :
		$item_page = base_url() . $item_segments . $row->item_url;

		$item_title = $row->item_title;
		$item_price = $row->item_price;
		$was_price = $row->was_price;

		$small_pic = $row->small_pic;
		$small_pic_path = base_url() . "uploads/small_pics/" . $small_pic;
	?>
		<div class="col-md-2 img-thumbnail" style="margin: 12px; min-height: 300px;">
			<a href="<?php echo $item_page; ?>">
				<img src="<?php echo $small_pic_path; ?>" title="<?php echo $item_title; ?>" class="img-responsive">
			</a>
			<br>
			<a href="<?php echo $item_page; ?>">
				<h4><?php echo $item_title; ?></h4>
			</a>
			<div style="color: red; font-weight: bold;">
				<?php echo $currency_symbol . number_format($item_price, 2); ?>
				<?php if ($was_price > 0) :	?>
					<span style="font-weight: normal; color: #333; text-decoration: line-through;"><?php echo $currency_symbol . $was_price; ?></span>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<?php echo $pagination; ?>