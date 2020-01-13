<h1><?php echo $headline; ?></h1>
<h2><?php echo $sub_headline; ?></h2>

<?php
if (isset($flash)) {
  echo $flash;
}
?>

<a href="<?= base_url() ?>store_items/create/<?= $parent_id ?>">
  <button type="button" class="btn btn-default" style="clear: both;">Previous Page</button>
</a>
<a href="<?= base_url() ?>item_galleries/upload_image/<?= $parent_id ?>" class="btn btn-primary">Upload New Picture</a>

<?php if ($num_rows < 1) : ?>
  <?php echo "So far you have not uploaded any gallery " . $entity_name . " for " . $parent_title . "."; ?>
<?php else : ?>
  <div class="row-fluid sortable" style="margin-top: 12px;">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white file"></i><span class="break"></span>Existing Pictures</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>
      <div class="box-content">
        <table class="table table-striped table-bordered bootstrap-datatable datatable">
          <thead>
            <tr>
              <th>Picture</th>
              <th class="span2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($query->result() as $row) :
                $delete_url = base_url() . "item_galleries/deleteconf/" . $row->id;

                $picture = $row->picture;
                $pic_path = base_url() . "uploads/item_galleries_pics/" . $picture;
            ?>
              <tr>
                <td>
                  <?php if ($picture != "") : ?>
                    <img src="<?php echo $pic_path; ?>" alt="thumbnail">
                  <?php endif; ?>
                </td>
                <td class="center">
                  <a class="btn btn-danger" href="<?php echo $delete_url; ?>">
                    <i class="halflings-icon white trash"></i>
                  </a>                  
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div><!--/span-->
  </div><!--/row-->
<?php endif; ?>