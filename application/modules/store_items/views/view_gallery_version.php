<?php 
  echo Modules::run("templates/_draw_breadcrumbs", $breadcrumbs_data); 

  if (isset($flash)) {
    echo $flash;
  }
?>

<script type="text/javascript">
	var myApp = angular.module("myApp", []);

	myApp.controller('myController', ['$scope', function($scope) {
		// $scope.myName = "David";
		$scope.defaultPic = "<?php echo base_url(); ?>/uploads/big_pics/<?php echo $big_pic; ?>";
		$scope.change = function(newPic) {
			$scope.defaultPic = newPic;
		};
	}])
</script>

<div class="row" ng-controller="myController">
  <div class="col-md-1">
    <?php	foreach ($gallery_pics as $thumbnail) : ?>    		
    	<img ng-click="change('<?= $thumbnail ?>')" src="<?= $thumbnail ?>" style="width: 100px;">
    <?php endforeach; ?>
  </div>
  <div class="col-md-4">
    <a href="#" data-featherlight="{{ defaultPic }}">
  		<img src="{{ defaultPic }}" class="img-responsive" alt="<?php echo $item_title; ?>" style="margin-top: 24px;">
    </a>
  </div>
  <div class="col-md-4">
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
