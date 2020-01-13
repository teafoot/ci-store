<h1>Content Management System</h1>

<?php
if (isset($flash)) {
  echo $flash;
}

$create_page_url = base_url() . "webpages/create";
?>
<a href="<?php echo $create_page_url; ?>">
  <button type="button" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 30px;">Create New Webpage</button>
</a>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white file"></i><span class="break"></span>Custom Webpages</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <table class="table table-striped table-bordered bootstrap-datatable datatable">
        <thead>
          <tr>
            <th>Page URL</th>
            <th>Page Title</th>
            <th class="span2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($query->result() as $row) :
            $view_page_url = base_url() . $row->page_url;
            $edit_page_url = base_url() . "webpages/create/" . $row->id;
            ?>
            <tr>
              <td><?php echo $view_page_url; ?></td>
              <td class="center"><?php echo $row->page_title; ?></td>
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