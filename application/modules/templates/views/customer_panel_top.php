<?php
	function _attempt_make_active($link_text) {
		if ((current_url() == base_url() . "youraccount/welcome") && ($link_text == "Your Messages")) {
			echo 'class="active"';
		}
	}
?>

<ul class="nav nav-tabs" style="margin-top: 24px;">
  <li role="presentation" <?php _attempt_make_active("Your Messages"); ?>>
  	<a href="<?php echo base_url(); ?>youraccount/welcome">Your Messages</a>
  </li>
  <li role="presentation"><a href="<?php echo base_url(); ?>yourorders/browse">Your Orders</a></li>
  <li role="presentation"><a href="#">Update Your Profile</a></li>
  <li role="presentation"><a href="<?php echo base_url(); ?>youraccount/logout">Log Out</a></li>  
</ul>