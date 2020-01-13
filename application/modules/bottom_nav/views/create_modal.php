<!-- Button trigger modal -->
<button type="button" class="btn btn-info" style="margin-top: 30px; margin-bottom: 30px;" data-toggle="modal" data-target="#commentModal">Create New Link</button>
<!-- Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Create Bottom Navigation Link</h4>
      </div>
      <div class="modal-body">
        <?php
          $form_location = base_url() . "bottom_nav/submit_create";
        ?>
        <form class="form-horizontal" action="<?php echo $form_location; ?>" method="post">          
          <div class="control-group">
            <label class="control-label" for="status">Page URL</label>
            <div class="controls">
              <?php
              echo form_dropdown("page_id", $options, "");
              ?>
            </div>
          </div>    
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" name="submit" value="Submit">Submit</button>
            <button type="submit" class="btn btn-default" name="submit" value="Cancel" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>      
    </div>
  </div>
</div>