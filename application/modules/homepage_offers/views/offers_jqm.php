<div class="ui-body">
  <ul data-role="listview">
    <?php foreach ($query->result() as $row) : 
      $item_page = base_url() . $item_segments . $row->item_url;

      $item_title = $row->item_title;
      $item_price = number_format($row->item_price, 2);
      $was_price = $row->was_price;

      $small_pic = $row->small_pic;
      $small_pic_path = base_url() . "uploads/small_pics/" . $small_pic;
    ?>  
      <li>
          <a href="<?php echo $item_page; ?>" rel="external">
            <img src="<?php echo $small_pic_path; ?>" title="<?php echo $item_title; ?>" class="img-responsive">          
            <h2><?php echo $item_title; ?></h2>
            <p><?php echo "Our Price: " . $currency_symbol . $item_price; ?></p>
          </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>