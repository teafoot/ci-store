<h1><?php echo $headline; ?></h1>
<?php
echo validation_errors('<p style="color: red;">', "</p>");

if (isset($flash)) {
  echo $flash;
}
?>

<!-- Only on update, not create. -->
<?php if (is_numeric($update_id)) : ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white edit"></i><span class="break"></span>Item Options</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>
      <div class="box-content">
        <?php if ($got_gallery_pic) : ?>
          <div class="alert alert-info">You got at least one gallery picture for this item.</div>
        <?php 
          $gallery_btn_theme = "success";
          $delete_btn_text = "Delete Main Item Image";
        else:
          $gallery_btn_theme = "primary";
          $delete_btn_text = "Delete Item Image";
        ?>
        <?php endif; ?>        
        <?php if (empty($big_pic)) : ?>
          <a href="<?php echo base_url(); ?>/store_items/upload_image/<?php echo $update_id; ?>">
            <button type="button" class="btn btn-primary">Upload Item Image</button>
          </a>
        <?php else : ?>
          <a href="<?php echo base_url(); ?>/store_items/delete_image/<?php echo $update_id; ?>">
            <button type="button" class="btn btn-danger"><?= $delete_btn_text ?></button>
          </a>
        <?php endif; ?>
        <a href="<?php echo base_url(); ?>item_galleries/update_group/<?php echo $update_id; ?>">
          <button type="button" class="btn btn-<?= $gallery_btn_theme ?>">Manage Item Gallery</button>
        </a>
        <a href="<?php echo base_url(); ?>/store_item_colors/update/<?php echo $update_id; ?>">
          <button type="button" class="btn btn-primary">Update Item Colors</button>
        </a>
        <a href="<?php echo base_url(); ?>/store_item_sizes/update/<?php echo $update_id; ?>">
          <button type="button" class="btn btn-primary">Update Item Sizes</button>
        </a>
        <a href="<?php echo base_url(); ?>/store_cat_assign/update/<?php echo $update_id; ?>">
          <button type="button" class="btn btn-primary">Update Item Categories</button>
        </a>
        <a href="<?php echo base_url(); ?>/store_items/deleteconf/<?php echo $update_id; ?>">
          <button type="button" class="btn btn-danger">Delete Item</button>
        </a>
        <a href="<?php echo base_url(); ?>/store_items/view/<?php echo $update_id; ?>">
          <button type="button" class="btn btn-default">View Item In Shop</button>
        </a>
      </div>
    </div><!--/span-->
  </div><!--/row-->
<?php endif; ?>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white edit"></i><span class="break"></span>Item Details</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <?php
      $form_location = base_url() . "store_items/create/" . $update_id;
      ?>
      <form class="form-horizontal" method="post" action="<?php echo $form_location; ?>">
        <fieldset>
          <div class="control-group">
            <label class="control-label" for="item_title">Item Title</label>
            <div class="controls">
              <input type="text" class="span6" name="item_title" value="<?php echo $item_title; ?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="was_price">Was Price <span style="color: green;">(optional)</span></label>
            <div class="controls">
              <input type="text" class="span1" name="was_price" value="<?php echo $was_price; ?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="status">Status</label>
            <div class="controls">
              <?php
              $additional_dropdown_code = 'id="selectError3"';
              $options = array(
                  '' => 'Please Select...',
                  '1' => 'Active',
                  '0' => 'Inactive'
              );
              echo form_dropdown('status', $options, $status, $additional_dropdown_code);
              ?>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="item_price">Item Price</label>
            <div class="controls">
              <input type="text" class="span1" name="item_price" value="<?php echo $item_price; ?>">
            </div>
          </div>
          <div class="control-group hidden-phone">
            <label class="control-label" for="item_description">Item Description</label>
            <div class="controls">
              <textarea class="cleditor" name="item_description" rows="3"><?php echo $item_description; ?></textarea>
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

<?php if (!empty($big_pic)) : ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white edit"></i><span class="break"></span>Item Image</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>
      <div class="box-content">
        <img src="<?php echo base_url(); ?>/uploads/big_pics/<?php echo $big_pic; ?>">
      </div>
    </div><!--/span-->
  </div><!--/row-->
<?php endif; ?>