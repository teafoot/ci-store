<?php
	$first_bit = $this->uri->segment(1);
?>
<div class="row">
  <div class="col-md-10 col-md-offset-1">	
		<table class="table table-striped table-bordered" style="margin-top: 36px;">
			<?php 
				$grand_total = 0;

				foreach ($query->result() as $row) : 
					$sub_total = $row->price * $row->item_qty;
					$sub_total_desc = number_format($sub_total, 2);
					$grand_total += $sub_total;
			?>
				<tr style="text-align: center;">
					<td class="col-md-2">
						<?php if ($row->small_pic != "") : ?>
							<img src="<?php echo base_url(); ?>uploads/small_pics/<?php echo $row->small_pic; ?>">						
						<?php else : echo "No image preview available."; ?>
						<?php endif; ?>												
					</td>
					<td class="col-md-8">
						<b><?php echo $row->item_title; ?></b><br><br>
						Item Number: <?php echo $row->item_id; ?><br>
						Item Price: <?php echo $currency_symbol . $row->price; ?><br>
						Item Quantity: <?php echo $row->item_qty; ?><br><br>
						<?php 
							if ($first_bit != "yourorders") {
								echo anchor("store_basket/remove/" . $row->id, "Remove"); 
							}
						?>
					</td>
					<td class="col-md-2">
						<?php echo $currency_symbol . $sub_total_desc; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr style="font-weight: bold;">
				<td colspan="2" style="text-align: right;">Shipping</td>
				<td>
					<?php 
						$shipping_desc = number_format($shipping, 2);
						echo $currency_symbol . $shipping_desc;
						$grand_total += $shipping;
					?>
				</td>
			</tr>
			<tr style="font-weight: bold;">
				<td colspan="2" style="text-align: right;">Grand Total</td>
				<td>
					<?php 
						$grand_total_desc = number_format($grand_total, 2);
						echo $currency_symbol . $grand_total_desc; 
					?>					
				</td>
			</tr>
		</table>
	</div>
</div>