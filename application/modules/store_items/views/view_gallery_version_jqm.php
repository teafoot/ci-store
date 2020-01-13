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

<script type="text/javascript">
  var myApp = angular.module("myApp", []);

  myApp.controller('myController', ['$scope', function($scope) {
    $scope.defaultPic = "<?php echo base_url(); ?>/uploads/big_pics/<?php echo $big_pic; ?>";
    $scope.change = function(newPic) {
      $scope.defaultPic = newPic;
    };
  }])
</script>

<style type="text/css">
  .ui-bar {
    border: 1px solid silver;
  }
</style>

<h3 style="margin-top: 0; margin-bottom: 4px;"><?php echo $item_title; ?></h3>
<div class="ui-grid-d" ng-controller="myController">
  <?php
    $count = 0;

    foreach ($gallery_pics as $thumbnail) { 
      $count++;

      if ($count > 5) {
        $count = 1;
      }

      switch ($count) {
        case '1':
          $block_value = "a";
          break;
        case '2':
          $block_value = "b";
          break;
        case '3':
          $block_value = "c";
          break;
        case '4':
          $block_value = "d";
          break;
        case '5':
          $block_value = "e";
          break;
      }
  ?>
    <div class="ui-block-<?= $block_value ?>">
      <div class="ui-bar" style="height: 80px">          
        <img ng-click="change('<?= $thumbnail ?>')" src="<?= $thumbnail ?>" style="width: 100%;">
      </div>
    </div>
  <?php
    }
  ?>
  <img src="{{ defaultPic }}" style="width: 100%; margin-top: 24px;" alt="<?php echo $item_title; ?>">
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
