<?php

class Store_basket extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }


  function add_to_basket() {

    $submit = $this->input->post("submit", true);

    if ($submit == "Submit") {
      $this->load->library("form_validation");
      $this->form_validation->set_rules("item_id", "Item ID", "required|numeric");
      $this->form_validation->set_rules("item_qty", "Item Quantity", "required|numeric");
      $this->form_validation->set_rules("item_color", "Item Color", "numeric");
      $this->form_validation->set_rules("item_size", "Item Size", "numeric");

      if ($this->form_validation->run() == true) {
        $data = $this->_fetch_the_data();
        $data = $this->_avoid_cart_conflicts($data);
        $this->_insert($data);

        redirect("cart");
      } else {
        $refer_url = $_SERVER["HTTP_REFERER"];

        $error_msg = validation_errors('<p style="color: red;">', "</p>");
        $this->session->set_flashdata("item", $error_msg);

        redirect($refer_url);
      }
    }
  }

  function _fetch_the_data() {
    $this->load->module("site_security");
    $this->load->module("store_items");

    $item_id = $this->input->post("item_id", true);
    $item_data = $this->store_items->_fetch_data_from_db($item_id);
    $item_title = $item_data["item_title"];
    $item_price = $item_data["item_price"];
    $item_qty = $this->input->post("item_qty", true);
    $item_size = $this->input->post("item_size", true);
    $item_color = $this->input->post("item_color", true);

    $shopper_id = $this->site_security->_get_user_id();

    if (!is_numeric($shopper_id)) {
      $shopper_id = 0;
    }

    $data["shopper_id"] = $shopper_id;
    $data["session_id"] = $this->session->session_id;
    $data["ip_address"] = $this->input->ip_address();
    $data["item_id"] = $item_id;
    $data["item_title"] = $item_title;
    $data["item_qty"] = $item_qty;
    $data["item_size"] = $this->_get_value("size", $item_size);
    $data["item_color"] = $this->_get_value("color", $item_color);
    $data["price"] = $item_price;
    $data["tax"] = "0";
    $data["date_added"] = time();

    return $data;
  }

  function _get_value($value_type, $update_id) {
    // Note: $value_type can be "color" or "size"
    if ($value_type == "size") {
      $this->load->module("store_item_sizes");

      $query = $this->store_item_sizes->get_where($update_id);
      foreach ($query->result() as $row) {
        $item_size = $row->size;
      }

      if (!isset($item_size)) {
        $item_size = "";
      }

      $value = $item_size;
    } else {
      $this->load->module("store_item_colors");

      $query = $this->store_item_colors->get_where($update_id);
      foreach ($query->result() as $row) {
        $item_color = $row->color;
      }

      if (!isset($item_color)) {
        $item_color = "";
      }

      $value = $item_color;
    }

    return $value;
  }

  function remove() {
    $update_id = $this->uri->segment(3);
    $allowed = $this->_make_sure_remove_allowed($update_id);

    if (!$allowed) {
      redirect("cart");
    } else {
      $this->_delete($update_id);
      redirect("cart");
    }
  }

  function _make_sure_remove_allowed($update_id) {
    $this->load->module("site_security");

    $query = $this->get_where($update_id);
    foreach ($query->result() as $row) {
      $session_id = $row->session_id;
      $shopper_id = $row->shopper_id;
    }

    if (!isset($session_id) or !isset($shopper_id)) {
      return false;
    }

    $customer_session_id = $this->session->session_id;
    $customer_shopper_id = $this->site_security->_get_user_id();

    if (($session_id == $customer_session_id) or ($shopper_id == $customer_shopper_id)) {
      return true;
    } else {
      return false;
    }

  }

  function _avoid_cart_conflicts($data) {
    /*
      Note: 
        -Make sure no items on store_shoppertrack has the same session_id and shopper_id as in store_basket
        -If there are items on store_shoppertrack with the same session_id and shopper_id as in store_basket:
          *regenerate the session_id for this user.
          *update the $data["session_id"] variable
    */

    $this->load->module("store_shoppertrack");

    $original_session_id = $data["session_id"];
    $shopper_id = $data["shopper_id"];

    $col1 = "session_id";
    $value1 = $original_session_id;
    $col2 = "shopper_id";
    $value2 = $shopper_id;

    $query = $this->store_shoppertrack->get_with_double_condition($col1, $value1, $col2, $value2);
    $num_rows = $query->num_rows();

    if ($num_rows > 0) { // items conflicting with store_shoppertrack
      session_regenerate_id();

      $new_session_id = $this->session->session_id;
      $data["session_id"] = $new_session_id;
    }

    return $data;
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_store_basket");
    $query = $this->mdl_store_basket->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_store_basket");
    $query = $this->mdl_store_basket->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_store_basket");
    $query = $this->mdl_store_basket->get_where($id);

    return $query;
  }

  public function get_with_double_condition($col1, $value1, $col2, $value2) {
    $this->load->model("mdl_store_basket");
    $query = $this->mdl_store_basket->get_with_double_condition($col1, $value1, $col2, $value2);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_store_basket");
    $query = $this->mdl_store_basket->get_where_custom($col, $value);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_store_basket");
    $this->mdl_store_basket->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_store_basket");
    $this->mdl_store_basket->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_store_basket");
    $this->mdl_store_basket->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_store_basket");
    $count = $this->mdl_store_basket->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_store_basket");
    $max_id = $this->mdl_store_basket->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_store_basket");
    $query = $this->mdl_store_basket->_custom_query($mysql_query);

    return $query;
  }

  function test() {
    ob_start();
    $this->load->module("site_security");

    $session_id = $this->session->session_id;
    echo $session_id . "<hr>";
    $shopper_id = $this->site_security->_get_user_id();
    echo "Your are shopper: $shopper_id <hr>";

    session_regenerate_id();
    echo "<h1>New Session ID has been generated.</h1>";

    $session_id = $this->session->session_id;
    echo $session_id . "<hr>";    
    $shopper_id = $this->site_security->_get_user_id();
    echo "Your are shopper: $shopper_id <hr>";
  }

  function autogen() {
    $mysql_query = "show columns from store_basket";
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
