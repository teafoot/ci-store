<?php
  $this->load->module("homepage_offers");
  $count = 0;

  foreach ($query->result() as $row) :
    $count++;
    $block_id = $row->id;
    $num_items_on_block = $this->homepage_offers->count_where("block_id", $block_id);

    if ($count > 4) {
      $count = 1;
    }

    if ($num_items_on_block > 0) :      
?>
	<h3 class="ui-bar ui-bar-a"><?php echo $row->block_title; ?></h3>
  <?php 
  	$block_data["block_id"] = $block_id;
  	$this->homepage_offers->_draw_offers($block_data, true);
  ?>
<?php 
    endif;
  endforeach; 
?>
