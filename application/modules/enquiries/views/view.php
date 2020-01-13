<h1><?php echo $headline; ?></h1>
<?php
echo validation_errors('<p style="color: red;">', "</p>");

if (isset($flash)) {
  echo $flash;
}
?>

<a href="<?php echo base_url(); ?>enquiries/create/<?php echo $update_id; ?>">
  <button type="button" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 30px;">Reply To This Message</button>
</a>

<!-- Button trigger modal -->
<button type="button" class="btn btn-info" style="margin-top: 30px; margin-bottom: 30px;" data-toggle="modal" data-target="#commentModal">Create New Comment</button>
<!-- Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Create Comment</h4>
      </div>
      <div class="modal-body">
        <?php
          $form_location = base_url() . "comments/submit";
        ?>
        <form class="form-horizontal" action="<?php echo $form_location; ?>" method="post">
          <div class="control-group">
            <label class="control-label" for="comment">Comment</label>
            <div class="controls">
              <textarea id="comment" name="comment" placeholder="Enter a comment" rows="6"></textarea>              
            </div>
          </div>
          <?php
            echo form_hidden("comment_type", "e"); // enquiry
            echo form_hidden("update_id", $update_id);
          ?>          
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>      
    </div>
  </div>
</div>

<?php
  $this->load->module("timedate");
  $this->load->module("store_accounts");

  foreach ($query->result() as $row) {
    $opened = $row->opened;

    if ($opened==1) {
      $icon = '<i class="icon-envelope"></i>';
    } else {
      $icon = '<i class="icon-envelope-alt" style="color: orange;"></i>';
    }

    $date_sent = $this->timedate->get_nice_date($row->date_created, "full");

    if ($row->sent_by == 0) {
      $sent_by = "Admin";
    } else {
      $sent_by = $this->store_accounts->_get_customer_name($row->sent_by);
    }

    $subject = $row->subject;
    $message = $row->message;
    $ranking = $row->ranking;
  }
?>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white star"></i><span class="break"></span>Enquiry Ranking</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <?php
        $form_location = base_url() . "enquiries/submit_ranking/" . $update_id;
      ?>
      <form class="form-horizontal" method="post" action="<?php echo $form_location; ?>">
        <fieldset>
          <div class="control-group">
            <label class="control-label" for="status">Ranking</label>
            <div class="controls">
              <?php
                if ($ranking > 0) {
                  unset($options[""]);
                }

                $additional_dropdown_code = 'class="form-control"';
                echo form_dropdown('ranking', $options, $ranking, $additional_dropdown_code);
              ?>
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" name="submit" value="Submit">Save changes</button>
            <button type="submit" class="btn btn-default" name="submit" value="Cancel">Cancel</button>
          </div>
        </fieldset>
      </form>
    </div>
  </div><!--/span-->
</div><!--/row-->

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white edit"></i><span class="break"></span>Enquiry Details</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">    	
			<table class="table table-striped table-bordered bootstrap-datatable datatable">
        <tbody>          
          <tr>
            <td style="font-weight: bold;">Date Sent</td>
            <td><?php echo $date_sent; ?></td>
          </tr>
          <tr>
            <td style="font-weight: bold;">Sent By</td>
            <td><?php echo $sent_by; ?></td>
          </tr>
          <tr>
            <td style="font-weight: bold;">Subject</td>
            <td><?php echo $subject; ?></td>
          </tr>
          <tr>
            <td style="font-weight: bold; vertical-align: top;">Message</td>
            <td style="vertical-align: top;"><?php echo nl2br($message) ?></td>              
          </tr>
        </tbody>
      </table>
    </div>
  </div><!--/span-->
</div><!--/row-->    	

<?php echo Modules::run("comments/_draw_comments", "e", $update_id) ; ?>