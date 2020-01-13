<?php
$create_msg_url = base_url() . "yourmessages/create";
?>

<a href="<?php echo $create_msg_url; ?>">
	<button style="margin-top: 30px; margin-bottom: 30px;" class="ui-btn ui-btn-inline">Compose Message</button>
</a>

<div class="ui-body ui-body-a ui-corner-all">
	<h3>Your <?php echo $folder_type; ?></h3>
	<table>
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
          $icon = '<span style="background-color: gray;" class="ui-btn ui-shadow ui-corner-all ui-icon-mail ui-btn-icon-notext">Opened</span>';
        } else {
          $icon = '<span style="background-color: gold;" class="ui-btn ui-shadow ui-corner-all ui-icon-mail ui-btn-icon-notext">Open</span>';
        }

        $date_sent = $this->timedate->get_nice_date($row->date_created, "mini");

        if ($row->sent_by == 0) {
          $sent_by = $team_name;
        } else {
          $sent_by = $this->store_accounts->_get_customer_name($row->sent_by, $customer_data);                
        }
    ?>
			<tr>
				<td><?= $icon ?></td>
				<td style="padding-left: 20px; padding-bottom: 20px;">
					<p>
						<b><a href="<?php echo $view_url; ?>"><?php echo $row->subject; ?></a></b><br>
						<?php echo $date_sent; ?><br>
						<?php echo $sent_by; ?><br>
					</p>
				</td>
			</tr>
    <?php endforeach; ?>
	</table>
</div>
