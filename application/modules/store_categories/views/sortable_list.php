<style type="text/css">
	.sort {
		list-style: none;
		border: 1px solid #aaa;
		color: #333;
		padding: 10px;
		margin-bottom: 4px;
	}
</style>

<ul id="sortlist">
	<?php
    $this->load->module("store_categories");

    foreach ($query->result() as $row) :
      $edit_category_url = base_url() . "store_categories/create/" . $row->id;

      if ($row->parent_cat_id == 0) {
        $parent_cat_title = "&nbsp;";
      } else {
        $parent_cat_title = $this->store_categories->_get_cat_title($row->parent_cat_id);
      }
  ?>
		<li class="sort" id="<?php echo $row->id; ?>">
			<i class="icon-sort"></i> <?php echo $row->cat_title; ?>
			<?php echo $parent_cat_title; ?>
      <?php
      	$num_sub_cats = $this->store_categories->_count_sub_cats($row->id);

        if ($num_sub_cats > 0) {
          $parent_sub_cats_url = base_url() . "store_categories/manage/" . $row->id;

          if ($num_sub_cats == 1) {
            $entity = "Sub category";
          } else {
            $entity = "Sub categories";
          }

          echo '<a class="btn btn-default" href="' . $parent_sub_cats_url . '"><i class="halflings-icon white zoom-in"></i> ' . $num_sub_cats . ' ' . $entity . '</a>';
        }
      ?>
      <a class="btn btn-info" href="<?php echo $edit_category_url; ?>">
        <i class="halflings-icon white edit"></i>
      </a>
		</li>
	<?php endforeach; ?>
</ul>