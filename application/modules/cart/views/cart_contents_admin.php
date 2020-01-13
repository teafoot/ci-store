<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white shopping-cart"></i><span class="break"></span>Customer's Shopping Basket Contents</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
			<table class="table table-bordered">
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
  </div><!--/span-->
</div><!--/row-->