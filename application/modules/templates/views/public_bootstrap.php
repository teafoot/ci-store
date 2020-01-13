<!DOCTYPE html>
<html lang="en" <?php if (isset($use_angularjs)) {
  echo 'ng-app="myApp"';
} ?>>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo base_url(); ?>/assets/vendor/bootstrap-3.3.6/docs/favicon.ico">

    <title>Jumbotron Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo base_url(); ?>/assets/vendor/bootstrap-3.3.6/docs/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="<?php echo base_url(); ?>/assets/vendor/bootstrap-3.3.6/docs/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>/assets/vendor/bootstrap-3.3.6/docs/examples/jumbotron/jumbotron.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="<?php echo base_url(); ?>/assets/vendor/bootstrap-3.3.6/docs/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="<?php echo base_url(); ?>/assets/vendor/bootstrap-3.3.6/docs/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php if (isset($use_featherlight)) : ?>
      <link href="<?php echo base_url(); ?>/assets/vendor/featherlight/featherlight.min.css" type="text/css" rel="stylesheet" />
    <?php endif; ?>

    <?php if (isset($use_angularjs)) : ?>
      <script type="text/javascript" src="<?= base_url() . "assets/vendor/angular/angular.min.js" ?>"></script>
    <?php endif; ?>
  </head>

  <body>

    <div class="container-fluid dctop">
      <div class="container" style="height: 100px;">
        <div class="row">
          <?= Modules::run("templates/_draw_page_top") ?>
        </div>
      </div>
    </div>

    <nav class="navbar navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo base_url(); ?>">Home</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <?php
          echo Modules::run("store_categories/_draw_top_nav");
          ?>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <div class="container roundbtm" style="background-color: #fff">
      <div id="stage">
        <?php
        echo Modules::run("sliders/_attempt_draw_slider");

        if ($customer_id > 0) {
          include("customer_panel_top.php");
        }

        if (isset($page_content)) {
          echo nl2br($page_content);
          
          // Special pages content
          if (isset($page_url) && $page_url == "") {
            require_once("content_homepage.php");
          } else if (isset($page_url) && $page_url == "contactus") {
            echo Modules::run("contactus/_draw_form");
          }
        } else if (isset($view_file)) {
          $this->load->view($view_module . '/' . $view_file);
        }      
        ?>    
    </div> <!-- /stage -->
    <hr>
    <div class="container">
      <footer>
        <?= Modules::run("bottom_nav/_draw_bottom_nav") ?>
        <p>&copy; 2015 Company, Inc.</p>
      </footer>
    </div>      
  </div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?php echo base_url(); ?>/assets/vendor/bootstrap-3.3.6/docs/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="<?php echo base_url(); ?>/assets/vendor/bootstrap-3.3.6/docs/dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo base_url(); ?>/assets/vendor/bootstrap-3.3.6/docs/assets/js/ie10-viewport-bug-workaround.js"></script>
    <?php if (isset($use_featherlight)) : ?>
      <script src="<?php echo base_url(); ?>/assets/vendor/featherlight/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
    <?php endif; ?>
  </body>
</html>
