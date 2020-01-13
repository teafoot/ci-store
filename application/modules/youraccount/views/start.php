<style>
.centered-form{
	margin-top: 60px;
}
.centered-form .panel{
	background: rgba(255, 255, 255, 0.8);
	box-shadow: rgba(0, 0, 0, 0.3) 20px 20px 20px;
}
</style>
<?php
	echo validation_errors("<p style='color: red;'>", "</p>");
?>
<div class="container">
  <div class="row centered-form">
    <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
    	<div class="panel panel-default">
    		<div class="panel-heading">
    			<h1>Create Account</h1>
	    		<h3 class="panel-title">Please submit your details using the form below.</h3>
	 			</div>
	 			<div class="panel-body">
	 				<?php
						$form_location = base_url() . "youraccount/submit";
					?>
	    		<form role="form" action="<?php echo $form_location; ?>" method="post">	    			
	    			<div class="form-group">
	    				<input type="text" name="username" id="username" value="<?php echo $username; ?>" class="form-control input-sm" placeholder="Username">
	    			</div>
	    			<div class="form-group">
	    				<input type="email" name="email" id="email" value="<?php echo $email; ?>" class="form-control input-sm" placeholder="Email Address">
	    			</div>
	    			<div class="row">
	    				<div class="col-xs-6 col-sm-6 col-md-6">
	    					<div class="form-group">
	    						<input type="password" name="pword" id="pword" value="<?php echo $pword; ?>" class="form-control input-sm" placeholder="Password">
	    					</div>
	    				</div>
	    				<div class="col-xs-6 col-sm-6 col-md-6">
	    					<div class="form-group">
	    						<input type="password" name="repeat_pword" id="repeat_pword" value="<?php echo $repeat_pword; ?>" class="form-control input-sm" placeholder="Confirm Password">
	    					</div>
	    				</div>
	    			</div>	    			
	    			<input type="submit" name="submit" value="Submit" class="btn btn-info btn-block">	    		
	    			<input type="submit" name="submit" value="Cancel" class="btn btn-default btn-block">	    		
	    		</form>
	    	</div>
  		</div>
		</div>
	</div>
</div>