<h1>Manage Homepage Offer Blocks</h1>

<?php
if (isset($flash)) {
  echo $flash;
}

$create_homepage_blocks_url = base_url() . "homepage_blocks/create";
?>
<a href="<?php echo $create_homepage_blocks_url; ?>">
  <button type="button" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 30px;">Create new homepage offer block</button>
</a>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="white icon-align-justify"></i><span class="break"></span>Existing Homepage Offer Blocks</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <?php
        echo Modules::run("homepage_blocks/_draw_sortable_list");
      ?>
    </div>
  </div><!--/span-->

</div><!--/row-->