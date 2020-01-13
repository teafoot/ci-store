<h1><?php echo $headline; ?></h1>
<?php
echo validation_errors('<p style="color: red;">', "</p>");

if (isset($flash)) {
  echo $flash;
}
?>

<a href="<?= base_url() ?>webpages/manage">
  <button type="button" class="btn btn-default" style="clear: both; margin-bottom: 12px;">Previous Page</button>
</a>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white edit"></i><span class="break"></span>Page Details</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <?php
      $form_location = base_url() . "webpages/create/" . $update_id;
      ?>
      <form class="form-horizontal" method="post" action="<?php echo $form_location; ?>">
        <fieldset>
          <div class="control-group">
            <label class="control-label" for="page_title">Page Title</label>
            <div class="controls">
              <input type="text" class="span6" name="page_title" value="<?php echo $page_title; ?>">
            </div>
          </div>
          <div class="control-group hidden-phone">
            <label class="control-label" for="page_keywords">Page Keywords</label>
            <div class="controls">
              <textarea class="span6" name="page_keywords" rows="3"><?php echo $page_keywords; ?></textarea>
            </div>
          </div>
          <div class="control-group hidden-phone">
            <label class="control-label" for="page_description">Page Description</label>
            <div class="controls">
              <textarea class="span6" name="page_description" rows="3"><?php echo $page_description; ?></textarea>
            </div>
          </div>
          <div class="control-group hidden-phone">
            <label class="control-label" for="page_content">Page Content</label>
            <div class="controls">
              <textarea class="cleditor" name="page_content" rows="3"><?php echo $page_content; ?></textarea>
            </div>
          </div>          
          <div class="form-actions">
            <button type="submit" class="btn btn-primary" name="submit" value="Submit">Save changes</button>
            <button type="submit" class="btn" name="submit" value="Cancel">Cancel</button>
          </div>
        </fieldset>
      </form>
    </div>
  </div><!--/span-->
</div><!--/row-->

<!-- Only on update, not create. -->
<?php if (is_numeric($update_id)) : ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white edit"></i><span class="break"></span>Additional Options</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>
      <div class="box-content">
        <?php if ($update_id != 1 && $update_id != 2) : ?>  <!-- prevent deletion of home and contact us page -->
          <a href="<?php echo base_url(); ?>/webpages/deleteconf/<?php echo $update_id; ?>">
            <button type="button" class="btn btn-danger">Delete Page</button>
          </a>
        <?php endif; ?>
        <a href="<?php echo base_url() . $page_url; ?>">
          <button type="button" class="btn btn-default">View Page</button>
        </a>
      </div>
    </div><!--/span-->
  </div><!--/row-->
<?php endif; ?>