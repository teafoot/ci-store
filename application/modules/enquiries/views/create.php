<h1><?php echo $headline; ?></h1>
<?php
echo validation_errors('<p style="color: red;">', "</p>");

if (isset($flash)) {
  echo $flash;
}
?>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white edit"></i><span class="break"></span>Message Details</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <?php
      $form_location = base_url() . "enquiries/create/" . $update_id;
      ?>
      <form class="form-horizontal" method="post" action="<?php echo $form_location; ?>">
        <fieldset>
          <?php if (!isset($sent_by)) : ?>
          	<div class="control-group">
              <label class="control-label" for="sent_to">Recipient</label>
              <div class="controls">
                <?php
  				 	      $additional_dropdown_code = 'class="form-control span4"';
  		 	  		    echo form_dropdown('sent_to', $options, $sent_to, $additional_dropdown_code);
  		  	      ?>
              </div>
            </div>      
          <?php endif; ?>
          <div class="control-group">
            <label class="control-label" for="subject">Subject</label>
            <div class="controls">
              <input type="text" class="span6" name="subject" value="<?php echo $subject; ?>">
            </div>
          </div>          
          <div class="control-group hidden-phone">
            <label class="control-label" for="message">Message</label>
            <div class="controls">
              <textarea class="cleditor" name="message" rows="3"><?php echo $message; ?></textarea>
            </div>
          </div>
          <?php 
            if (isset($sent_by)) {
              echo form_hidden("sent_to", $sent_by);
            }
          ?>                    
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" name="submit" value="Submit">Save changes</button>
            <button type="submit" class="btn" name="submit" value="Cancel">Cancel</button>
          </div>
        </fieldset>
      </form>
    </div>
  </div><!--/span-->
</div><!--/row-->