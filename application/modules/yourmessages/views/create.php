<h1><?php echo $headline; ?></h1>

<?php
	echo validation_errors('<p style="color: red;">', "</p>");

	$form_location = current_url();
?>

<div class="row">
  <div class="col-md-8">
		<form action="<?php echo $form_location; ?>" method="post" style="margin-top: 24px;">
			<?php if (empty($code)) : ?>
			  <div class="form-group">
			    <label for="subject">Subject</label>
			    <input type="text" name="subject" value="<?php echo $subject; ?>" class="form-control" id="subject" placeholder="Enter your subject">
			  </div>
			<?php else : ?>
				<?php echo form_hidden("subject", $subject); ?>
			<?php endif; ?>
		  <div class="form-group">
		    <label for="message">Message</label>
		    <textarea name="message" class="form-control" rows="6" placeholder="Enter your message"><?php echo $message; ?></textarea>
		  </div>
		  <div class="checkbox">
		    <label>
		      <input name="urgent" value="1" type="checkbox" <?php 
		      	if ($urgent == 1) {
		      		echo "checked";
		      	}
		      ?>> Urgent?
		    </label>
		  </div>
		  <?php	echo form_hidden("token", $token); ?>
		  <button type="submit" name="submit" value="Submit" class="btn btn-primary">Submit Your Message</button>
		  <button type="submit" name="submit" value="Cancel" class="btn btn-default">Cancel</button>
		</form>
	</div>
</div>