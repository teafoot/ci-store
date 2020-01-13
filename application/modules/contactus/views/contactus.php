<?php
    $_SESSION['start'] = time();

    if (!isset($_SESSION['antispam_token'])) {
      $this->load->module("site_security");
      $token = $this->site_security->generate_random_string(10);
      $_SESSION['antispam_token'] = $token;
    }
?>

<style type="text/css">
/* For Form */
.jumbotron {
  background: #358CCE;
  color: #FFF;
  border-radius: 0px;
}
.jumbotron-sm { 
  padding-top: 24px;
  padding-bottom: 24px; 
}
.jumbotron small {
  color: #FFF;
}
.h1 small {
  font-size: 24px;
}
/* For Map */
.map-responsive{
    overflow:hidden;
    padding-bottom: 25%;
    position:relative;
    height:0;
}
.map-responsive iframe{
    left:0;
    top:0;
    height:100%;
    width:100%;
    position:absolute;
}
</style>

<?php
  echo validation_errors('<p style="color: red;">', "</p>");
?>

<div class="row">
  <div class="col-md-12">
  	<h1>Contact Us</h1>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="well well-sm">
                    <form action="<?php echo $form_location; ?>" method="post">
                    <div class="row">
                        <div class="col-md-6">                            
                            <input type="text" id="firstname" name="firstname" value="<?php echo $_SESSION['antispam_token']; ?>" style="display:none !important" tabindex="-1" autocomplete="off" />
                            <div class="form-group">
                                <label for="yourname">Name</label>
                                <input type="text" class="form-control" id="yourname" name="yourname" value="<?php echo $yourname; ?>" placeholder="Enter name" required="required" />
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" placeholder="Enter email" required="required" /></div>
                            </div>
                            <div class="form-group">
                                <label for="telnum">Telephone Number</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-phone"></span>
                                    </span>
                                    <input type="text" class="form-control" id="telnum" name="telnum" value="<?php echo $telnum; ?>" placeholder="Enter telephone number" required="required" /></div>
                            </div>                            
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="message">
                                    Message</label>
                                <textarea id="message" name="message" class="form-control" rows="9" cols="25" required="required" placeholder="Message"><?php echo $message; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" name="submit" value="Submit" class="btn btn-primary pull-right" id="submit">
                                Send Message</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <form>
                <legend><span class="glyphicon glyphicon-globe"></span> Our office</legend>
                <address>
                    <strong><?php echo $our_name; ?></strong><br>
                    <?php echo $our_address; ?>
                </address>
                <address>
                    <strong>Telephone</strong><br>
                    <?php echo $our_telnum; ?>
                </address>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
              <div class="map-responsive">
                <?php echo $map_code; ?>
              </div>
            </div>
        </div>
  	</div>
  </div>
</div>