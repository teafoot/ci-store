<h1><?php echo $headline; ?></h1>
<?php
echo validation_errors('<p style="color: red;">', "</p>");

if (isset($flash)) {
  echo $flash;
}
?>

<p style="text-align: right;">
  <a href="<?= base_url() ?>invoices/test">
    <button class="btn btn-success">View Invoice</button>
  </a>
</p>

<?php
echo Modules::run("paypal/_display_summary_info", $paypal_id);
?>

<!-- Only on update, not create. -->
<?php if (is_numeric($update_id)) : ?>
  <div class="row-fluid sortable">
    <div class="box span12">
      <div class="box-header" data-original-title>
        <h2><i class="halflings-icon white edit"></i><span class="break"></span>Order Status: <?php echo $status_title; ?></h2>
        <div class="box-icon">
          <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
          <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
        </div>
      </div>
      <div class="box-content">
        <p>To update the order status please choose an option from the dropdown below and then hit "Submit".</p>
        <?php
          $form_location = base_url() . "store_orders/submit_order_status/" . $update_id;
        ?>
        <form class="form-horizontal" method="post" action="<?php echo $form_location; ?>">
          <fieldset>
            <div class="control-group">
              <label class="control-label" for="status">Order Status</label>
              <div class="controls">
                <?php
                echo form_dropdown('order_status', $options, $order_status);
                ?>
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary" name="submit" value="Submit">Save changes</button>
              <button type="submit" class="btn" name="submit" value="Cancel">Cancel</button>
            </div>
          </fieldset>
        </form>
      </div>
    </div><!--/span-->
  </div><!--/row-->
<?php endif; ?>

<div class="row-fluid sortable">
  <div class="box span12">
    <div class="box-header" data-original-title>
      <h2><i class="halflings-icon white edit"></i><span class="break"></span>Customer Details</h2>
      <div class="box-icon">
        <a href="#" class="btn-minimize"><i class="halflings-icon white chevron-up"></i></a>
        <a href="#" class="btn-close"><i class="halflings-icon white remove"></i></a>
      </div>
    </div>
    <div class="box-content">
      <p style="text-align: right;">
        <a href="<?php echo base_url(); ?>store_accounts/create/<?php echo $shopper_id; ?>">
          <button type="button" class="btn btn-info">Edit Account Details</button>
        </a>
      </p>
      <table class="table table-striped table-bordered">
        <tr>
          <td class="span3">First Name</td>
          <td><?php echo $store_accounts_data["firstname"]; ?></td>
        </tr>
        <tr>
          <td>Last Name</td>
          <td><?php echo $store_accounts_data["lastname"]; ?></td>
        </tr>
        <tr>
          <td>Company</td>
          <td><?php echo $store_accounts_data["company"]; ?></td>
        </tr>
        <tr>
          <td>Telephone Number</td>
          <td><?php echo $store_accounts_data["telnum"]; ?></td>
        </tr>
        <tr>
          <td>Email Address</td>
          <td><?php echo $store_accounts_data["email"]; ?></td>
        </tr>
        <tr>
          <td style="vertical-align: top;">Customer Address</td>
          <td style="vertical-align: top;"><?php echo $customer_address; ?></td>
        </tr>
      </table>     
    </div>
  </div><!--/span-->
</div><!--/row-->

<?php
  $user_type = "admin";
  echo Modules::run("cart/_draw_cart_contents", $query_cart_contents, $user_type);
?>