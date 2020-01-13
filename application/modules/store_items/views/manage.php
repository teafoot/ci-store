<h1>Manage Items</h1>

<?php
if (isset($flash)) {
  echo $flash;
}

$create_item_url = base_url() . "store_items/create";
?>
<a href="<?php echo $create_item_url; ?>">
  <button type="button" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 30px;">Add new item</button>
</a>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white tag"></i><span class="break"></span>Items Inventory</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <table class="table table-striped table-bordered bootstrap-datatable datatable">
        <thead>
          <tr>
            <th>Item title</th>
            <th>Price</th>
            <th>Was price</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($query->result() as $row) :
            $view_item_url = base_url() . "store_items/view/" . $row->id;
            $edit_item_url = base_url() . "store_items/create/" . $row->id;
            $status = $row->status;
            $status_label = ($status == 1) ? "success" : "default";
            $status_description = ($status == 1) ? "Active" : "Inactive";
            ?>
            <tr>
              <td><?php echo $row->item_title; ?></td>
              <td class="center"><?php echo $row->item_price; ?></td>
              <td class="center"><?php echo $row->was_price; ?></td>
              <td class="center">
                <span class="label label-<?php echo $status_label; ?>"><?php echo $status_description; ?></span>
              </td>
              <td class="center">
                <a class="btn btn-success" href="<?php echo $view_item_url; ?>">
                  <i class="halflings-icon white zoom-in"></i>
                </a>
                <a class="btn btn-info" href="<?php echo $edit_item_url; ?>">
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