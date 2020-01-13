<h1>Manage Sliders</h1>

<?php
if (isset($flash)) {
  echo $flash;
}

$create_sliders_url = base_url() . "sliders/create";
?>
<a href="<?php echo $create_sliders_url; ?>">
  <button type="button" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 30px;">Create new slider</button>
</a>

<?php if ($num_rows < 1) : ?>
  <p>You currently have no sliders on the website.</p>
<?php else : ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="white icon-align-justify"></i><span class="break"></span>Existing Sliders</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>
      <div class="box-content">
        <table class="table table-striped table-bordered bootstrap-datatable datatable">
          <thead>
            <tr>
              <th>Slider Title</th>
              <th>Slider URL</th>
              <th class="span2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($query->result() as $row) :
              $view_page_url = base_url() . $row->target_url;
              $edit_page_url = base_url() . "sliders/create/" . $row->id;
              ?>
              <tr>
                <td class="center"><?php echo $row->slider_title; ?></td>
                <td><?php echo $view_page_url; ?></td>
                <td class="center">
                  <a class="btn btn-success" href="<?php echo $view_page_url; ?>">
                    <i class="halflings-icon white zoom-in"></i>
                  </a>
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