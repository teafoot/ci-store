<!-- Button trigger modal -->
<button type="button" class="btn btn-info" style="margin-top: 30px; margin-bottom: 30px;" data-toggle="modal" data-target="#commentModal">Create New Slide</button>
<!-- Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Create Slide</h4>
      </div>
      <div class="modal-body">
        <?php
          $form_location = base_url() . "slides/submit_create";
        ?>
        <form class="form-horizontal" action="<?php echo $form_location; ?>" method="post">
          <div class="control-group">
            <label class="control-label" for="target_url">Target URL (Optional)</label>
            <div class="controls">
              <input type="text" class="span9" name="target_url" placeholder="Enter the target URL here.">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="alt_text">Alt-Text (Optional)</label>
            <div class="controls">
              <input type="text" class="span9" name="alt_text" placeholder="Enter the alt-text here.">
            </div>
          </div>
          <?php
            echo form_hidden("parent_id", $parent_id);
          ?>          
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" name="submit" value="Submit">Submit</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>      
    </div>
  </div>
</div>