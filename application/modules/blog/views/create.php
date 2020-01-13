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
      <h2><i class="halflings-icon white edit"></i><span class="break"></span>Blog Details</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <?php
      $form_location = base_url() . "blog/create/" . $update_id;
      ?>
      <form class="form-horizontal" method="post" action="<?php echo $form_location; ?>">
        <fieldset>
          <div class="control-group">
            <label class="control-label" for="date_published">Date Published</label>
            <div class="controls">
              <input type="text" class="input-xlarge datepicker" name="date_published" value="<?php echo $date_published; ?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="blog_title">Blog Entry Title</label>
            <div class="controls">
              <input type="text" class="span6" name="blog_title" value="<?php echo $blog_title; ?>">
            </div>
          </div>
          <div class="control-group hidden-phone">
            <label class="control-label" for="blog_keywords">Blog Entry Keywords</label>
            <div class="controls">
              <textarea class="span6" name="blog_keywords" rows="3"><?php echo $blog_keywords; ?></textarea>
            </div>
          </div>
          <div class="control-group hidden-phone">
            <label class="control-label" for="blog_description">Blog Entry Description</label>
            <div class="controls">
              <textarea class="span6" name="blog_description" rows="3"><?php echo $blog_description; ?></textarea>
            </div>
          </div>
          <div class="control-group hidden-phone">
            <label class="control-label" for="blog_content">Blog Entry Content</label>
            <div class="controls">
              <textarea class="cleditor" name="blog_content" rows="3"><?php echo $blog_content; ?></textarea>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="author">Author</label>
            <div class="controls">
              <input type="text" class="span6" name="author" value="<?php echo $author; ?>">
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
        <a href="<?php echo base_url(); ?>/blog/deleteconf/<?php echo $update_id; ?>">
          <button type="button" class="btn btn-danger">Delete Blog Entry</button>
        </a>
        <?php if (empty($picture)) : ?>
          <a href="<?php echo base_url(); ?>/blog/upload_image/<?php echo $update_id; ?>">
            <button type="button" class="btn btn-primary">Upload Image</button>
          </a>
        <?php else : ?>
          <a href="<?php echo base_url(); ?>/blog/delete_image/<?php echo $update_id; ?>">
            <button type="button" class="btn btn-danger">Delete Image</button>
          </a>
        <?php endif; ?>
        <a href="<?php echo base_url() . $blog_url; ?>">
          <button type="button" class="btn btn-default">View Blog Entry</button>
        </a>
      </div>
    </div><!--/span-->
  </div><!--/row-->
<?php endif; ?>

<?php if (!empty($picture)) : ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white edit"></i><span class="break"></span>Image</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>
      <div class="box-content">
        <img src="<?php echo base_url(); ?>/uploads/blog_pics/<?php echo $picture; ?>">
      </div>
    </div><!--/span-->
  </div><!--/row-->
<?php endif; ?>