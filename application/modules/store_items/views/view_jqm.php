<?php 
  // echo Modules::run("templates/_draw_breadcrumbs", $breadcrumbs_data); 

  if (isset($flash)) {
    echo $flash;
  }
?>

<!-- Solution 1: without the link that triggers this page with rel="external" -->
<!-- 
<?php if (isset($use_angularjs)) : ?>
  <script type="text/javascript" src="<?php echo base_url() . "assets/vendor/angular/angular.min.js" ?>"></script>
<?php endif; ?> 
-->

<style type="text/css">
  .ui-bar {
    border: 1px solid silver;
  }
</style>

<h3 style="margin-top: 0; margin-bottom: 4px;"><?php echo $item_title; ?></h3>
<img src="<?php echo base_url(); ?>/uploads/big_pics/<?php echo $big_pic; ?>" style="width: 100%;">
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
