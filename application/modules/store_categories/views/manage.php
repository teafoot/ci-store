<h1>Manage Categories</h1>

<?php
if (isset($flash)) {
  echo $flash;
}

$create_category_url = base_url() . "store_categories/create";
?>
<a href="<?php echo $create_category_url; ?>">
  <button type="button" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 30px;">Add new category</button>
</a>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="white icon-align-justify"></i><span class="break"></span>Existing Categories</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <?php
        echo Modules::run("store_categories/_draw_sortable_list", $parent_cat_id);
      ?>
      <!-- <table class="table table-striped table-bordered bootstrap-datatable datatable">
        <thead>
          <tr>
            <th>Category title</th>
            <th>Parent category</th>
            <th>Sub categories</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $this->load->module("store_categories");

          foreach ($query->result() as $row) :
            if ($row->parent_cat_id == 0) {
              $parent_cat_title = "-";
            } else {
              $parent_cat_title = $this->store_categories->_get_cat_title($row->parent_cat_id);
            }

            $edit_category_url = base_url() . "store_categories/create/" . $row->id;

            $num_sub_cats = $this->store_categories->_count_sub_cats($row->id);
          ?>
            <tr>
              <td><?php echo $row->cat_title; ?></td>
              <td class="center"><?php echo $parent_cat_title; ?></td>
              <td class="center">
                <?php
                if ($num_sub_cats > 0) {
                  $parent_sub_cats_url = base_url() . "store_categories/manage/" . $row->id;

                  if ($num_sub_cats == 1) {
                    $entity = "Sub category";
                  } else {
                    $entity = "Sub categories";
                  }

                  echo '<a class="btn btn-default" href="' . $parent_sub_cats_url . '"><i class="halflings-icon white zoom-in"></i> ' . $num_sub_cats . ' ' . $entity . '</a>';
                } else {
                  echo "-";
                }
                ?>
              </td>
              <td class="center">
                <a class="btn btn-info" href="<?php echo $edit_category_url; ?>">
                  <i class="halflings-icon white edit"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table> -->
    </div>
  </div><!--/span-->

</div><!--/row-->