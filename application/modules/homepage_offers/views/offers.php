<?php 
foreach ($query->result() as $row) : 
  $item_page = base_url() . $item_segments . $row->item_url;

  $item_title = $row->item_title;
  $item_price = number_format($row->item_price, 2);
  $was_price = $row->was_price;

  $small_pic = $row->small_pic;
  $small_pic_path = base_url() . "uploads/small_pics/" . $small_pic;
?>
  <div class="col-xs-3">
    <div class="offer offer-<?php echo $theme; ?>" style="min-height: 400px;">
      <div class="shape">
        <div class="shape-text">
          <span class="glyphicon glyphicon-star" aria-hidden="true" style="font-size: 1.4em;"></span>               
        </div>
      </div>
      <div class="offer-content">
        <h3 class="lead">
          <?php echo "Our Price: " . $currency_symbol . $item_price; ?>
        </h3>
        <a href="<?php echo $item_page; ?>">
          <img src="<?php echo $small_pic_path; ?>" title="<?php echo $item_title; ?>" class="img-responsive">
        </a>    
        <p>
          <a href="<?php echo $item_page; ?>"><?php echo $item_title; ?></a>
        </p>
      </div>
    </div>
  </div>
<?php endforeach; ?>