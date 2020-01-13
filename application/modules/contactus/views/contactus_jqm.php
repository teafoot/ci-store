<?php
  $_SESSION['start'] = time();

  if (!isset($_SESSION['antispam_token'])) {
    $this->load->module("site_security");
    $token = $this->site_security->generate_random_string(10);
    $_SESSION['antispam_token'] = $token;
  }
?>	

<div class="ui-body ui-body-a ui-corner-all">
	<h3>Contact Us</h3>
	<?php echo validation_errors('<p style="color: red;">', "</p>"); ?>

	<?= form_open($form_location) ?>
		<input type="text" id="firstname" name="firstname" value="<?php echo $_SESSION['antispam_token']; ?>" style="display:none !important" tabindex="-1" autocomplete="off" />

		<label for="text-yourname">Name:</label>
	  <input type="text" name="yourname" id="yourname" value="<?= $yourname ?>">

	  <label for="email">Email:</label>
	  <input type="email" name="email" id="email" value="<?= $email ?>">

	  <label for="telnum">Telephone:</label>
	  <input type="text" name="telnum" id="telnum" value="<?= $telnum ?>">

	  <label for="message">Message:</label>
	  <textarea cols="40" rows="8" name="message" id="message"><?= $message ?></textarea>

	  <button type="submit" name="submit" value="Submit" class="ui-shadow ui-btn ui-corner-all">Submit</button>
	<?= form_close() ?>
</div>