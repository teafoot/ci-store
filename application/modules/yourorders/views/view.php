<h1>Order: <?php echo $order_ref; ?></h1>
<p style="font-weight: bold;">Date Created: <?php echo $date_created; ?></p>
<p style="font-weight: bold;">Order Status: <?php echo $order_status_title; ?></p>

<?php
  $user_type = "public";
  echo Modules::run("cart/_draw_cart_contents", $query_cart_contents, $user_type);
?>