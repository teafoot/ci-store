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
    $this->load->module("homepage_offers");

    foreach ($query->result() as $row) : 
      $edit_item_url = base_url() . "homepage_blocks/create/" . $row->id;
  ?>
    <li class="sort" id="<?php echo $row->id; ?>">
      <i class="icon-sort"></i> <?php echo $row->block_title; ?>
      <?php
        $num_items = $this->homepage_offers->count_where("block_id", $row->id);

        if ($num_items > 0) {
          if ($num_items == 1) {
            $entity = "Homepage Offer Item";
          } else {
            $entity = "Homepage Offer Items";
          }
          echo '<a class="btn btn-default" href="' . base_url() . '"><i class="halflings-icon white zoom-in"></i> ' . $num_items . ' ' . $entity . '</a>';
        }
      ?>
      <a class="btn btn-info" href="<?php echo $edit_item_url; ?>">
        <i class="halflings-icon white edit"></i>
      </a>
    </li>
  <?php endforeach; ?>
</ul>