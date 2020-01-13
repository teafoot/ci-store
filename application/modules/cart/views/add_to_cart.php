<div style="background-color: #ddd; border-radius: 7px; margin-top: 24px; padding: 7px;">
<?php
	echo form_open("store_basket/add_to_basket");
?>
	<table class="table">
		<tr>
			<td colspan="2">ID: <?php echo $item_id; ?></td>
		</tr>
		<?php if ($num_colors>0) : ?>
		<tr>
			<td>Color: </td>
			<td>
	      <?php
	      $additional_dropdown_code = 'class="form-control"';
	      echo form_dropdown('item_color', $color_options, $submitted_color, $additional_dropdown_code);
	      ?>
			</td>
		</tr>
		<?php endif; ?>
		<?php if ($num_sizes>0) : ?>
		<tr>
			<td>Size: </td>
			<td>
	      <?php
	      $additional_dropdown_code = 'class="form-control"';
	      echo form_dropdown('item_size', $size_options, $submitted_size, $additional_dropdown_code);
	      ?>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<td>Qty: </td>
			<td>
				<div class="col-sm-5" style="padding-left: 0;">
					<input type="text" name="item_qty" class="form-control input-sm">
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center;">
				<button class="btn btn-primary" name="submit" value="Submit" type="submit"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Add to basket</button>
			</td>
		</tr>
	</table>
<?php
	echo form_hidden("item_id", $item_id);
	echo form_close();
?>
</div>