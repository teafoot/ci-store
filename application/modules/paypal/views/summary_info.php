<div class="row-fluid">				
	<div class="span12 statbox" style="background-color: #c0c0c0 !important; border: 1px #999 solid;">		
		<div class="span4">
			<img src="<?php echo base_url(); ?>assets/img/paypal-logo.png" style="width: 75%; height: 75%;">
		</div>
		<div class="span8">
			<h2>FEEDBACK FROM PAYPAL</h2>
			<p>
				<b>Transmission Time:</b> <?php echo $date_created; ?><br>
				<b>Payment Status:</b> <?php echo $payment_status; ?><br>
				<b>Transaction ID:</b> <?php echo $txn_id; ?><br>
				<b>Payment Gross:</b> <?php echo $mc_gross; ?><br>
				<b>Payer ID:</b> <?php echo $payer_id; ?><br>
				<b>Payer Email:</b> <?php echo $payer_email; ?><br>
				<b>Payer Status:</b> <?php echo $payer_status; ?><br>
				<b>Payment Date:</b> <?php echo $payment_date; ?><br>
				<!-- Payer's Details -->
				<b>Payer's Name:</b> <?php echo $first_name . " " . $last_name; ?><br>
				<b>Payer's Email:</b> <?php echo $payer_email; ?><br>
				<b>Address Line 1:</b> <?php echo $address_name; ?><br>
				<b>Address Line 2:</b> <?php echo $address_street; ?><br>
				<b>City:</b> <?php echo $address_city; ?><br>
				<b>State:</b> <?php echo $address_state; ?><br>
				<b>Postcode/Zip:</b> <?php echo $address_zip; ?><br>
				<b>Country</b> <?php echo $address_country; ?><br>
			</p>
		</div>
		<div class="footer">
			<a href="https://www.paypal.com"> check Paypal for more info.</a>
		</div>
	</div>
</div>