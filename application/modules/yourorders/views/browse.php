<h1>Your Orders</h1>

<?php	if ($num_rows < 1) : ?>
	<p>You have not placed any orders so far.</p>
<?php	else : ?>
  <?php echo $showing_statement; ?>
  <?php echo $pagination; ?>
	<table class="table table-striped table-bordered bootstrap-datatable datatable">
	  <thead>
	    <tr style="background-color: #666; color: #fff;">
	      <th>Order Ref</th>
	      <th>Order Value</th>
	      <th>Date Created</th>
	      <th>Order Status</th>
	      <th>Actions</th>
	    </tr>
	  </thead>
	  <tbody>
	    <?php
	      $this->load->module("timedate");            

	      foreach ($query->result() as $row) :
	        $view_order_url = base_url() . "yourorders/view/" . $row->order_ref;

	        $date_created = $this->timedate->get_nice_date($row->date_created, "cool");

	        $order_status = $row->order_status;
	        $order_status_title = $order_status_options[$order_status];
	    ?>
	      <tr>
	        <td><?php echo $row->order_ref; ?></td>
	        <td><?php echo $row->mc_gross; ?></td>
	        <td><?php echo $date_created; ?></td>
	        <td class="center"><?php echo $order_status_title; ?></td>        
	        <td class="center">
	          <a class="btn btn-default" href="<?php echo $view_order_url; ?>">
	            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View
	          </a>
	        </td>
	      </tr>
	    <?php endforeach; ?>
	  </tbody>
	</table>
	<?php echo $pagination; ?>
<?php endif; ?>