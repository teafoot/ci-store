<h1>Your <?php echo $folder_type; ?></h1>

<?php
if (isset($flash)) {
  echo $flash;
}

$create_msg_url = base_url() . "yourmessages/create";
?>

<a href="<?php echo $create_msg_url; ?>">
  <button type="button" class="btn btn-primary" style="margin-top: 30px; margin-bottom: 30px;">Compose Message</button>
</a>

<table class="table table-striped table-bordered bootstrap-datatable datatable">
  <thead>
    <tr style="background-color: #666; color: #fff;">
      <th>&nbsp;</th>
      <th>Date Sent</th>
      <th>Sent By</th>
      <th>Subject</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    	$this->load->module("site_settings");
      $this->load->module("timedate");
      $this->load->module("store_accounts");

      $team_name = $this->site_settings->_get_support_team_name();

      foreach ($query->result() as $row) :
        $view_url = base_url() . "yourmessages/view/" . $row->code;

        $customer_data["firstname"] = $row->firstname;
        $customer_data["lastname"] = $row->lastname;
        $customer_data["company"] = $row->company;
        $opened = $row->opened;

        if ($opened==1) {
          $icon = '<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>';
        } else {
          $icon = '<span class="glyphicon glyphicon-envelope" aria-hidden="true" style="color: orange;"></span>';
        }

        $date_sent = $this->timedate->get_nice_date($row->date_created, "mini");

        if ($row->sent_by == 0) {
          $sent_by = $team_name;
        } else {
          $sent_by = $this->store_accounts->_get_customer_name($row->sent_by, $customer_data);                
        }
    ?>
      <tr>
        <td class="span1"><?php echo $icon; ?></td>
        <td><?php echo $date_sent; ?></td>
        <td><?php echo $sent_by; ?></td>
        <td><?php echo $row->subject; ?></td>
        <td class="span1">
          <a class="btn btn-default" href="<?php echo $view_url; ?>">
            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>