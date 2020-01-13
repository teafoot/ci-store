<?php 
  echo Modules::run("templates/_draw_breadcrumbs", $breadcrumbs_data); 

  if (isset($flash)) {
    echo $flash;
  }
?>

<div class="row">
  <div class="col-md-2">
    <a href="#" data-featherlight="<?php echo base_url(); ?>/uploads/big_pics/<?php echo $big_pic; ?>">
  		<img src="<?php echo base_url(); ?>/uploads/big_pics/<?php echo $big_pic; ?>" class="img-responsive" alt="<?php echo $item_title; ?>" style="margin-top: 24px;">
    </a>
  </div>
  <div class="col-md-7">
  	<h1><?php echo $item_title; ?></h1>
    <h2>Our Price: 
      <?php
        $item_price_desc = number_format($item_price, 2);
        $item_price_desc = str_replace(".00", "", $item_price_desc);
        echo $currency_symbol . $item_price_desc; 
      ?>        
    </h2>
  	<div style="clear: both;">
			<?php echo nl2br($item_description); ?>
  	</div>
  </div>
  <div class="col-md-3">
  	<?php echo Modules::run("cart/_draw_add_to_cart", $update_id); ?>
  </div>
</div>
