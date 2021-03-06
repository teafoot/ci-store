<h1>Manage Bottom Navigation</h1>

<?php
if (isset($flash)) {
  echo $flash;
}

$create_bottom_nav_url = base_url() . "bottom_nav/create";

echo Modules::run("bottom_nav/_draw_create_modal");
?>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="white icon-align-justify"></i><span class="break"></span>Existing Bottom Navigation Links</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <?php
        echo Modules::run("bottom_nav/_draw_sortable_list");
      ?>
    </div>
  </div><!--/span-->

</div><!--/row-->