<ul class="nav navbar-nav">
  <?php 
  $this->load->module("store_categories");

  foreach ($parent_categories as $id => $cat_title) :
    $parent_cat_id = $id;
    $parent_cat_title = $cat_title;
  ?>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $parent_cat_title; ?> <span class="caret"></span></a>
      <ul class="dropdown-menu">
        <?php 
        $query = $this->store_categories->get_where_custom("parent_cat_id", $parent_cat_id);
        foreach ($query->result() as $row) {
          $cat_url = $row->cat_url;
          echo '<li><a href="' . $target_url_start . $cat_url . '">' . $row->cat_title . '</a></li>';
        }
        ?>
      </ul>
    </li>
  <?php endforeach; ?>
</ul>