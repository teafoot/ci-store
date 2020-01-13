<h1>Manage Orders</h1>
<h2><?php echo $current_order_status; ?></h2>

<?php
if (isset($flash)) {
  echo $flash;
}

function get_customer_name($firstname, $lastname, $company) {
  $firstname = trim(ucfirst($firstname));
  $lastname = trim(ucfirst($lastname));
  $company = trim(ucfirst($company));

  $customer_name = $firstname . " " . $lastname;
  $company_length = strlen($company);

  if ($company_length > 2) {
    $customer_name .= " from " . $company;
  }

  return $customer_name;
}

$paypal_url = "https://www.paypal.com";
?>
<a href="<?php echo $paypal_url; ?>">
  <button type="button" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 30px;">Visit Paypal</button>
</a>

<?php if ($num_rows < 1) : ?>
  <p>There are currently no orders with this order status.</p>
<?php else : ?>
  <?php
    echo "<p>" . $showing_statement . "</p>";
    echo $pagination;
  ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white tag"></i><span class="break"></span>Your Orders</h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>
      <div class="box-content">
        <table class="table table-striped table-bordered bootstrap-datatable datatable">
          <thead>
            <tr>
              <th>Order Ref</th>
              <th>Order Value</th>
              <th>Date Created</th>
              <th>Customer Name</th>
              <th>Order Status</th>
              <th>Opened</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $this->load->module("timedate");            

              foreach ($query->result() as $row) :
                $view_order_url = base_url() . "store_orders/view/" . $row->id;

                $opened = $row->opened;
                $opened_label = ($opened == 1) ? "success" : "important";
                $opened_description = ($opened == 1) ? "Opened" : "Unopened";

                $date_created = $this->timedate->get_nice_date($row->date_created, "full");

                $firstname = $row->firstname;
                $lastname = $row->lastname;
                $company = $row->company;
                $customer_name = get_customer_name($firstname, $lastname, $company);

                if (isset($row->status_title)) {
                  $order_status = $row->status_title;
                } else {
                  $order_status = "Order Submitted"; // when $order_status=0
                }
            ?>
              <tr>
                <td><?php echo $row->order_ref; ?></td>
                <td><?php echo $row->mc_gross; ?></td>
                <td><?php echo $date_created; ?></td>
                <td class="center"><?php echo $customer_name; ?></td>
                <td class="center"><?php echo $order_status; ?></td>
                <td class="center">
                  <span class="label label-<?php echo $opened_label; ?>"><?php echo $opened_description; ?></span>
                </td>
                <td class="center">
                  <a class="btn btn-success" href="<?php echo $view_order_url; ?>">
                    <i class="halflings-icon white zoom-in"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div><!--/span-->
  </div><!--/row-->
  <?php echo $pagination; ?>
<?php endif; ?>