<h1><?php echo $headline; ?></h1>

<?php
if (isset($flash)) {
  echo $flash;
}
?>

<a href="<?= base_url() ?>sliders/create/<?= $parent_id ?>">
  <button type="button" class="btn btn-default" style="clear: both;">Previous Page</button>
</a>

<?php
echo Modules::run("slides/_draw_create_modal", $parent_id);
?>

<?php if ($num_rows < 1) : ?>
  <?php echo "So far you have not uploaded any " . $entity_name . " for " . $parent_title . "."; ?>
<?php else : ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white file"></i><span class="break"></span>Existing Slides</h2>
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
                $target_url = $row->target_url;
                if ($target_url != "") {
                  $view_page_url = $target_url;
                }
                $edit_page_url = base_url() . "slides/view/" . $row->id;

                $picture = $row->picture;
                $pic_path = base_url() . "uploads/slider_pics/" . $picture;
            ?>
              <tr>
                <td>
                  <?php if ($picture != "") : ?>
                    <img src="<?php echo $pic_path; ?>" alt="thumbnail">
                  <?php endif; ?>
                </td>
                <td class="center">
                  <?php if (isset($view_page_url)) : ?>
                    <a class="btn btn-success" href="<?php echo $view_page_url; ?>">
                      <i class="halflings-icon white zoom-in"></i>
                    </a>
                  <?php endif; ?>
                  <a class="btn btn-info" href="<?php echo $edit_page_url; ?>">
                    <i class="halflings-icon white edit"></i>
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