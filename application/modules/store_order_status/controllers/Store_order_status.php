<?php

class Store_order_status extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

   function manage() {
    $this->load->library("session");
    $this->load->module("site_security");

    $this->site_security->_make_sure_is_admin();

    $data["flash"] = $this->session->flashdata("item");
    $data["query"] = $this->get("status_title");
    $data["view_file"] = "manage";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function create() {
    $this->load->module("site_security");
    $this->load->library("session");

    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post("submit", true);

    // POST Request from form
    if ($submit == "Submit") {
      $this->load->library("form_validation");
      $this->form_validation->set_rules("status_title", "Status Title", "required");

      if ($this->form_validation->run() == true) {
        $data = $this->_fetch_data_from_post();

        // Update/Insert to DB
        if (is_numeric($update_id)) {
          $this->_update($update_id, $data);
          $flash_msg = "The status title was successfully updated.";
        } else {
          $this->_insert($data);
          $update_id = $this->get_max(); // get the id of the new account.
          $flash_msg = "The status title was successfully added.";
        }

        $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
        $this->session->set_flashdata("item", $value);
        redirect("store_order_status/create/" . $update_id);
      }
    } else if ($submit == "Cancel") {
      redirect("store_order_status/manage");
    }

    // GET Request from URL
    if (is_numeric($update_id) && ($submit != "Submit")) {
      $data = $this->_fetch_data_from_db($update_id);
    } else {
      $data = $this->_fetch_data_from_post();
    }

    if (is_numeric($update_id)) {
      $data["headline"] = "Update order status option";
    } else {
      $data["headline"] = "Add new order status option";
    }
    $data["flash"] = $this->session->flashdata("item");
    $data["update_id"] = $update_id;
    $data["view_file"] = "create";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _fetch_data_from_post() {
    $data["status_title"] = $this->input->post("status_title", true);

    return $data;
  }

  function _fetch_data_from_db($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $query = $this->get_where($update_id);

    foreach ($query->result() as $row) {
      $data["status_title"] = $row->status_title;
    }

    if (!isset($data)) {
      $data = "";
    }

    return $data;
  }

  function deleteconf($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $data["headline"] = "Delete order status option";
    $data["update_id"] = $update_id;
    $data["view_file"] = "deleteconf";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function delete($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->library("session");
    $this->load->module("site_security");

    $this->site_security->_make_sure_is_admin();

    $submit = $this->input->post("submit", true);

    if ($submit == "Delete") {
      $allowed = $this->_make_sure_delete_allowed($update_id);

      if (!$allowed) {
        $flash_msg = "You're not allowed to delete this status option (there is at least one order with this status).";
        $value = '<div class="alert alert-danger" role="alert">' . $flash_msg . '</div>';
        $this->session->set_flashdata("item", $value);

        redirect("store_accounts/manage");        
      }

      $this->_process_delete($update_id);

      $flash_msg = "The order status option was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
      $this->session->set_flashdata("item", $value);

      redirect("store_order_status/manage");
    } else if ($submit == "Cancel") {
      redirect("store_order_status/create/" . $update_id);
    }
  }

  function _make_sure_delete_allowed($update_id) {
    // do not allow if order has this status
    $mysql_query = "select * from store_orders where order_status=$update_id";
    $query = $this->_custom_query($mysql_query);
    $num_rows = $query->num_rows();

    if ($num_rows > 0) {
      return false;
    } else {
      return true;
    }
  }

  function _process_delete($update_id) {
    $this->_delete($update_id);
  }

  function _draw_left_nav_links() {
    $data["query_dlnl"] = $this->get("status_title");
    $this->load->view("left_nav_links", $data);
  }

  function _get_status_title($update_id) {
    $query = $this->get_where($update_id);
    foreach ($query->result() as $row) {
      $status_title = $row->status_title;
    }

    if (!isset($status_title)) {
      $status_title = "Unknown";
    }

    return $status_title;
  }

  function _get_dropdown_options() {
    $options["0"] = "Order Submitted";

    $query = $this->get("status_title");
    foreach ($query->result() as $row) {
      $options[$row->id] = $row->status_title;
    }

    if (!isset($options)) {
      $options = "";
    }

    return $options;
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_store_order_status");
    $query = $this->mdl_store_order_status->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_store_order_status");
    $query = $this->mdl_store_order_status->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_store_order_status");
    $query = $this->mdl_store_order_status->get_where($id);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_store_order_status");
    $query = $this->mdl_store_order_status->get_where_custom($col, $value);

    return $query;
  }

  public function get_with_double_condition($col1, $value1, $col2, $value2) {
    $this->load->model("mdl_store_order_status");
    $query = $this->mdl_store_order_status->get_with_double_condition($col1, $value1, $col2, $value2);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_store_order_status");
    $this->mdl_store_order_status->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_store_order_status");
    $this->mdl_store_order_status->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_store_order_status");
    $this->mdl_store_order_status->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_store_order_status");
    $count = $this->mdl_store_order_status->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_store_order_status");
    $max_id = $this->mdl_store_order_status->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_store_order_status");
    $query = $this->mdl_store_order_status->_custom_query($mysql_query);

    return $query;
  }

  function autogen() {
    $mysql_query = "show columns from store_order_status";
    $query = $this->_custom_query($mysql_query);

    // fetch data from posts
    foreach ($query->result() as $row) {
      $column_name = $row->Field;

      if ($column_name != "id") {
        echo '$data["' . $column_name . '"] = $this->input->post("' . $column_name . '", true);' . "<br>";
      }
    }

    echo "<hr>";

    // validate posts data
    foreach ($query->result() as $row) {
      $column_name = $row->Field;

      if ($column_name != "id") {
        echo '$this->form_validation->set_rules("' . $column_name . '", "' . ucfirst($column_name) . '", "required");' . "<br>";
      }
    }

    echo "<hr>";

    // fetch data from db
    foreach ($query->result() as $row) {
      $column_name = $row->Field;

      if ($column_name != "id") {
        echo '$data["' . $column_name . '"] = $row->' . $column_name . ';' . "<br>";
      }
    }

    echo "<hr>";

    // post input fields
    foreach ($query->result() as $row) {
      $column_name = $row->Field;

      if ($column_name != "id") {
        $var = '<div class="control-group">
                  <label class="control-label" for="' . $column_name . '">' . ucfirst($column_name) . '</label>
                  <div class="controls">
                    <input type="text" class="span6" name="' . $column_name . '" value="<?php echo $' .  $column_name . '; ?>">
                  </div>
                </div>';
        echo htmlentities($var) . "<br>";
      }
    }
  }

}
