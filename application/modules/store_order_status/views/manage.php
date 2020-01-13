<h1>Manage Order Status Options</h1>

<?php
if (isset($flash)) {
  echo $flash;
}

$create_account_url = base_url() . "store_order_status/create";
?>
<a href="<?php echo $create_account_url; ?>">
  <button type="button" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 30px;">Add new order status option</button>
</a>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white list-alt"></i><span class="break"></span>Current Order Status Options</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <table class="table table-striped table-bordered bootstrap-datatable datatable">
        <thead>
          <tr>
            <th>Status Title</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($query->result() as $row) :
            $edit_store_order_status_url = base_url() . "store_order_status/create/" . $row->id;
          ?>
            <tr>
              <td class="center"><?php echo $row->status_title; ?></td>
              <td class="center">
                <a class="btn btn-info" href="<?php echo $edit_store_order_status_url; ?>">
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