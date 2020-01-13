<a href="<?php echo base_url(); ?>yourmessages/create/<?php echo $code; ?>">
  <button type="button" class="btn btn-default" style="margin-top: 30px; margin-bottom: 30px;">Reply</button>
</a>

<div class="row">
  <div class="col-md-8">
		<p style="margin-top: 24px;">Message sent on <?php echo $date_created; ?></p>
		<h2 style="margin-top: 48px;"><?php echo $subject; ?></h2>
		<p><?php echo $message; ?></p>
	</div>
</div>